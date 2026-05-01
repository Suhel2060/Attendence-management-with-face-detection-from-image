<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendenceController extends Controller
{
    public function index()
    {
       // $user_id=Auth::user()->id;
       // $user=User::find($user_id);
        // 1) Get the logged-in user’s employee_id
      //  $employeeId = Auth::user()->employee_id;

        // 2) Today’s date (Y-m-d)
        // $today = Carbon::today()->toDateString();

        // $attendance = Attendence::where('employee_id', $employeeId)
        //     ->where('date', $today)
        //     ->first();
        // if (! $attendance) {
        //     $clockbutton = 'Clock IN';
        // } elseif (! $attendance->check_out) {
        //     $clockbutton = 'Clock OUT';
        // } else {
        //     $clockbutton = 'Clock IN';
        // }

        // 5) Get all records for this user
        // $records = Attendence::where('employee_id', $employeeId)
        //     ->orderByDesc('date')
        //     ->get();

        $clockbutton = 'Attendence';

        return view('pages.attendence', compact( 'clockbutton'));
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



        // if($attendance->exists&&!is_null($attendance->check_out)){
        //     return response()->json([
        //         'message' => 'You have already clocked in and out today.'
        //     ], 400);
        // }
        $imageData = $data['image'];
        [$type, $base64Data] = explode(';', $imageData);
        [, $base64Data] = explode(',', $base64Data);
        $imageBinary = base64_decode($base64Data);

        $extension = strpos($type, 'jpeg') !== false ? 'jpg' : 'png';
        $filename =   "Attendance".Carbon::now()->format('Y-m-d').rand(1,1000000). '.' . $extension;
        $path = 'attendance_photos/' . $filename;
        Storage::disk('public')->put($path, $imageBinary);

        $imagePath = public_path('storage/' . $path);
        $imagePath = str_replace('/', '\\', $imagePath);
        
        // $pythonScript = 'F:\Attendance Management System\face_detection_python\recognize.py';
        $pythonScript = 'F:\Attendance Management System\face_detection_python\recognize-version2.py';

        // Prepare the command, redirect stderr to stdout to capture errors
        $command = sprintf(
            'python "%s" recognize "%s" 2>&1',
            $pythonScript,
            $imagePath
        );


        // dd($command);
        
        // Execute the command
        exec($command, $output, $returnVar);
        
        // Convert output array to string
        $outputString = implode("\n", $output);
                if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        if ($returnVar !== 0) {
            return response()->json([
                'error' => 'Python script error',
                'output' => $outputString,
                'return_code' => $returnVar,
            ], 500);
        }
        
        $response = json_decode($outputString, true);
        // dd($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'error' => 'Invalid JSON from Python script',
                'output' => $outputString,
            ], 500);
        }

        if(isset($response['success'])&&$response['success']==true){
            $employee_id=$response['results'][0]['emp_id'];
            $employee=User::where('employee_id',$employee_id)->first();
            if(        $employee){
                $employeeId = $employee->employee_id;
                $today      = Carbon::today()->toDateString();
                $attendance = Attendence::firstOrNew(
                    ['employee_id' => $employeeId, 'date' => $today]
                );
                if (! $attendance->exists) {
                    $attendance->status    = 'Present';
                    $attendance->date    = $today;
                    $attendance->check_in  = Carbon::now()->format('H:i:s');
                    // leave check_out null
                } elseif (is_null($attendance->check_out)) {
                    $attendance->check_out = Carbon::now()->format('H:i:s');
                } else {
                    return response()->json([
                        'message' => 'You have already clocked in and out today.'
                    ], 400);
                }
                $attendance->save();

                return response()->json([
                    'message'    => 'Attendance recorded for Employee ID '.$employee->employee_id."( ".$employee->name." )",
                    'check_in'   => $attendance->check_in,
                    'check_out'  => $attendance->check_out,
                    'date'       => $attendance->date,
                ]);
            }else{
                return response()->json([
                    'message' => 'You are not authozied to attendence'
                ], 400);
            }
        }else{
            return response()->json([
                'message' => 'You are not authozied to attendence'
            ], 400);
        }



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
