<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;

class AttendanceController extends Controller
{


    public function index()
    {

        $today = Carbon::today();

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', $today)
            ->first();

        $status = 'off'; // 勤務外

        if ($attendance) {

            if ($attendance->end_time) {
                $status = 'finished';
            } elseif ($attendance->break_start && !$attendance->break_end) {
                $status = 'break';
            } else {
                $status = 'working';
            }
        }

        return view('attendance.index', compact('attendance', 'status'));
    }

    public function startWork()
    {
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'work_date' => $today,
            ],
            [
                'status' => 'working'
            ]
        );

        $attendance->update([
            'clock_in' => now(),
            'status' => 'working'
        ]);

        return redirect()->route('attendance.index');
    }

    public function startBreak()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if ($attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => now(),
            ]);
        }

        return redirect()->route('attendance.index');
    }
}
