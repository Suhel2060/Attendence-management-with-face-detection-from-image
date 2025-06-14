<?php

namespace App\Http\Controllers;

use App\Models\Leaves;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRLeaveController extends Controller
{
    public function index()
    {
        $pendingLeaves = Leaves::with(['leaveType', 'employee'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hr.leaves.index', compact('pendingLeaves'));
    }
 
    public function show(Leaves $leave)
    {
        return view('hr.leaves.show', compact('leave'));
    }

    public function approve(Leaves $leave)
    {
        $leave->update(['status' => Leaves::STATUS_APPROVED]);
        
        return redirect()->route('hr.leaves.index')
            ->with('success', 'Leave approved successfully!');
    }

    public function reject(Request $request, Leaves $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $leave->update([
            'status' => Leaves::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->route('hr.leaves.index')
            ->with('success', 'Leave rejected successfully!');
    }
}