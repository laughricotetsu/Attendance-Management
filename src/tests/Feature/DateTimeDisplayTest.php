<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class DateTimeDisplayTest extends TestCase
{
use RefreshDatabase;

    /** @test */
    public function 現在日時が表示される()
    {
        Carbon::setTestNow('2024-01-01 10:00:00');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        // 日付
        $response->assertSee('2024年1月1日');

        // 曜日
        $response->assertSee('(月)');

        // 時間
        $response->assertSee('10:00');
    }
}
