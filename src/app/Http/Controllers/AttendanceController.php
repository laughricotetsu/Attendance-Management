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
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        $status = 'off';

        if (!$attendance || !$attendance->clock_in) {
            $status = 'off';
        } elseif ($attendance->clock_out) {
            $status = 'finished';
        } elseif ($attendance->breaks()->whereNull('break_end')->exists()) {
            $status = 'break';
        } else {
            $status = 'working';
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

    public function endBreak()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if ($attendance) {

            $latestBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->latest()
                ->first();

            if ($latestBreak) {
                $latestBreak->break_end = now();
                $latestBreak->save();
            }
        }

        return redirect()->route('attendance.index');
    }

    public function finish()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->first();

        if ($attendance && !$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now()
            ]);
        }

        return redirect()->route('attendance.index');
    }


}
