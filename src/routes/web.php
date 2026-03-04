<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\AuthController;



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

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');



// ===================
// 管理者ログイン（authの外）
// ===================
Route::get('/admin/login', function () {
    return view('admin.auth.login');
})->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.post');


// ===================
// 一般ユーザー（auth必須）
// ===================
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance/start', [AttendanceController::class, 'startWork'])
        ->name('attendance.start');

    Route::post('/attendance/finish', [AttendanceController::class, 'finish'])
        ->name('attendance.finish');

    Route::post('/attendance/break/start', [AttendanceController::class, 'startBreak'])
        ->name('attendance.break.start');

    Route::post('/attendance/break/end', [AttendanceController::class, 'endBreak'])
        ->name('attendance.break.end');

    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
        ->name('attendance.detail');

    Route::patch('/attendance/{attendance}', 
        [AttendanceController::class, 'update']
    )->name('attendance.update');

});

// ===================
// 管理画面（auth＋admin必須）
// ===================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','admin'])
    ->group(function () {

        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])
            ->name('attendance.list');

        Route::get('/attendance/{attendance}', [AdminAttendanceController::class, 'show'])
            ->name('attendance.detail');

        Route::put('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])
            ->name('attendance.update');

        Route::get('/staff/list', function () {
            return view('admin.staff.list');
        });

        Route::get('/stamp_correction_request/approve/{id}', function ($id) {
            return view('admin.stamp_correction_request.approve', compact('id'));
        });
});
