<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(\Database\Seeders\AdminUserSeeder::class);
        $this->call(\Database\Seeders\ProductSeeder::class);
    }
}

