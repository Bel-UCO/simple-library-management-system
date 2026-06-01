<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin Library',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'address' => 'Library Office',
                'status' => 'active',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'member@mail.com'],
            [
                'name' => 'Member Library',
                'password' => Hash::make('password'),
                'phone' => '081234567891',
                'address' => 'Member Address',
                'status' => 'active',
                'is_admin' => false,
            ]
        );
    }
}