<?php

use App\Models\User;
use App\Models\Attendence;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\HRLeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendenceController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\TimeCorrectionController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthController::class,'index'])->name('login');

Route::get('/login', [AuthController::class,'index']);

Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class,'index']);
    Route::get('/attendence', [AttendenceController::class,'index']);
    Route::post('/attendence/identify', [AttendenceController::class,'identify'])->name('attendance.identify');
    Route::post('/attendence', [AttendenceController::class,'attendence'])->name('attendance.store');
    Route::put('/attendence/{id}', [AttendenceController::class,'attendence'])->name('attendance.edit');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/authattendence', [AttendenceController::class,'getAuthattendence']);


    Route::get('/profile',[ProfileController::class,'index']);

    // Time Correction - Employee
    Route::get('/time-corrections', [TimeCorrectionController::class, 'myRequests'])->name('time-corrections.my-requests');
    Route::get('/time-corrections/create', [TimeCorrectionController::class, 'create'])->name('time-corrections.create');
    Route::post('/time-corrections', [TimeCorrectionController::class, 'store'])->name('time-corrections.store');

    Route::middleware(['role:Admin'])->group(function () {
        Route::post('/users', [UserController::class, 'AddUser']);
        Route::post('/users', [UserController::class, 'AddUser']);
        Route::post('/users/{id}', [UserController::class, 'UpdateUser']);
        Route::Delete('/users/{id}', [UserController::class, 'DeleteUser']);
        Route::get('/user', [UserController::class, 'index']);
        Route::get('/enrollment', [EnrollmentController::class, 'index'])->name('enrollment.index');
        Route::post('/enrollment', [EnrollmentController::class, 'enroll'])->name('enrollment.store');
        Route::delete('/enrollment/{employee_id}', [EnrollmentController::class, 'destroy'])->name('enrollment.destroy');
        Route::get('/attendence/{emp_id}',[AttendenceController::class,'userAttendence']);
        Route::get('/all-attendence', [AttendenceController::class,'viewAttendence']);
        Route::get('/attendance/export', [AttendenceController::class, 'export'])
     ->name('attendance.export');
     // HR Time Corrections
     Route::get('/hr/time-corrections', [TimeCorrectionController::class, 'index'])->name('hr.time-corrections.index');
     Route::post('/hr/time-corrections/{id}/approve', [TimeCorrectionController::class, 'approve'])->name('hr.time-corrections.approve');
     Route::post('/hr/time-corrections/{id}/reject', [TimeCorrectionController::class, 'reject'])->name('hr.time-corrections.reject');

     Route::prefix('hr')->group(function () {
        Route::get('/leaves', [HRLeaveController::class, 'index'])->name('hr.leaves.index');
        Route::get('/leaves/{leave}', [HRLeaveController::class, 'show'])->name('hr.leaves.show');
        Route::post('/leaves/{leave}/approve', [HRLeaveController::class, 'approve'])->name('hr.leaves.approve');
        Route::post('/leaves/{leave}/reject', [HRLeaveController::class, 'reject'])->name('hr.leaves.reject');
    });
    });


    Route::get('/employee/leaves', function(){
        return view('employee_leave');
    })->name('employee.leaves');



    
});
