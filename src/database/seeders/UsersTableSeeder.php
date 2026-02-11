<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // 👇 ① 管理者を追加
        User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 👇 ② 一般ユーザー
        $users = [
            ['name' => '山田 太郎', 'email' => 'yamada@example.com'],
            ['name' => '西 伶奈', 'email' => 'nishi@example.com'],
            ['name' => '増田 一世', 'email' => 'masuda@example.com'],
            ['name' => '山本 敬吉', 'email' => 'yamamoto@example.com'],
            ['name' => '秋田 朋美', 'email' => 'akita@example.com'],
            ['name' => '中西 教夫', 'email' => 'nakanishi@example.com'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }

    }
}
