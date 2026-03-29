<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;


class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

            public function test_勤怠詳細画面に正しいデータが表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $attendance = Attendance::factory()->create([
                'clock_in' => '09:00:00',
                'clock_out' => '18:00:00',
                'remarks' => 'テスト備考',
            ]);

            $this->actingAs($admin);

            $response = $this->get(route('admin.attendance.detail', $attendance->id));

            $response->assertStatus(200);

            $response->assertSee('09:00');
            $response->assertSee('18:00');
            $response->assertSee('テスト備考');
        }

        public function test_備考未入力でエラーが表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $attendance = Attendance::factory()->create();

            $this->actingAs($admin);

            $response = $this->put(
                route('admin.attendance.update', $attendance->id),
                [
                    'clock_in' => '09:00',
                    'clock_out' => '18:00',
                    'remarks' => '', // ← 空
                ]
            );

            $response->assertSessionHasErrors([
                'remarks' => '備考を記入してください',
            ]);
        }

        public function test_出勤時間が退勤時間より後だとエラー()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $attendance = Attendance::factory()->create();

            $this->actingAs($admin);

        $response = $this->put(
            route('admin.attendance.update', $attendance->id),
            [
                        'clock_in' => '19:00',
                        'clock_out' => '18:00',
                        'remarks' => 'テスト',
                    ]
                );

                $response->assertSessionHasErrors([
                    'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
                ]);
            }

        public function test_休憩開始が退勤より後だとエラー()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $attendance = Attendance::factory()->create([
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);

            $break = BreakTime::factory()->create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);

            $this->actingAs($admin);

            $response = $this->put(
                route('admin.attendance.update', $attendance->id),
                [
                    'clock_in' => '09:00',
                    'clock_out' => '18:00',
                    'remarks' => 'テスト',
                    'breaks' => [
                        $break->id => [
                            'break_start' => '19:00', // ← 退勤より後
                            'break_end' => '20:00',
                        ]
                    ]
                ]
            );

            $response->assertSessionHasErrors([
                'breaks.' . $break->id . '.break_start' => '休憩時間が不適切な値です',
            ]);
        }

        public function test_休憩終了が退勤より後だとエラー()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $attendance = Attendance::factory()->create([
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);

            $break = BreakTime::factory()->create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);

            $this->actingAs($admin);

            $response = $this->put(
                route('admin.attendance.update', $attendance->id),
                [
                    'clock_in' => '09:00',
                    'clock_out' => '18:00',
                    'remarks' => 'テスト',
                    'breaks' => [
                        $break->id => [
                            'break_start' => '10:00',
                            'break_end' => '19:00', // ← 退勤より後
                        ]
                    ]
                ]
            );

            $response->assertSessionHasErrors([
                'breaks.' . $break->id . '.break_end'
                    => '休憩時間もしくは退勤時間が不適切な値です',
            ]);
        }

    }

