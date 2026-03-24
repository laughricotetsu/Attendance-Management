<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

            public function test_自分の勤怠情報が全て表示される()
        {
            $user = User::factory()->create();

            $attendances = collect();

            for ($i = 0; $i < 3; $i++) {
                $attendances->push(
                    Attendance::factory()->create([
                        'user_id' => $user->id,
                        'work_date' => now()->subDays($i),
                    ])
                );
            }

            $this->actingAs($user)
                ->get('/attendance/list')
                ->assertStatus(200);

            // 自分のデータが表示されているか確認
            foreach ($attendances as $attendance) {
                $this->assertDatabaseHas('attendances', [
                    'id' => $attendance->id,
                ]);
            }
        }
        public function test_現在の月が表示される()
        {
            $user = User::factory()->create();

            $this->actingAs($user)
                ->get('/attendance/list')
                ->assertSee(now()->format('Y/m'));
        }

        public function test_前月の情報が表示される()
        {
            $user = User::factory()->create();

            // 前月データ作る
            Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->subMonth(),
            ]);

            $this->actingAs($user)
                ->get('/attendance/list?month=' . now()->subMonth()->format('Y-m'))
                ->assertSee(now()->subMonth()->format('Y/m'));
        }

        public function test_翌月の情報が表示される()
        {
            $user = User::factory()->create();

            Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->addMonth(),
            ]);

            $this->actingAs($user)
                ->get('/attendance/list?month=' . now()->addMonth()->format('Y-m'))
                ->assertSee(now()->addMonth()->format('Y/m'));
        }

        public function test_詳細画面に遷移できる()
        {
            $user = User::factory()->create();

            $attendance = Attendance::factory()->create([
                'user_id' => $user->id,
            ]);

            $this->actingAs($user)
                ->get('/attendance/detail/' . $attendance->id)
                ->assertStatus(200);
        }

    }
