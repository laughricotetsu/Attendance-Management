<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

        /** @test */
        public function test_email_is_required_for_admin()
        {
            User::factory()->create([
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);

            $response = $this->post('/admin/login', [
                'password' => 'password',
            ]);

            $response->assertSessionHasErrors(['email']);
        }

        /** @test */
        public function test_password_is_required()
        {
            $admin = User::factory()->create([
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);

            $response = $this->post('/admin/login', [
                'email' => $admin->email,
            ]);

            $response->assertSessionHasErrors(['password']);
        }

        /** @test */
        public function test_login_fails_with_invalid_credentials()
        {
            User::factory()->create([
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);

            $response = $this->post('/admin/login', [
                'email' => 'wrong@example.com',
                'password' => 'password',
            ]);

            $response->assertSessionHasErrors();
        }
}
