<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tuapp.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('clave-super-segura'), // cámbiala
                'is_admin' => true,
            ]
        );
    }
}