<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Mira Agustiansyah',
                'email' => 'miraa@gmail.com',
                'password' => Hash::make('pekerja123'),
                'role' => 'pekerja',
                'status' => 'Aktif'
            ],
            [
                'name' => 'Puspitasari Alfaris',
                'email' => 'pitaa@gmail.com',
                'password' => Hash::make('supervisor123'),
                'role' => 'supervisor',
                'status' => 'Aktif'
            ],
            [
                'name' => 'Ayu Wulandari',
                'email' => 'ayuu17@gmail.com',
                'password' => Hash::make('adminadmin'),
                'role' => 'admin',
                'status' => 'Aktif'
            ],
            [
                'name' => 'Projek Bertiga',
                'email' => 'projekbertiga@gmail.com',
                'password' => Hash::make('magang26'),
                'role' => 'safety officer',
                'status' => 'Aktif'
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // cek email
                $user // update atau insert
            );
        }
    }
}