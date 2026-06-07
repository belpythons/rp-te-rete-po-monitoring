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

        // 3. Seed the 9 designated production master rows
        $procurements = [
            [
                'kode_pengadaan' => '4100001263',
                'nama_barang'    => 'Microsoft Surface for DPGM',
                'vendor'         => 'Vendor Peripheral A',
                'tanggal_in'     => '2025-05-27',
                'tanggal_te'     => '2025-05-27',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '4000018155',
                'nama_barang'    => 'Renewal Autocad and Installation',
                'vendor'         => 'Vendor Software B',
                'tanggal_in'     => '2025-05-15',
                'tanggal_te'     => '2025-05-27',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '4000000030',
                'nama_barang'    => 'Software License',
                'vendor'         => 'Mandan Estina',
                'tanggal_in'     => '2026-01-11',
                'tanggal_te'     => '2026-01-12',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '4000002032',
                'nama_barang'    => 'Up Bandwidth On Demand',
                'vendor'         => 'Vendor Telekomunikasi',
                'tanggal_in'     => '2026-02-18',
                'tanggal_te'     => '2026-03-06',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '1000004315',
                'nama_barang'    => 'Penggantian Storage and Troubleshoot',
                'vendor'         => 'Vendor IT Solution',
                'tanggal_in'     => '2026-02-23',
                'tanggal_te'     => '2026-04-08',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '4000002052',
                'nama_barang'    => 'Installation CCTV Proof',
                'vendor'         => 'Vendor Security System',
                'tanggal_in'     => '2026-02-23',
                'tanggal_te'     => null,
                'status'         => 'RP',
            ],
            [
                'kode_pengadaan' => '4000000063',
                'nama_barang'    => 'Additional Storage 200GB',
                'vendor'         => 'Vendor Storage',
                'tanggal_in'     => '2026-03-05',
                'tanggal_te'     => '2026-03-11',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '4000000091',
                'nama_barang'    => 'AUTOCAD LT 2026',
                'vendor'         => 'Vendor Software B',
                'tanggal_in'     => '2026-02-24',
                'tanggal_te'     => '2026-04-17',
                'status'         => 'TE',
            ],
            [
                'kode_pengadaan' => '4000000105',
                'nama_barang'    => 'Renewal for Mail Security',
                'vendor'         => 'Vendor IT Solution',
                'tanggal_in'     => '2026-03-24',
                'tanggal_te'     => '2026-04-10',
                'status'         => 'TE',
            ],
        ];

        foreach ($procurements as $data) {
            Procurement::updateOrCreate(
                ['kode_pengadaan' => $data['kode_pengadaan']],
                $data
            );
        }
    }
}
