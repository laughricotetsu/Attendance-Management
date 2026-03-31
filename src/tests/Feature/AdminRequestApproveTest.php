<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;


class AdminRequestApproveTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

        public function test_承認待ちの修正申請が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user = User::factory()->create();

            // 承認待ち
            $request = AttendanceCorrectionRequest::factory()->create([
                'user_id' => $user->id,
                'attendance_id' => \App\Models\Attendance::factory(),
                'status' => 'pending',
                'reason' => 'テスト理由',
            ]);

            $this->actingAs($admin);

            $response = $this->get('/stamp_correction_request/list?status=pending');

            $response->assertStatus(200);

            $response->assertSee($user->name);
        }

        public function test_承認済みの修正申請が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user = User::factory()->create();

            $request = \App\Models\AttendanceCorrectionRequest::factory()->create([
                'user_id' => $user->id,
                'status' => 'approved',
                'reason' => 'テスト理由',
            ]);

            $this->actingAs($admin);

            $response = $this->get('/stamp_correction_request/list?status=approved');

            $response->assertStatus(200);

            $response->assertSee($user->name);
        }


        public function test_修正申請の詳細が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            $request = \App\Models\AttendanceCorrectionRequest::factory()->create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'status' => 'pending',
                'reason' => 'テスト理由',
            ]);

            $this->actingAs($admin);

            $response = $this->get(
                route('admin.correction.request.approve', $request->id)
            );

            $response->assertStatus(200);

            // 👇 表示確認（まずはこれ）
            $response->assertSee($user->name);
            $response->assertSee('テスト理由');
        }

        public function test_修正申請が承認される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);

            $request = \App\Models\AttendanceCorrectionRequest::factory()->create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'status' => 'pending',
                'reason' => 'テスト理由',
            ]);

            $this->actingAs($admin);

            // 承認処理
            $response = $this->post(
                route('admin.correction.request.approve.update', $request->id)
            );

            // ステータス変わるか
            $this->assertDatabaseHas('attendance_correction_requests', [
                'id' => $request->id,
                'status' => 'approved',
            ]);
        }
    }


