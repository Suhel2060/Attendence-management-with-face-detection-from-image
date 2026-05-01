<?php

namespace App\Http\Controllers;

use App\Models\Leaves;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    // Validate form data including face_image base64 string
    $data = $request->validate([
        'leave_type_id' => 'required|exists:leave_types,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string|max:500',
        'face_image' => 'required|string',
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
    // dd($employee->employee_id);

    // Process base64 face image
    $imageData = $data['face_image'];
    if (!str_contains($imageData, ';base64,')) {
        return back()->withErrors(['face_image' => 'Invalid image format'])->withInput();
    }

    [$type, $base64Data] = explode(';', $imageData);
    [, $base64Data] = explode(',', $base64Data);
    $imageBinary = base64_decode($base64Data);

    if ($imageBinary === false) {
        return back()->withErrors(['face_image' => 'Invalid base64 image data'])->withInput();
    }

    $extension = strpos($type, 'jpeg') !== false ? 'jpg' : 'png';
    $filename =  $employee->employee_id . '.' . $extension;
    $path = 'attendance_photos/' . $employee->employee_id.rand(1,1000000) . '/' . $filename;


    Storage::disk('public')->put($path, $imageBinary);

    $imagePath = public_path('storage/' . $path);
    $imagePath = str_replace('/', '\\', $imagePath);

    $pythonScript = 'F:\Attendance Management System\face_detection_python\recognize-version2.py';

    $command = sprintf(
        'python "%s" recognize "%s" 2>&1',
        $pythonScript,
        $imagePath
    );

    exec($command, $output, $returnVar);

    $outputString = implode("\n", $output);

    if ($returnVar !== 0) {
        return back()->withErrors([
            'face_image' => 'Error processing face recognition. Please try again.'
        ])->withInput();
    }

    $response = json_decode($outputString, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return back()->withErrors([
            'face_image' => 'Invalid response from face recognition service.'
        ])->withInput();
    }
    if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
    }
    if (isset($response['success']) && $response['success'] == true) {
        $recognizedEmpId = $response['results'][0]['emp_id'] ?? null;
        
        // dd($employee->employee_id,$recognizedEmpId);
        if ($employee->employee_id == $recognizedEmpId) {
            // dd($employee->employee_id);
            // Create leave with pending status
            Leaves::create([
                'employee_id' => $employee->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => Leaves::STATUS_PENDING,
            ]);

            return redirect()->route('leaves.create')
                ->with('success', 'Leave request submitted successfully! Waiting for HR approval.');
        } else {
            return back()->withErrors([
                'face_image' => 'Face does not match your registered identity.'
            ])->withInput();
        }
    } else {
        return back()->withErrors([
            'face_image' => 'Face recognition failed or not authorized.'
        ])->withInput();

    }
}


    // public function store(Request $request)
    // {
    //    $data = $request->validate([
    //         'leave_type_id' => 'required|exists:leave_types,id',
    //         'start_date' => 'required|date|after_or_equal:today',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //         'reason' => 'required|string|max:500',
    //         'face_image' => 'required|string',
    //     ]);

    //     $employee = Auth::user();
    //     $leaveType = LeaveType::findOrFail($request->leave_type_id);

    //     // Calculate total days
    //     $start = Carbon::parse($request->start_date);
    //     $end = Carbon::parse($request->end_date);
    //     $totalDays = $end->diffInDays($start) + 1;

    //     // Validate same year
    //     if ($start->year !== $end->year) {
    //         return back()->withErrors([
    //             'end_date' => 'Leave must be within the same calendar year'
    //         ])->withInput();
    //     }

    //     // Check leave balance (only approved leaves count)
    //     $leavesTaken = Leaves::where('employee_id', $employee->employee_id)
    //         ->where('leave_type_id', $leaveType->id)
    //         ->where('status', Leaves::STATUS_APPROVED)
    //         ->whereYear('start_date', $start->year)
    //         ->sum('total_days');

    //     $remainingDays = $leaveType->max_days - $leavesTaken;

    //     if ($totalDays > $remainingDays) {
    //         return back()->withErrors([
    //             'balance' => "Insufficient leave balance! You only have $remainingDays days left for $leaveType->name this year"
    //         ])->withInput();
    //     }






    //     // if($attendance->exists&&!is_null($attendance->check_out)){
    //     //     return response()->json([
    //     //         'message' => 'You have already clocked in and out today.'
    //     //     ], 400);
    //     // }
    //     $imageData = $data['face_image'];
    //     [$type, $base64Data] = explode(';', $imageData);
    //     [, $base64Data] = explode(',', $base64Data);
    //     $imageBinary = base64_decode($base64Data);

    //     $extension = strpos($type, 'jpeg') !== false ? 'jpg' : 'png';
    //     $filename =  $employee->id . '.' . $extension;
    //     $path = 'attendance_photos/' . $employee->id . '/' . $filename;
    //     if (Storage::disk('public')->exists($path)) {
    //         Storage::disk('public')->delete($path);
    //     }
    //     Storage::disk('public')->put($path, $imageBinary);


    //     $imagePath = public_path('storage/' . $path);
    //     $imagePath = str_replace('/', '\\', $imagePath);

    //     // $pythonScript = 'F:\Attendance Management System\face_detection_python\recognize.py';
    //     $pythonScript = 'F:\Attendance Management System\face_detection_python\recognize-version2.py';

    //     // Prepare the command, redirect stderr to stdout to capture errors
    //     $command = sprintf(
    //         'python "%s" recognize "%s" 2>&1',
    //         $pythonScript,
    //         $imagePath
    //     );

    //     // dd($command);

    //     // Execute the command
    //     exec($command, $output, $returnVar);

    //     // Convert output array to string
    //     $outputString = implode("\n", $output);

    //     if ($returnVar !== 0) {
    //         return response()->json([
    //             'error' => 'Python script error',
    //             'output' => $outputString,
    //             'return_code' => $returnVar,
    //         ], 500);
    //     }

    //     $response = json_decode($outputString, true);
    //     // dd($response);
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return response()->json([
    //             'error' => 'Invalid JSON from Python script',
    //             'output' => $outputString,
    //         ], 500);
    //     }
    //     if (isset($response['success']) && $response['success'] == true) {
    //         $employee_id = $response['results'][0]['emp_id'];
    //         if ($employeeId = Auth::user()->employee_id == $employee_id) {
    //             // Create leave with pending status
    //             Leaves::create([
    //                 'employee_id' => $employee->employee_id,
    //                 'leave_type_id' => $request->leave_type_id,
    //                 'start_date' => $request->start_date,
    //                 'end_date' => $request->end_date,
    //                 'total_days' => $totalDays,
    //                 'reason' => $request->reason,
    //                 'status' => Leaves::STATUS_PENDING, // Default status
    //             ]);

    //             return redirect()->route('leaves.create')
    //                 ->with('success', 'Leave request submitted successfully! Waiting for HR approval.');
    //         } else {
    //             return response()->json([
    //                 'message' => 'You are not authozied to attendence'
    //             ], 400);
    //         }
    //     } else {
    //         return response()->json([
    //             'message' => 'You are not authozied to attendence'
    //         ], 400);
    //     }
    // }
}
