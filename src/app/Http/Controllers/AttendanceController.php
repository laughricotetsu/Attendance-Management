<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', $today)
            ->first();

        return view('attendance.index', compact('attendance'));
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
}
