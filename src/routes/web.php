<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


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
    });

    Route::get('/login', function () {
        return view('auth.login');
    });

    Route::get('/attendance', function () {
        return view('attendance.index');
    });

    Route::get('/attendance/list', function () {
        return view('attendance.list');
    });

    Route::get('/attendance/detail/{id}', function () {
        return view('attendance.detail');
    });

    Route::get('/stamp_correction_request/list', function () {
        return view('stamp_correction_request.list');
    });

    Route::post(
        '/attendance/{attendance}/request',
        [AttendanceController::class, 'requestCorrection']
    )->name('attendance.request');



Route::post('/login', function () {
    $user = User::first(); // 管理者想定ユーザー
    Auth::login($user);

    return redirect()->route('admin.attendance.list');
});

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // 管理画面（ログイン必須）
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('auth')
        ->group(function () {

        Route::get('/attendance/list', [AttendanceController::class, 'index'])
            ->name('attendance.list');

        Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])
            ->name('attendance.detail');

        Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])
            ->name('attendance.update');


        Route::get('/staff/list', function () {
            return view('admin.staff.list');
        });

        Route::get('/stamp_correction_request/approve/{id}', function ($id) {
            return view('admin.stamp_correction_request.approve', compact('id'));
        });
    });
