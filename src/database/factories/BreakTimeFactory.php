<?php

namespace Database\Factories;

use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => now(),
            'break_end' => null,
        ];
    }

    public function test_退勤ボタンが正しく機能する()
{
    $user = User::factory()->create();

    // 出勤状態を作る
    Attendance::factory()->create([
        'user_id' => $user->id,
        'clock_in' => now(),
        'clock_out' => null,
        'status' => 'working',
    ]);

    // ① ボタン表示確認
    $this->actingAs($user)
        ->get('/attendance')
        ->assertSee('退勤');

    // ② 退勤処理
    $this->actingAs($user)
        ->post('/attendance/finish');

    // ③ ステータス確認
    $this->actingAs($user)
        ->get('/attendance')
        ->assertSee('退勤済');

    // ④ DB確認
    $this->assertDatabaseHas('attendances', [
        'user_id' => $user->id,
        'clock_out' => now(),
    ]);
}

public function test_退勤時刻が一覧で確認できる()
{
    $user = User::factory()->create();

    // 出勤
    $this->actingAs($user)
        ->post('/attendance/start');

    // 退勤
    $this->actingAs($user)
        ->post('/attendance/finish');

    // 一覧確認
    $this->actingAs($user)
        ->get('/attendance/list')
        ->assertSee(now()->format('H:i'));
}

}