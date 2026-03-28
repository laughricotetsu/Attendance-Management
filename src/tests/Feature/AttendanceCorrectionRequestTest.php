<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;


class AttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

        public function test_出勤時間が退勤時間より後だとエラー()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'clock_in' => now()->setTime(9, 0),
                'clock_out' => now()->setTime(18, 0),
            ]);

            $this->actingAs($user);

            $response = $this->post("/attendance/{$attendance->id}/correction-request", [
                'clock_in' => '19:00',
                'clock_out' => '18:00',
                'remarks' => 'テスト',
            ]);

            $response->assertSessionHasErrors([
                'clock_in' => '出勤時間が不適切な値です'
            ]);
        }

        public function test_休憩開始が退勤より後だとエラー()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);

            $this->actingAs($user);

            $response = $this->post("/attendance/{$attendance->id}/correction-request", [
                'clock_in' => '09:00',
                'clock_out' => '18:00',
                'remarks' => 'テスト',
                'breaks' => [
                    'new' => [
                        'break_start' => '19:00', // ← NG
                        'break_end' => '19:30',
                    ]
                ]
            ]);

            $response->assertSessionHasErrors([
                'breaks.new.break_start' => '休憩時間が不適切な値です'
            ]);
        }

        public function test_備考未入力でエラーになる()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            $this->actingAs($user);

            $response = $this->post(
                route('correction.request.store', $attendance),
                [
                    'clock_in' => '10:00',
                    'clock_out' => '19:00',
                    'remarks' => '', // ← 空！！
                ]
            );

            $response->assertSessionHasErrors([
                'remarks' => '備考を記入してください',
            ]);
        }

        public function test_修正申請が保存される()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);

            $break = BreakTime::factory()->create([
                'attendance_id' => $attendance->id,
                'break_start' => '11:00',
                'break_end' => '12:00',
            ]);

            $this->actingAs($user);

            $response = $this->post(
                route('correction.request.store', $attendance),
                [
                    'clock_in' => '10:00',
                    'clock_out' => '19:00',
                    'remarks' => '修正テスト',
                    'breaks' => [
                        $break->id => [
                            'break_start' => '12:00',
                            'break_end' => '13:00',
                        ]
                    ]
                ]
            );

            $response->assertStatus(302);

            // 👇 親
            $this->assertDatabaseHas('attendance_correction_requests', [
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'reason' => '修正テスト',
            ]);

            // 👇 子（出勤・退勤）
            $this->assertDatabaseHas('attendance_correction_request_details', [
                'target_type' => 'clock_in',
                'after_value' => '10:00',
            ]);

            $this->assertDatabaseHas('attendance_correction_request_details', [
                'target_type' => 'clock_out',
                'after_value' => '19:00',
            ]);

            // 👇 子（休憩）
            $this->assertDatabaseHas('attendance_correction_request_details', [
                'target_type' => 'break_start',
                'after_value' => '12:00',
            ]);

            $this->assertDatabaseHas('attendance_correction_request_details', [
                'target_type' => 'break_end',
                'after_value' => '13:00',
            ]);
        }

        public function test_修正申請が管理者画面に表示される()
        {
            // 👤 一般ユーザー
            $user = User::factory()->create([
                'role' => 'user',
            ]);

            // 👑 管理者
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            // 勤怠
            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            // 一般ユーザーでログイン
            $this->actingAs($user);

            // 修正申請
            $this->post(
                route('correction.request.store', $attendance),
                [
                    'clock_in' => '10:00',
                    'clock_out' => '19:00',
                    'remarks' => '修正テスト',
                ]
            );

            // 👑 管理者でログインし直す
            $this->actingAs($admin);

            // 👇 申請一覧ページ
            $response = $this->get(route('correction.request.list'));

            // 表示確認
            $response->assertStatus(200);
            $response->assertSee('修正テスト');
        }

        public function test_承認待ちに自分の申請が表示される()
        {
            $user = User::factory()->create([
                'role' => 'user',
            ]);

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            $this->actingAs($user);

            // 修正申請
            $this->post(
                route('correction.request.store', $attendance),
                [
                    'clock_in' => '10:00',
                    'clock_out' => '19:00',
                    'remarks' => '承認待ちテスト',
                ]
            );

            // 一覧ページ
            $response = $this->get(route('correction.request.list'));

            $response->assertStatus(200);
            $response->assertSee('承認待ちテスト');
        }

        public function test_承認済みに表示される()
        {
            $user = User::factory()->create([
                'role' => 'user',
            ]);

            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            // 一般ユーザーで申請
            $this->actingAs($user);

            $this->post(
                route('correction.request.store', $attendance),
                [
                    'clock_in' => '10:00',
                    'clock_out' => '19:00',
                    'remarks' => '承認済みテスト',
                ]
            );

            // 👇 DBから取得して承認状態にする
            $request = \App\Models\AttendanceCorrectionRequest::first();

            $request->update([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

            // 一般ユーザーで一覧確認
            $this->actingAs($user);

            $response = $this->get(
            route('correction.request.list', ['status' => 'approved'])
        );

        $response->assertStatus(200);
        $response->assertSee('承認済みテスト');
        }

        public function test_申請一覧から詳細画面に遷移できる()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            $this->actingAs($user);

            // 👇 まず詳細ページにアクセスできるか確認
            $response = $this->get(route('attendance.detail', $attendance->id));

            $response->assertStatus(200);

            // 👇 詳細ページ
            $response->assertSee('勤怠詳細');
        }

}
