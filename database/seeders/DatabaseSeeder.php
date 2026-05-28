<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        User::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        User::create([
            'name' => 'Admin KMI',
            'email' => 'admin@outlook.com',
            'password' => bcrypt('adminkmi123'),
        ]);
    }
}