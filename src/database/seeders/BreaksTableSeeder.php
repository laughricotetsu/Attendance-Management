<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use App\Models\Attendance;
use Carbon\Carbon;

class BreaksTableSeeder extends Seeder
{
    public function run()
    {
        $attendances = Attendance::all();

        foreach ($attendances as $attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => Carbon::parse($attendance->date)->setTime(12, 0),
                'break_end' => Carbon::parse($attendance->date)->setTime(13, 0),
            ]);
        }
    }
}
