<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            '山田 太郎',
            '西 伶奈',
            '増田 一世',
            '山本 敬吉',
            '秋田 朋美',
            '中西 教夫',
        ];

        foreach ($users as $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }
    }
}
