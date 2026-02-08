<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AttendanceController;

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


    Route::prefix('admin')->group(function () {

        Route::get('/login', function () {
            return view('admin.auth.login');
        });

        Route::get('/attendance/list', [AttendanceController::class, 'index'])
            ->name('admin.attendance.list');

        Route::get('/attendance/{id}', [AttendanceController::class, 'show'])
            ->name('admin.attendance.detail');

        Route::get('/staff/list', function () {
            return view('admin.staff.list');
        });

        Route::get('/stamp_correction_request/approve/{id}', function ($id) {
            return view('admin.stamp_correction_request.approve', compact('id'));
        });

    });
