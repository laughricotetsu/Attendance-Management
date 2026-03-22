<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレス必須
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertStatus(302);

        $this->assertStringContainsString(
            'メールアドレスを入力してください',
            session('errors')->first('email')
        );
    }

    /**
     * パスワード必須
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertStatus(302);

        $this->assertStringContainsString(
            'パスワードを入力してください',
            session('errors')->first('password')
        );
    }

    /**
     * 認証失敗
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'not_exist@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);

        $this->assertStringContainsString(
            'ログイン情報が登録されていません',
            session('errors')->first('email')
        );
    }

    /**
     * ログイン成功
     */
    public function test_login_success()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }
}