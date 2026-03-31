<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
        public function definition()
        {
            return [
                'user_id' => \App\Models\User::factory(),
                'attendance_id' => \App\Models\Attendance::factory(),
                'status' => 'pending',
            ];
        }
    }
