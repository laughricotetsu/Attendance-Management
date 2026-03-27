<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
        public function test_名前がログインユーザーになっている()
        {
            $user = User::factory()->create([
                'name' => 'テスト太郎',
            ]);

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            $this->actingAs($user)
                ->get('/attendance/detail/' . $attendance->id)
                ->assertSee('テスト太郎');
        }

                public function test_日付が正しく表示される()
        {
            $user = User::factory()->create();

            $date = now();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => $date,
            ]);

            $this->actingAs($user)
                ->get('/attendance/detail/' . $attendance->id)
                ->assertSee($date->format('Y年m月d日'));
        }

                public function test_出勤退勤時間が一致する()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'clock_in' => now()->setTime(9, 0),
                'clock_out' => now()->setTime(18, 0),
            ]);

            $this->actingAs($user)
                ->get('/attendance/detail/' . $attendance->id)
                ->assertSee('09:00')
                ->assertSee('18:00');
        }

                public function test_休憩時間が一致する()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            BreakTime::factory()->create([
                'attendance_id' => $attendance->id,
                'break_start' => now()->setTime(12, 0),
                'break_end' => now()->setTime(13, 0),
            ]);

            $this->actingAs($user)
                ->get('/attendance/detail/' . $attendance->id)
                ->assertSee('12:00')
                ->assertSee('13:00');
        }

        
    }
    
