<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // PEKERJA
        User::create([
            'name' => 'Mira Agustiansyah',
            'email' => 'miraa@gmail.com',
            'role' => 'pekerja',
            'status' => 'Aktif',
            'password' => Hash::make('pekerja123')
        ]);

        // SUPERVISOR
        User::create([
            'name' => 'Puspitasari Alfaris',
            'email' => 'pitaa@gmail.com',
            'role' => 'supervisor',
            'status' => 'Aktif',
            'password' => Hash::make('supervisor123')
        ]);

        // ADMIN
        User::create([
            'name' => 'Ayu Wulandari',
            'email' => 'ayuu17@gmail.com',
            'role' => 'admin',
            'status' => 'Aktif',
            'password' => Hash::make('adminadmin')
        ]);

        // SAFETY OFFICER
        User::create([
            'name' => 'Projek Bertiga',
            'email' => 'projekbertiga@gmail.com',
            'role' => 'safety officer',
            'status' => 'Aktif',
            'password' => Hash::make('magang26')
        ]);

    }
}