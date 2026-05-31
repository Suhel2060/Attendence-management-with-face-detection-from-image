<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendenceController extends Controller
{
    public function index()
    {
        return view('pages.attendence');
    }

    public function identify(Request $request)
    {
        $data = $request->validate([
            'image' => [
                'required',
                'regex:/^data:image\/(jpeg|png);base64,[A-Za-z0-9\/+=]+$/'
            ],
        ]);

        $imageData = $data['image'];
        [, $base64Data] = explode(',', explode(';', $imageData)[1]);
        $imageBinary = base64_decode($base64Data);

        $response = Http::timeout(config('services.face_api.timeout', 10))
            ->attach('image', $imageBinary, 'capture.jpg')
            ->post(config('services.face_api.url') . '/api/recognize');

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Recognition service unavailable.'
            ], 503);
        }

        $result = $response->json();

        if ($result['status'] !== 'recognized') {
            return response()->json([
                'recognized' => false,
                'message' => $result['message'] ?? 'Face not recognized.'
            ], 200);
        }

        $employee = User::where('employee_id', $result['student_id'])->first();
        if (!$employee) {
            return response()->json([
                'recognized' => false,
                'message' => 'Face recognized but no employee record found.'
            ], 200);
        }

        $today = Carbon::today()->toDateString();
        $attendance = Attendence::where('employee_id', $employee->employee_id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            $action = 'clock_in';
        } elseif (is_null($attendance->check_out)) {
            $action = 'clock_out';
        } else {
            $action = 'finished';
        }

        return response()->json([
            'recognized' => true,
            'employee_id' => $employee->employee_id,
            'name' => $employee->name,
            'action' => $action,
        ]);
    }
    public function getAuthattendence()
    {
       $user_id=Auth::user()->id;
       $user=User::find($user_id);
       $employeeId = Auth::user()->employee_id;

        $today = Carbon::today()->toDateString();

        $attendance = Attendence::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        $records = Attendence::where('employee_id', $employeeId)
            ->orderByDesc('date')
            ->get();


        return view('pages.authattendence', compact( 'records','attendance','user'));
    }

    public function viewAttendence(Request $request)
    {
        $query = Attendence::with('user')->orderBy('employee_id');

    // Search by employee name or ID
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('employee_id', 'like', '%' . $request->search . '%');
        });
    }

    // Filter by date range
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $query->whereBetween('date', [$request->date_from, $request->date_to]);
    }

    $attendences = $query->get();
        return view('pages.viewattendence',compact('attendences'));
    }

    public function attendence(Request $request)
    {
        $data = $request->validate([
            'image' => [
                'required',
                'regex:/^data:image\/(jpeg|png);base64,[A-Za-z0-9\/+=]+$/'
            ],
        ]);

        $imageData = $data['image'];
        [, $base64Data] = explode(',', explode(';', $imageData)[1]);
        $imageBinary = base64_decode($base64Data);

        $recognizeResponse = Http::timeout(config('services.face_api.timeout', 10))
            ->attach('image', $imageBinary, 'capture.jpg')
            ->post(config('services.face_api.url') . '/api/recognize');

        if (!$recognizeResponse->successful()) {
            return response()->json([
                'message' => 'Face recognition service unavailable. Please try again.'
            ], 503);
        }

        $result = $recognizeResponse->json();

        if ($result['status'] !== 'recognized') {
            $messages = [
                'unknown' => 'Face not recognized. Please ensure you are enrolled.',
                'no_face' => 'No face detected. Please look at the camera.',
                'no_enrollments' => 'No enrolled faces in the system. Contact admin.',
            ];
            $statusCodes = [
                'unknown' => 401,
                'no_face' => 400,
                'no_enrollments' => 400,
            ];
            $msg = $messages[$result['status']] ?? 'Unable to process attendance.';
            return response()->json(['message' => $msg], $statusCodes[$result['status']] ?? 500);
        }

        $employee = User::where('employee_id', $result['student_id'])->first();
        if (!$employee) {
            return response()->json([
                'message' => 'Face recognized but no employee record was found.'
            ], 400);
        }

        $today = Carbon::today()->toDateString();
        $attendance = Attendence::firstOrNew(
            ['employee_id' => $employee->employee_id, 'date' => $today]
        );

        if ($attendance->exists && !is_null($attendance->check_out)) {
            return response()->json([
                'message' => $employee->name . ' has already clocked in and out today.'
            ], 400);
        }

        if (!$attendance->exists) {
            $attendance->status = 'Present';
            $attendance->date = $today;
            $attendance->check_in = Carbon::now()->format('H:i:s');
        } elseif (is_null($attendance->check_out)) {
            $attendance->check_out = Carbon::now()->format('H:i:s');
        }

        $attendance->save();

        return response()->json([
            'message'   => 'Attendance recorded for ' . $employee->name,
            'name'      => $employee->name,
            'check_in'  => $attendance->check_in,
            'check_out' => $attendance->check_out,
            'date'      => $attendance->date,
            'action'    => is_null($attendance->check_out) ? 'clock_in' : 'clock_out',
        ]);
    }

    public function getAttendence($id)
    {
        // $attendence=Attendence::where();
    }

    public function userAttendence($emp_id){
        $attendance=Attendence::where('employee_id',$emp_id)->get();
        $user=User::where('employee_id',$emp_id)->first();
        // dd($attendance);
        return view('pages.editattendence',compact('attendance','user'));
    }



    public function export(Request $request)
{
    $request->validate([
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date|after_or_equal:date_from',
        'search' => 'nullable|string|max:255'
    ]);

    $fileName = 'attendance-'.now()->format('Y-m-d').'.csv';

    $attendances = Attendence::with('user')
        ->when($request->date_from, function($query) use ($request) {
            $query->where('date', '>=', $request->date_from);
        })
        ->when($request->date_to, function($query) use ($request) {
            $query->where('date', '<=', $request->date_to);
        })
        ->when($request->search, function($query) use ($request) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        })
        ->orderBy('date', 'desc')
        ->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"             => "no-cache",
        "Cache-Control"      => "must-revalidate, post-check=0, pre-check=0",
        "Expires"            => "0"
    ];

    $callback = function() use($attendances) {
        $file = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($file, [
            'Employee ID',
            'User Name',
            'Date',
            'Status',
            'Check In',
            'Check Out'
        ]);

        // Add data rows
        foreach ($attendances as $attendance) {
            fputcsv($file, [
                $attendance->employee_id,
                $attendance->user->name,
                $attendance->date,
                ucfirst($attendance->status),
                $attendance->check_in ?? 'N/A',
                $attendance->check_out ?? 'N/A'
            ]);
        }

        fclose($file);
    };

    return new StreamedResponse($callback, 200, $headers);
}

}
