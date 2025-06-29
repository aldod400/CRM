<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create(
            [
                'email' => 'admin@admin.com',
                'name' => 'Admin',
                'phone' => '1234567890',
                'image' => null,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole('admin');
    }
}
