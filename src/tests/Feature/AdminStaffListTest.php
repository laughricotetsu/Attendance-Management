<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;


class AdminStaffListTest extends TestCase
{
        use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
        public function test_管理者がユーザー一覧を見れる()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            // 一般ユーザー
            $users = User::factory()->count(3)->create();

            $this->actingAs($admin);

            $response = $this->get('/admin/staff/list');

            $response->assertStatus(200);

            foreach ($users as $user) {
                $response->assertSee($user->name);
                $response->assertSee($user->email);
            }
        }

        public function test_特定ユーザーの勤怠一覧が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->toDateString(),
                'clock_in' => '09:00',
                'clock_out' => '18:00',
            ]);

            $this->actingAs($admin);

            $response = $this->get(
                route('admin.staff.attendance', $user->id)
            );

            $response->assertStatus(200);

            $response->assertSee($user->name);
            $response->assertSee('09:00');
            $response->assertSee('18:00');
        }


                public function test_前月を押すと前月の勤怠が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user = User::factory()->create();

            $lastMonth = now()->subMonth()->format('Y-m');

            Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->subMonth()->toDateString(),
            ]);

            $this->actingAs($admin);

            $response = $this->get(
                route('admin.staff.attendance', [
                    'id' => $user->id,
                    'month' => $lastMonth,
                ])
            );

            $response->assertStatus(200);

            $response->assertSee(
                \Carbon\Carbon::parse($lastMonth)->format('Y/m')
            );
        }

        public function test_翌月を押すと翌月の勤怠が表示される()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create();

    $nextMonth = now()->addMonth()->format('Y-m');

    Attendance::factory()->create([
        'user_id' => $user->id,
        'work_date' => now()->addMonth()->toDateString(),
    ]);

    $this->actingAs($admin);

    $response = $this->get(
        route('admin.staff.attendance', [
            'id' => $user->id,
            'month' => $nextMonth,
        ])
    );

    $response->assertStatus(200);

    $response->assertSee(
        \Carbon\Carbon::parse($nextMonth)->format('Y/m')
    );
}

public function test_詳細を押すと勤怠詳細画面に遷移する()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create();

    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
    ]);

    $this->actingAs($admin);

    $response = $this->get(
        route('admin.staff.attendance', $user->id)
    );

    $response->assertStatus(200);

    // 詳細リンクが存在するか
    $response->assertSee(
        route('admin.attendance.detail', $attendance->id)
    );
}

    }
