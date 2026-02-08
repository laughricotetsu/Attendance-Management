<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        // 勤怠 + ユーザーを一緒に取得
        $attendances = Attendance::with('user')
            ->orderBy('work_date', 'desc')
            ->get();

        return view('admin.attendance.list', compact('attendances'));
    }
}
