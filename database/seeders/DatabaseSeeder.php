<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Procurement;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Admin exists
        User::updateOrCreate(
            ['email' => 'admin@outlook.com'],
            [
                'name' => 'Admin KMI',
                'password' => bcrypt('adminkmi123'),
            ]
        );

        // 2. Clear out other records to ensure a clean starting point
        Schema::disableForeignKeyConstraints();
        Procurement::truncate();
        Schema::enableForeignKeyConstraints();

        // 3. Call the master ProcurementSeeder
        $this->call([
            ProcurementSeeder::class,
        ]);
    }
}
