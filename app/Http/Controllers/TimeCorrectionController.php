<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendence;
use App\Models\TimeCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeCorrectionController extends Controller
{
    // ─── Employee Side ─────────────────────────────────────────

    public function myRequests()
    {
        $requests = TimeCorrection::where('employee_id', Auth::user()->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('time-corrections.index', compact('requests'));
    }

    public function create()
    {
        $today = Carbon::today()->toDateString();
        $attendance = Attendence::where('employee_id', Auth::user()->employee_id)
            ->whereDate('date', $today)
            ->first();
        return view('time-corrections.create', compact('attendance'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:clock_in,clock_out,both',
            'requested_time_in' => 'required_if:type,clock_in,both|nullable|date_format:H:i',
            'requested_time_out' => 'required_if:type,clock_out,both|nullable|date_format:H:i',
            'reason' => 'required|string|min:10|max:500',
        ]);

        // Prevent duplicate pending requests for same date+type
        $existing = TimeCorrection::where('employee_id', Auth::user()->employee_id)
            ->where('date', $data['date'])
            ->where('type', $data['type'])
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->withErrors(['date' => 'You already have a pending correction request for this date and type.'])->withInput();
        }

        TimeCorrection::create([
            'employee_id' => Auth::user()->employee_id,
            'date' => $data['date'],
            'type' => $data['type'],
            'requested_time_in' => $data['requested_time_in'],
            'requested_time_out' => $data['requested_time_out'],
            'reason' => $data['reason'],
        ]);

        return redirect()->route('time-corrections.my-requests')
            ->with('success', 'Time correction request submitted successfully! Waiting for HR approval.');
    }

    // ─── HR / Admin Side ───────────────────────────────────────

    public function index()
    {
        $requests = TimeCorrection::with(['employee', 'reviewer'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();
        return view('hr.time-corrections.index', compact('requests'));
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $correction = TimeCorrection::findOrFail($id);

            if ($correction->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'This request has already been ' . $correction->status . '.'], 400);
            }

            $attendance = Attendence::firstOrNew([
                'employee_id' => $correction->employee_id,
                'date' => $correction->date,
            ]);

            if (!$attendance->exists) {
                $attendance->date = $correction->date;
                $attendance->status = 'present';
            }

            if (in_array($correction->type, ['clock_in', 'both']) && $correction->requested_time_in) {
                $attendance->check_in = $correction->requested_time_in;
            }

            if (in_array($correction->type, ['clock_out', 'both']) && $correction->requested_time_out) {
                $attendance->check_out = $correction->requested_time_out;
            }

            $attendance->save();

            $correction->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            $correction->load('reviewer');
            $status_html = view('hr.time-corrections._status_badge', ['correction' => $correction])->render();

            return response()->json([
                'success' => true,
                'message' => 'Time correction approved and attendance updated.',
                'status_html' => $status_html,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to approve request.'], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $data = $request->validate([
            'review_notes' => 'required|string|min:5|max:500',
        ]);

        $correction = TimeCorrection::findOrFail($id);

        if ($correction->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This request has already been ' . $correction->status . '.'], 400);
        }

        $correction->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $data['review_notes'],
        ]);

        $correction->load('reviewer');
        $status_html = view('hr.time-corrections._status_badge', ['correction' => $correction])->render();

        return response()->json([
            'success' => true,
            'message' => 'Time correction request rejected.',
            'status_html' => $status_html,
        ]);
    }
}
