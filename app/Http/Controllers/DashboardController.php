<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Pastikan nama Model ini (Permit) sesuai dengan yang ada di folder app/Models Anda
use App\Models\Permit; 

class DashboardController extends Controller
{
    public function admin()
{
    $chartData = [
        'hot' => Permit::where('jenis_pekerjaan', 'Hot Work')->count(),
        'cold' => Permit::where('jenis_pekerjaan', 'Cold Work')->count(),
        'penggalian' => Permit::where('jenis_pekerjaan', 'Penggalian')->count(),
        'listrik' => Permit::where('jenis_pekerjaan', 'Listrik & Instrument')->count(),
        'kendaraan' => Permit::where('jenis_pekerjaan', 'Kendaraan & Alat Berat')->count(),
        'confined' => Permit::where('jenis_pekerjaan', 'Confined Space')->count(),
        'kompresor' => Permit::where('jenis_pekerjaan', 'Kompressor Oksigen')->count(),
    ];

    return view('admin.dashboard', compact('chartData'));
}

    public function pekerja()
    {
        return view('pekerja.dashboard');
    }

    public function supervisor()
    {
        return view('supervisor.dashboard');
    }

    public function safety()
    {
        return view('safety.dashboard');
    }
}