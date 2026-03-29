<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;


class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

        public function test_管理者はその日の全ユーザーの勤怠を確認できる()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $today = today()->toDateString();

            Attendance::factory()->create([
                'user_id' => $user1->id,
                'work_date' => $today,
            ]);

            Attendance::factory()->create([
                'user_id' => $user2->id,
                'work_date' => $today,
            ]);

            $this->actingAs($admin);

            $response = $this->get('/admin/attendance/list');

            $response->assertStatus(200);

            // 👇 名前が表示されること
            $response->assertSee($user1->name);
            $response->assertSee($user2->name);
        }

        public function test_現在の日付が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $this->actingAs($admin);

            $response = $this->get('/admin/attendance/list');

            $response->assertStatus(200);

            $response->assertSee(today()->format('Y年n月j日') . 'の勤怠');
        }

        public function test_前日を押すと前日の勤怠が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $yesterday = today()->subDay()->toDateString();

            $user = User::factory()->create();

            Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => $yesterday,
            ]);

            $this->actingAs($admin);

            $response = $this->get('/admin/attendance/list?date=' . $yesterday);

            $response->assertStatus(200);

            // 日付確認
            $response->assertSee(
                \Carbon\Carbon::parse($yesterday)->format('Y年n月j日')
            );

            // ユーザー表示確認
            $response->assertSee($user->name);
        }

        public function test_翌日を押すと翌日の勤怠が表示される()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
            ]);

            $tomorrow = today()->addDay()->toDateString();

            $user = User::factory()->create();

            Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => $tomorrow,
            ]);

            $this->actingAs($admin);

            $response = $this->get('/admin/attendance/list?date=' . $tomorrow);

            $response->assertStatus(200);

            // 日付確認
            $response->assertSee(
                \Carbon\Carbon::parse($tomorrow)->format('Y年n月j日')
            );

            // ユーザー表示確認
            $response->assertSee($user->name);
        }



}
