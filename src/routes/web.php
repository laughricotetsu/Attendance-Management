<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionRequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\StaffController;


/*
|--------------------------------------------------------------------------
| ユーザー登録
|--------------------------------------------------------------------------
*/

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


/*
|--------------------------------------------------------------------------
| 管理者ログイン
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', function () {
    return view('admin.auth.login');
})->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class,'login'])
    ->name('admin.login.post');


/*
|--------------------------------------------------------------------------
| ログインユーザー
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 打刻
    |--------------------------------------------------------------------------
    */

    Route::get('/attendance',[AttendanceController::class,'index'])
        ->name('attendance.index');

    Route::post('/attendance/start',[AttendanceController::class,'startWork'])
        ->name('attendance.start');

    Route::post('/attendance/finish',[AttendanceController::class,'finish'])
        ->name('attendance.finish');

    Route::post('/attendance/break/start',[AttendanceController::class,'startBreak'])
        ->name('attendance.break.start');

    Route::post('/attendance/break/end',[AttendanceController::class,'endBreak'])
        ->name('attendance.break.end');


    /*
    |--------------------------------------------------------------------------
    | 勤怠一覧
    |--------------------------------------------------------------------------
    */

    Route::get('/attendance/list',[AttendanceController::class,'list'])
        ->name('attendance.list');

    Route::get('/attendance/detail/{id}',[AttendanceController::class,'detail'])
        ->name('attendance.detail');


    /*
    |--------------------------------------------------------------------------
    | 修正申請
    |--------------------------------------------------------------------------
    */

    Route::post('/attendance/{attendance}/correction-request',
        [AttendanceCorrectionRequestController::class,'store']
    )->name('correction.request.store');


    /*
    |--------------------------------------------------------------------------
    | 申請一覧（ユーザー＋管理者共通）
    |--------------------------------------------------------------------------
    */

    Route::get('/stamp_correction_request/list',
        [AttendanceCorrectionRequestController::class,'index']
    )->name('correction.request.list');

});


/*
|--------------------------------------------------------------------------
| 管理者専用
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 修正申請 承認
    |--------------------------------------------------------------------------
    */

    Route::prefix('stamp_correction_request')->group(function(){

        Route::get('/approve/{id}',
            [AttendanceCorrectionRequestController::class,'approve']
        )->name('admin.correction.request.approve');

        Route::post('/approve/{id}',
            [AttendanceCorrectionRequestController::class,'approveUpdate']
        )->name('admin.correction.request.approve.update');

    });


    /*
    |--------------------------------------------------------------------------
    | 管理画面
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')->name('admin.')->group(function () {


        /*
        | 勤怠一覧
        */

        Route::get('/attendance/list',
            [AdminAttendanceController::class,'index']
        )->name('attendance.list');

        Route::get('/attendance/{attendance}',
            [AdminAttendanceController::class,'show']
        )->name('attendance.detail');

        Route::put('/attendance/{attendance}',
            [AdminAttendanceController::class,'update']
        )->name('attendance.update');


        /*
        | スタッフ一覧
        */

        Route::get('/staff/list',
            [StaffController::class,'index']
        )->name('staff.list');


        /*
        | スタッフ勤怠（月別）
        */

        Route::get('/staff/{id}/attendance',
            [StaffController::class,'attendance']
        )->name('staff.attendance');

    });

});