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


    }
