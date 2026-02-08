<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendancesTableSeeder extends Seeder
{
    public function run()
    {
        $date = Carbon::create(2023, 6, 1);

        $users = User::all();

        foreach ($users as $user) {
            Attendance::create([
                'user_id' => $user->id,
                'work_date' => $date->toDateString(),
                'clock_in' => $date->copy()->setTime(9, 0),
                'clock_out' => $date->copy()->setTime(18, 0),
                'status' => 'finished',
            ]);
        }
    }
}
