<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class StatusTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_勤務外ステータスが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('勤務外');
    }

    public function test_出勤中ステータスが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => now(),
            'clock_out' => null,
            'status' => 'working',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('出勤中');
    }

    public function test_休憩中ステータスが表示される()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => now(),
            'clock_out' => null,
            'status' => 'working',
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
            'break_end' => null, // ← 休憩中
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩中');
    }

    public function test_退勤済ステータスが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
            'status' => 'done',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('退勤済');
    }
}
