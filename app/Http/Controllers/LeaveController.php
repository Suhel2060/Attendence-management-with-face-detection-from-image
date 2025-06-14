<?php

namespace App\Http\Controllers;

use App\Models\Leaves;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $employeeId = Auth::user()->employee_id;
        
        // Get all leave requests for the employee
        $leaveRequests = Leaves::where('employee_id', $employeeId)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get leave balances for each type
        $leaveTypes = LeaveType::all();
        $leaveBalances = [];
        
        foreach ($leaveTypes as $type) {
            // Calculate taken days (approved and pending)
            $takenDays = Leaves::where('employee_id', $employeeId)
                ->where('leave_type_id', $type->id)
                ->whereIn('status', [Leaves::STATUS_APPROVED, Leaves::STATUS_PENDING])
                ->sum('total_days');
                
            $remaining = max(0, $type->max_days - $takenDays);
            
            $leaveBalances[] = (object)[
                'id' => $type->id,
                'name' => $type->name,
                'max_days' => $type->max_days,
                'taken_days' => $takenDays,
                'remaining' => $remaining,
                'color' => $this->getColorForLeaveType($type->id)
            ];
        }
        
        return view('employee_leave', compact('leaveRequests', 'leaveBalances'));
    }
    
    private function getColorForLeaveType($typeId)
    {
        // Assign colors based on leave type ID
        $colors = [
            1 => '#20c997', // Annual
            2 => '#6f42c1', // Sick
            3 => '#fd7e14', // Maternity
            4 => '#0dcaf0', // Paternity
            5 => '#ffc107', // Unpaid
        ];
        
        return $colors[$typeId] ?? '#6c757d'; // Default gray
    }
    public function create()
    {
        $leaveTypes = LeaveType::all();
        $employee = Auth::user();
        
        $balances = [];
        foreach ($leaveTypes as $type) {
            $leavesTaken = Leaves::where('employee_id', $employee->employee_id)
                ->where('leave_type_id', $type->id)
                ->where('status', Leaves::STATUS_APPROVED) // Only count approved leaves
                ->whereYear('start_date', now()->year)
                ->sum('total_days');
                
            $balances[$type->id] = [
                'taken' => $leavesTaken,
                'remaining' => max(0, $type->max_days - $leavesTaken),
                'max' => $type->max_days
            ];
        }

        return view('leaves.create', compact('leaveTypes', 'balances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $employee = Auth::user();
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        // Calculate total days
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $totalDays = $end->diffInDays($start) + 1;

        // Validate same year
        if ($start->year !== $end->year) {
            return back()->withErrors([
                'end_date' => 'Leave must be within the same calendar year'
            ])->withInput();
        }

        // Check leave balance (only approved leaves count)
        $leavesTaken = Leaves::where('employee_id', $employee->employee_id)
            ->where('leave_type_id', $leaveType->id)
            ->where('status', Leaves::STATUS_APPROVED)
            ->whereYear('start_date', $start->year)
            ->sum('total_days');

        $remainingDays = $leaveType->max_days - $leavesTaken;

        if ($totalDays > $remainingDays) {
            return back()->withErrors([
                'balance' => "Insufficient leave balance! You only have $remainingDays days left for $leaveType->name this year"
            ])->withInput();
        }

        // Create leave with pending status
        Leaves::create([
            'employee_id' => $employee->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => Leaves::STATUS_PENDING, // Default status
        ]);

        return redirect()->route('leaves.create')
            ->with('success', 'Leave request submitted successfully! Waiting for HR approval.');
    }
}