<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_出勤ボタンが正しく機能する()
    {
        $user = User::factory()->create();

        // ① ボタン表示確認
        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('出勤');

        // ② 出勤処理
        $this->actingAs($user)
            ->post('/attendance/start');

        // ③ ステータス確認
        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('出勤中');

        // ④ DB確認（おすすめ）
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
        ]);
    }

        public function test_出勤は一日一回のみ()
    {
        $user = User::factory()->create();

        // すでに退勤済のデータを作る
        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
            'status' => 'done',
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertDontSee('出勤');
    }

        public function test_出勤時刻が勤怠一覧で確認できる()
    {
        $user = User::factory()->create();

        // 出勤処理
        $this->actingAs($user)
            ->post('/attendance/start');

        // 一覧画面確認
        $this->actingAs($user)
            ->get('/attendance/list')
            ->assertSee(now()->format('H:i'));
    }
    public function test_休憩ボタンが正しく機能する()
    {
        $user = User::factory()->create();

        // 出勤状態を作る
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => now(),
            'clock_out' => null,
            'status' => 'working',
        ]);

        // ① ボタン表示
        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩入');

        // ② 休憩開始
        $this->actingAs($user)
            ->post('/attendance/break/start');

        // ③ ステータス確認
        $this->actingAs($user)
            ->get('/attendance')
            ->assertSee('休憩中');
    }

public function test_休憩は一日に何回でもできる()
{
    $user = User::factory()->create();

    Attendance::factory()->create([
        'user_id' => $user->id,
        'clock_in' => now(),
        'clock_out' => null,
        'status' => 'working',
    ]);

    // 休憩入
    $this->actingAs($user)->post('/attendance/break/start');

    // 休憩戻
    $this->actingAs($user)->post('/attendance/break/end');

    // 再度「休憩入」ボタンが出るか
    $this->actingAs($user)
        ->get('/attendance')
        ->assertSee('休憩入');
}

public function test_休憩戻ボタンが正しく機能する()
{
    $user = User::factory()->create();

    Attendance::factory()->create([
        'user_id' => $user->id,
        'clock_in' => now(),
        'clock_out' => null,
        'status' => 'working',
    ]);

    // 休憩入
    $this->actingAs($user)->post('/attendance/break/start');

    // 休憩戻
    $this->actingAs($user)->post('/attendance/break/end');

    // 出勤中に戻る
    $this->actingAs($user)
        ->get('/attendance')
        ->assertSee('出勤中');
}

public function test_休憩戻は一日に何回でもできる()
{
    $user = User::factory()->create();

    Attendance::factory()->create([
        'user_id' => $user->id,
        'clock_in' => now(),
        'clock_out' => null,
        'status' => 'working',
    ]);

    // 1回目
    $this->actingAs($user)->post('/attendance/break/start');
    $this->actingAs($user)->post('/attendance/break/end');

    // 2回目
    $this->actingAs($user)->post('/attendance/break/start');

    // 「休憩戻」ボタンが出るか
    $this->actingAs($user)
        ->get('/attendance')
        ->assertSee('休憩戻');
}

public function test_休憩時刻が一覧で確認できる()
{
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/attendance/start');

    $this->actingAs($user)
        ->post('/attendance/break/start');

    $this->actingAs($user)
        ->post('/attendance/break/end');

    $this->actingAs($user)
        ->get('/attendance/list')
        ->assertSee(now()->format('H:i'));
}

    }
