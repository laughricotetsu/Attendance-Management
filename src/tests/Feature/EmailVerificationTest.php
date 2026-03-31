<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;



class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
        public function test_認証メールが送信される()
        {
            Notification::fake();

            $user = User::factory()->unverified()->create();

            // メール送信
            $user->sendEmailVerificationNotification();

            Notification::assertSentTo(
                $user,
                VerifyEmail::class
            );
        }


        public function test_認証画面に遷移できる()
        {
            $user = User::factory()->unverified()->create();

            $this->actingAs($user);

            $response = $this->get('/email/verify');

            $response->assertStatus(200);
        }


public function test_メール認証後に勤怠画面へ遷移する()
{
    Event::fake();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    // 認証URL生成
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->get($verificationUrl);

    $response->assertRedirect('/attendance?verified=1');

    Event::assertDispatched(Verified::class);
}
    }
