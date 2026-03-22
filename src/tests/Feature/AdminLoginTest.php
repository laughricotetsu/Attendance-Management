<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_email_is_required_for_admin()
    {
        // 管理者ユーザー作成
        User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // email 空で送信
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        // バリデーションエラーを確認
        $response
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors([
                'email' => 'メールアドレスを入力してください',
            ]);
    }

    /** @test */
    public function test_password_is_required()
    {
        // 管理者ユーザー作成
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // password 空
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => $admin->email,
            'password' => '',
        ]);

        $response
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors([
                'password' => 'パスワードを入力してください',
            ]);
    }

    /** @test */
    public function test_login_fails_with_invalid_credentials()
    {
        // 正しい管理者を登録
        User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 間違ったメールアドレスでログイン
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors([
                'email' => 'ログイン情報が登録されていません',
            ]);
    }
}