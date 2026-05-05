<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return redirect('/login');
});


/*
| LOGIN
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

/* LOGOUT */
Route::post('/logout', [AuthController::class, 'logout']);


/*
| REGISTER
*/
Route::get('/register', [RegisterController::class,'showRegister']);
Route::post('/register', [RegisterController::class,'register']);


/*
| DASHBOARD PEKERJA
*/
Route::get('/pekerja/dashboard', function () {
    return view('pekerja.dashboard');
})->middleware('auth');

/*
| BUAT PERMIT (FORM)
*/
Route::get('/pekerja/buat_permit', function () {

    return view('pekerja.buat_permit');

})->middleware('auth');

/*
| DETAIL PERMIT PEKERJA
*/
Route::get('/pekerja/detail_permit/{id}', function ($id) {

    $permits = [

        1 => [
            'nomor'=>'PRM-001',
            'nama'=>'Raka Pratama',
            'jenis'=>'Hot Work',
            'tgl_pengajuan'=>'04 Mar 2026',
            'tgl_kerja'=>'05 Mar 2026',
            'status'=>'Pending',
            'departemen'=>'Maintenance Dept',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Produksi',
            'area'=>'Mesin Produksi',
            'lokasi'=>'Line 1',
            'deskripsi'=>'Pengelasan rangka mesin',
            'risiko'=>'Risiko Tinggi',
            'apd'=>['Helm','Sarung tangan','Sepatu safety'],
        ],

        2 => [
            'nomor'=>'PRM-002',
            'nama'=>'Dinda Cahya',
            'jenis'=>'Cold Work',
            'tgl_pengajuan'=>'03 Mar 2026',
            'tgl_kerja'=>'04 Mar 2026',
            'status'=>'Disetujui',
            'departemen'=>'Logistics Dept',
            'section'=>'Warehouse',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Gudang',
            'area'=>'Penyimpanan',
            'lokasi'=>'Blok B',
            'deskripsi'=>'Perbaikan rak',
            'risiko'=>'Risiko Sedang',
            'apd'=>['Helm','Sepatu safety'],
        ],

    ];

    $permit = $permits[$id] ?? null;

    if(!$permit){
        abort(404);
    }

    return view('pekerja.detail_permit', compact('permit'));

})->middleware('auth')->name('pekerja.detail');

/*
| DASHBOARD ADMIN
*/
Route::get('/admin/dashboard', function () {

    $permits = [

        // monitoring
        ['jenis'=>'Confined Space'],
        ['jenis'=>'Hot Work Permit'],
        ['jenis'=>'Cold Work Permit'],

        // riwayat
        ['jenis'=>'Permit Confined Space'],
        ['jenis'=>'Cold Work Permit'],
        ['jenis'=>'Listrik & Instrument'],
        ['jenis'=>'Cold Work Permit'],
        ['jenis'=>'Hot Work Permit'],
        ['jenis'=>'Cold Work Permit'],
        ['jenis'=>'Hot Work Permit'],
    ];

    $chartData = [
        'hot' => 0,
        'cold' => 0,
        'penggalian' => 0,
        'listrik' => 0,
        'kendaraan' => 0,
        'confined' => 0,
        'kompresor' => 0,
    ];

    foreach($permits as $permit){
        $jenis = strtolower($permit['jenis']);

        if(str_contains($jenis,'hot')){
            $chartData['hot']++;
        } elseif(str_contains($jenis,'cold')){
            $chartData['cold']++;
        } elseif(str_contains($jenis,'penggalian')){
            $chartData['penggalian']++;
        } elseif(str_contains($jenis,'listrik')){
            $chartData['listrik']++;
        } elseif(str_contains($jenis,'kendaraan')){
            $chartData['kendaraan']++;
        } elseif(str_contains($jenis,'confined')){
            $chartData['confined']++;
        } elseif(str_contains($jenis,'kompresor')){
            $chartData['kompresor']++;
        }
    }

    return view('admin.dashboard', compact('chartData'));

})->middleware('auth')->name('admin.dashboard');


/*
| DASHBOARD SUPERVISOR
*/
Route::get('/supervisor/dashboard', function () {
    return view('supervisor.dashboard');
})->middleware('auth');


/*
| DASHBOARD SAFETY
*/
Route::get('/safety/dashboard', function () {
    return view('safety_officer.dashboard');
})->middleware('auth');


/*
| MANAJEMEN USER
*/
Route::get('/admin/users', function () {
    $users = User::all();
    return view('admin.users', compact('users'));
})->middleware('auth');


/*
| TAMBAH USER
*/
Route::post('/admin/users/store', function (Request $request) {

    User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'department' => $request->department,
        'sub_department' => $request->sub_department, // 🔥 WAJIB
        'role' => $request->role,
        'status' => $request->status,
    ]);

    return redirect('/admin/users')->with('success','User berhasil ditambahkan');

})->middleware('auth');


/*
| EDIT USER
*/
Route::get('/admin/users/edit/{id}', function ($id) {

$user = User::find($id);

$users = User::all();
return view('admin.users', compact('user','users'));

})->middleware('auth');

/*
| UPDATE USER
*/
Route::post('/admin/users/update/{id}', function (Request $request,$id) {

    $user = User::find($id);

    if($user){
        $user->update([
            'name'=>$request->name,
            'username'=>$request->username,
            'email'=>$request->email,
            'department'=>$request->department,
            'sub_department'=>$request->sub_department, // 🔥 TAMBAH INI
            'role'=>$request->role,
            'status'=>$request->status
        ]);
    }

    return redirect('/admin/users')->with('success','User berhasil diupdate');

})->middleware('auth');


/*
| HAPUS USER
*/
Route::delete('/admin/users/delete/{id}', function ($id) {

    $user = User::find($id);

    if($user){
        $user->delete();
    }

    return redirect('/admin/users')->with('success','User berhasil dihapus');

})->middleware('auth');

/*
| MONITORING PERMIT (HALAMAN UTAMA)
*/
Route::get('/admin/monitoring_permit', function () {

    $permits = [
    [
        'id'=>10,
        'nomor'=>'PRM-010',
        'nama'=>'Mira Agustiansyah',
        'lokasi'=>'UNIT 1200 - FIRE',
        'jenis'=>'Confined Space',
        'tanggal'=>'11 April 2026', // ✅ FIX
        'status'=>'Pending',
    ],
    [
        'id'=>8,
        'nomor'=>'PRM-008',
        'nama'=>'Yoga Prasetya',
        'lokasi'=>'UNIT 1500',
        'jenis'=>'Hot Work Permit',
        'tanggal'=>'19 Maret 2026', // ✅ FIX
        'status'=>'Pending',
    ],
    [
        'id'=>7,
        'nomor'=>'PRM-007',
        'nama'=>'Mira Agustiansyah',
        'lokasi'=>'UNIT 700 - MAIN',
        'jenis'=>'Cold Work Permit',
        'tanggal'=>'06 Maret 2026', // ✅ FIX
        'status'=>'Disetujui',
    ],
];

    return view('admin.monitoring_permit', compact('permits'));

})->middleware('auth')->name('admin.monitoring');

/*
| RIWAYAT PERMIT (HALAMAN UTAMA)
*/
Route::get('/admin/riwayat_permit', function () {

    $permits = [

        ['nomor'=>'PRM-009','nama'=>'Dinda Cahya','lokasi'=>'UNIT 1200 - FIRE TANK SYSTEM','jenis'=>'Permit Confined Space','tanggal'=>'03 April 2026','status'=>'Ditolak'],
        ['nomor'=>'PRM-006','nama'=>'Fikri Ramadhan','lokasi'=>'UNIT 700 - MAIN','jenis'=>'Cold Work Permit','tanggal'=>'02 Maret 2026','status'=>'Selesai'],
        ['nomor'=>'PRM-005','nama'=>'Mira Agustiansyah','lokasi'=>'UNIT 1500 - CONTROL','jenis'=>'Listrik & Instrument','tanggal'=>'21 Februari 2026','status'=>'Selesai'],
        ['nomor'=>'PRM-004','nama'=>'Bima Saputra','lokasi'=>'UNIT 1500 - CONTROL','jenis'=>'Cold Work Permit','tanggal'=>'11 Februari 2026','status'=>'Ditolak'],
        ['nomor'=>'PRM-003','nama'=>'Mira Agustiansyah','lokasi'=>'UNIT 1500 - CONTROL','jenis'=>'Hot Work Permit','tanggal'=>'06 Februari 2026','status'=>'Selesai'],
        ['nomor'=>'PRM-002','nama'=>'Dinda Cahya','lokasi'=>'UNIT 1150 - LOADING','jenis'=>'Cold Work Permit','tanggal'=>'29 Januari 2026','status'=>'Selesai'],
        ['nomor'=>'PRM-001','nama'=>'Mira Agustiansyah','lokasi'=>'UNIT 1500 - CONTROL','jenis'=>'Hot Work Permit','tanggal'=>'16 Januari 2026','status'=>'Selesai'],

    ];

    return view('admin.riwayat_permit', compact('permits'));

})->middleware('auth')->name('admin.riwayat');

/*
| DETAIL MONITORING PERMIT (FIX LENGKAP)
*/
Route::get('/admin/monitoring/detail/{id}', function ($id) {

    $permits = [

        10 => [
            'id'=>10,
            'nomor'=>'PRM-010',
            'nama'=>'Mira Agustiansyah',
            'pekerjaan'=>'Pemindahan bahan kimia',
            'waktu'=>'08:00 - 10:00',
            'departemen'=>'Department Dept',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Klorin Unit',
            'area'=>'Tangki',
            'lokasi'=>'UNIT 1200 - FIRE TANK SYSTEM',
            'jenis'=>' PermitConfined Space',
            'tgl_kerja'=>'11 April 2026',
            'deskripsi'=>'Distribusi bahan kimia ke lokasi penyimpanan',
            'risiko'=>'Risiko Tinggi',
            'apd'=>['APD lengkap'],
            'catatan'=>[
                'supervisor'=>' ',
                'safety'=>''
            ],
            'status'=>'Pending',
        ],

        8 => [
            'id'=>8,
            'nomor'=>'PRM-008',
            'nama'=>'Yoga Prasetya',
            'pekerjaan'=>'Pengelasan tangka mesin',
            'waktu'=>'10:00 - 15:00',
            'departemen'=>'Operaation Dept',
            'section'=>'Utility',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'CB (Control Building)',
            'area'=>'Workshop',
            'lokasi'=>'UNIT 1500 - CONTROL BUILDING (Line 1)',
            'jenis'=>'Hot Work Permit',
            'tgl_kerja'=>'19 Maret 2026',
            'deskripsi'=>'Pemotongan besi untuk perbaikan komponen mesin',
            'risiko'=>'Risiko Tinggi',
            'apd'=>['Helm','Sarung tangan'],
            'catatan'=>[
                'supervisor'=>'',
                'safety'=>''
            ],
            'status'=>'Pending',
        ],

        7 => [
            'id'=>7,
            'nomor'=>'PRM-007',
            'nama'=>'Mira Agustiansyah',
            'pekerjaan'=>'Pemotongan besi',
            'waktu'=>'09:00 - 12:00',
            'departemen'=>'Maintenance Dept',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Maintenance',
            'area'=>'Pipa Utama',
            'lokasi'=>'UNIT 700 - MAIN POWER GENERATOR (Area Las)',
            'jenis'=>'Cold Work Permit',
            'tgl_kerja'=>'06 Maret 2026',
            'deskripsi'=>'Pemotongan besi untuk perbaikan komponen mesin',
            'risiko'=>'Risiko Rendah',
            'apd'=>['Helm','Sarung tangan'],
            'catatan'=>[
                'supervisor'=>'Pekerjaan dapat dilakukan sesuai prosedur',
                'safety'=>'Pastikan areaaman dari percikan api selama bekerja'
            ],
            'status'=>'Disetujui',
        ],

    ];

    $permit = $permits[$id] ?? null;

    if(!$permit){
        abort(404);
    }

    return view('admin.detail_permit', compact('permit'));

})->middleware('auth')->name('admin.monitoring.detail');

/*
| TAMBAHAN 
*/
Route::get('/admin/detail_permit/{nomor}', function ($nomor) {

    $data = [
        
    'PRM-009' => [
        'nomor'=>'PRM-009',
        'status'=>'Ditolak',
        'jenis'=>'Permit Confined Space',
        'tgl_kerja'=>'03 April 2026',
        'pekerjaan'=>'Pemindahan bahan kimia',
        'waktu'=>'11.00 - 13.00',
        'nama'=>'Dinda Cahya',
        'departemen'=>'Logistics Dept',
        'section'=>'Warehouse',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'Klorin Unit',
        'lokasi'=>'UNIT 1200 – FIRE TANK SYSTEM (Storage 3)',
        'deskripsi'=>'Pemindahan bahan kimia cair dengan prosedur khusus.',
        'apd'=>['Masker'],
        'risiko'=>'Risiko Tinggi',
    'catatan'=>[
        'supervisor'=>'Ditolak karena APD tidak lengkap.',
        'safety'=>''
    ]
],

        'PRM-006' => [
        'nomor'=>'PRM-006',
        'status'=>'Selesai',
        'jenis'=>'Cold Work Permit',
        'tgl_kerja'=>'02 Maret 2026',
        'pekerjaan'=>'Pemindahan bahan kimia',
        'waktu'=>'08.00 - 10.00',
        'nama'=>'Fikri Ramadhan',
        'departemen'=>'Maintenance Dept',
        'section'=>'MPC',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'Maintenance',
        'lokasi'=>'UNIT 700 – MAIN POWER GENERATOR (Area Las)',
        'deskripsi'=>'Pemotongaan material besi untuk kebutuhan rangka mesin.',
        'apd'=>['Helm','Sarung tangan'],
        'risiko'=>'Risiko Rendah',
    'catatan'=>[
        'supervisor'=>'Pastikan alat dalam kondisi baik sebelum digunakan.',
        'safety'=>'Gunakan perlindungan mata selama proses pemotongan.'
    ]
],

        'PRM-005' => [
        'nomor'=>'PRM-005',
        'status'=>'Selesai',
        'jenis'=>'Listrik & Instrument',
        'tgl_kerja'=>'21 Februari 2026',
        'pekerjaan'=>'Perbaikan Panel Listrik',
        'waktu'=>'13.00 - 15.00',
        'nama'=>'Mira Agustiansyah',
        'departemen'=>'Maintenance Dept',
        'section'=>'Mechanic',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'CB (Control Building)',
        'lokasi'=>'UNIT 1500 – CONTROL BUILDING (Control Room)',
        'deskripsi'=>'Perbaikan ssistem panel listrik untuk memastikan distribusi daya berjalan normal.',
        'apd'=>['APD Lengkap','Lockout '],
        'risiko'=>'Risiko Sedang',
    'catatan'=>[
        'supervisor'=>'Pastikan prosedur perbaikan sesuai standar operasional.',
        'safety'=>'Prosedur lockout berjalan dengan baik dan aman.'
    ]
],

        'PRM-004' => [
        'nomor'=>'PRM-004',
        'status'=>'Ditolak',
        'jenis'=>'Cold Work Permit',
        'tgl_kerja'=>'11 Februari 2026',
        'pekerjaan'=>'Perbaiki Atap',
        'waktu'=>'09.00 - 11.00',
        'nama'=>'Bima Saputra',
        'departemen'=>'Maintenance Dept',
        'section'=>'Electrical & Instrument',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'Adminstation Building',
        'lokasi'=>'UNIT 1500 – CONTROL BUILDING (Gedung A)',
        'deskripsi'=>'Perbaikan atap bangunan yang mengalami kebocoran.',
        'apd'=>['Helm'],
        'risiko'=>'Risiko Tinggi',
    'catatan'=>[
        'supervisor'=>'Pekerjaan ditolak karna perlengkapan keselamatan belum lengkap.',
        'safety'=>''
    ]
],

        'PRM-003' => [
        'nomor'=>'PRM-003',
        'status'=>'Selesai',
        'jenis'=>'Hot Work Permit',
        'tgl_kerja'=>'06 Februari 2026',
        'pekerjaan'=>'Pengelasan rangka mesin',
        'waktu'=>'08.00 - 14.00',
        'nama'=>'Mira Agustiansyah',
        'departemen'=>'Maintenance Dept',
        'section'=>'Mechanic',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'CB (Control Building)',
        'lokasi'=>'UNIT 1500 – CONTROL BUILDING (Line 1)',
        'deskripsi'=>'Perbaikan rangka mesin dengan metode pengelasan pada bagian yang retak.',
        'apd'=>['Helm', 'Sarung tangan', 'Sepatu safety'],
        'risiko'=>'Risiko Tinggi',
    'catatan'=>[
        'supervisor'=>'Pastikan area kerja aman sebelum dilakukan pengelasan ulang.',
        'safety'=>'Area kerja dalam kondisi aman dan terkendali selama proses berlangsung.'
    ]
],

        'PRM-002' => [
        'nomor'=>'PRM-002',
        'status'=>'Selesai',
        'jenis'=>'Cold Work Permit',
        'tgl_kerja'=>'29 Januari 2026',
        'pekerjaan'=>'Perbaikan rak gudang',
        'waktu'=>'10.00 - 13.00',
        'nama'=>'Dinda Cahya',
        'departemen'=>'Logistics Dept',
        'section'=>'Warehouse',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'Warehouse',
        'lokasi'=>'UNIT 11500 – LOADING ARM SYSTEM (Blok B)',
        'deskripsi'=>'Perbaiki rak gudang yang mengalami kemiringan akibat beban berlebih.',
        'apd'=>['Helm', 'Sarung tangan', 'Sepatu safety'],
        'risiko'=>'Risiko Sedang',
    'catatan'=>[
        'supervisor'=>'Pastikan struktur rak diperiksa sebelum dilakukan perbaikan.',
        'safety'=>'Pastikan area kerja aman dan gunakan APD lengkap selama pekerjaan berlangsung.'
    ]
],

        'PRM-001' => [
        'nomor'=>'PRM-001',
        'status'=>'Selesai',
        'jenis'=>'Hot Work Permit',
        'tgl_kerja'=>'16 Januari 2026',
        'pekerjaan'=>'Pengelasan rangka mesin',
        'waktu'=>'09.00 - 12.00',
        'nama'=>'Mira Agustiansyah',
        'departemen'=>'Maintenance Dept',
        'section'=>'Mechanic',
        'supervisor'=>'Puspitasari Alfaris',
        'gedung'=>'CB (Control Building)',
        'lokasi'=>'UNIT 1500 – CONTROL BUILDING (Line 1)',
        'deskripsi'=>'Pengelasan pada rangka mesin yang mengalami retak akibat getaran operasinal .',
        'apd'=>['Helm', 'Sarung tangan', 'Sepatu safety'],
        'risiko'=>'Risiko Tinggi',
    'catatan'=>[
        'supervisor'=>'Pastikan area kerja steril dari maaterial mudah terbakar sebelum pengelasan dimulai.',
        'safety'=>'Wajib menyediakan APAR sebelum pekerjaan dimulai dan memastikan area aman dari percikan api.'
    ]
],

    ];

    $permit = $data[$nomor] ?? null;

    if(!$permit){
        abort(404);
    }

    return view('admin.detail_riwayat', compact('permit'));

})->middleware('auth');

/*
| EDIT PERMIT PEKERJA (FULL)
*/
Route::get('/pekerja/edit_permit/{id}', function ($id) {

    // DATA DUMMY (bisa nanti diganti database)
    $permits = [
        1 => [
            'jenis' => 'Permit Confined Space',
            'pekerjaan' => 'Perbaikan Panel Listrik',
            'deskripsi' => 'Proses distribusi bahan kimia cair ke lokasi penyimpanan sesuai kebutuhan operasional.',
            'tanggal' => '2026-04-11',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'gedung' => 'Gudang',
            'lokasi' => 'Storage 3',
            'risiko' => 'Risiko Tinggi',
            'safety' => ['Helm','Sarung Tangan','Sepatu Safety'],
            'lainnya' => 'Masker'
        ]
    ];

    $permit = $permits[$id] ?? null;

    if(!$permit){
        abort(404);
    }

    return view('pekerja.edit_permit', compact('permit','id'));

})->middleware('auth');

/*
| SIMPAN PERMIT 
*/
Route::post('/pekerja/simpan_permit', function (Illuminate\Http\Request $request) {

    return response()->json([
        'success' => true,
        'data' => [
            'id' => rand(1,999),
            'no_permit' => 'PRM-' . rand(100,999),
            'pekerjaan' => $request->pekerjaan,
            'lokasi' => $request->lokasi,
            'tanggal' => $request->tanggal,
        ]
    ]);

})->middleware('auth');

/*
| LAPORAN
*/
Route::get('/admin/laporan', function () {
    return view('admin.laporan');
})->middleware('auth');

/*
| DETAIL LAPORAN
*/
Route::get('/admin/laporan/detail/{nomor}', function ($nomor) {
    $data = [

        'PRM-010' => [
            'nomor'=>'PRM-010',
            'status'=>'Pending',
            'jenis'=>'Confined Space',
            'tgl_kerja'=>'11 April 2026',
            'pekerjaan'=>'Pemindahan bahan kimia',
            'waktu'=>'08:00 - 10:00',
            'nama'=>'Mira Agustiansyah',
            'departemen'=>'Department Dept',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Klorin Unit',
            'area'=>'Tangki',
            'lokasi'=>'UNIT 1200 - FIRE TANK SYSTEM',
            'deskripsi'=>'Distribusi bahan kimia ke lokasi penyimpanan',
            'apd'=>['APD lengkap'],
            'risiko'=>'Risiko Tinggi',
            'catatan'=>[
                'supervisor'=>'',
                'safety'=>''
            ]
        ],

        'PRM-009' => [
            'nomor'=>'PRM-009',
            'status'=>'Ditolak',
            'jenis'=>'Permit Confined Space',
            'tgl_kerja'=>'03 April 2026',
            'pekerjaan'=>'Pemindahan bahan kimia',
            'waktu'=>'11.00 - 13.00',
            'nama'=>'Dinda Cahya',
            'departemen'=>'Logistics Dept',
            'section'=>'Warehouse',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Klorin Unit',
            'lokasi'=>'UNIT 1200 – FIRE TANK SYSTEM',
            'deskripsi'=>'Pemindahan bahan kimia cair dengan prosedur khusus.',
            'apd'=>['Masker'],
            'risiko'=>'Risiko Tinggi',
            'catatan'=>[
                'supervisor'=>'Ditolak karena APD tidak lengkap.',
                'safety'=>''
            ]
        ],

        'PRM-008' => [
            'nomor'=>'PRM-008',
            'status'=>'Pending',
            'jenis'=>'Hot Work Permit',
            'tgl_kerja'=>'19 Maret 2026',
            'pekerjaan'=>'Pengelasan tangka mesin',
            'waktu'=>'10:00 - 15:00',
            'nama'=>'Yoga Prasetya',
            'departemen'=>'Operation Dept',
            'section'=>'Utility',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'CB (Control Building)',
            'area'=>'Workshop',
            'lokasi'=>'UNIT 1500 - CONTROL BUILDING (Line 1)',
            'deskripsi'=>'Pemotongan besi untuk perbaikan komponen mesin',
            'apd'=>['Helm','Sarung tangan'],
            'risiko'=>'Risiko Tinggi',
            'catatan'=>[
                'supervisor'=>'',
                'safety'=>''
            ]
        ],


        'PRM-007' => [
            'nomor'=>'PRM-007',
            'status'=>'Disetujui',
            'jenis'=>'Cold Work Permit',
            'tgl_kerja'=>'06 Maret 2026',
            'pekerjaan'=>'Pemotongan besi',
            'waktu'=>'09:00 - 12:00',
            'nama'=>'Mira Agustiansyah',
            'departemen'=>'Maintenance Dept',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Maintenance',
            'area'=>'Pipa Utama',
            'lokasi'=>'UNIT 700 - MAIN POWER GENERATOR',
            'deskripsi'=>'Pemotongan besi untuk perbaikan komponen mesin',
            'apd'=>['Helm','Sarung tangan'],
            'risiko'=>'Risiko Rendah',
            'catatan'=>[
                'supervisor'=>'Pekerjaan dapat dilakukan sesuai prosedur',
                'safety'=>'Pastikan area aman dari percikan api'
            ]
        ],

        'PRM-006' => [
            'nomor'=>'PRM-006',
            'status'=>'Selesai',
            'jenis'=>'Cold Work Permit',
            'tgl_kerja'=>'02 Maret 2026',
            'pekerjaan'=>'Pemindahan bahan',
            'waktu'=>'08.00 - 10.00',
            'nama'=>'Fikri Ramadhan',
            'departemen'=>'Maintenance Dept',
            'section'=>'MPC',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Maintenance',
            'lokasi'=>'UNIT 700 – MAIN',
            'deskripsi'=>'Pemotongan material besi',
            'apd'=>['Helm','Sarung tangan'],
            'risiko'=>'Risiko Rendah',
            'catatan'=>[
                'supervisor'=>'Periksa alat',
                'safety'=>'Gunakan pelindung mata'
            ]
        ],

        'PRM-005' => [
            'nomor'=>'PRM-005',
            'status'=>'Selesai',
            'jenis'=>'Listrik & Instrument',
            'tgl_kerja'=>'21 Februari 2026',
            'pekerjaan'=>'Perbaikan Panel',
            'waktu'=>'13.00 - 15.00',
            'nama'=>'Mira Agustiansyah',
            'departemen'=>'Maintenance Dept',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Control Building',
            'lokasi'=>'UNIT 1500',
            'deskripsi'=>'Perbaikan panel listrik',
            'apd'=>['APD Lengkap'],
            'risiko'=>'Risiko Sedang',
            'catatan'=>[
                'supervisor'=>'Ikuti SOP',
                'safety'=>'Lockout aman'
            ]
        ],

        'PRM-004' => [
            'nomor'=>'PRM-004',
            'status'=>'Ditolak',
            'jenis'=>'Cold Work Permit',
            'tgl_kerja'=>'11 Februari 2026',
            'pekerjaan'=>'Perbaiki Atap',
            'waktu'=>'09.00 - 11.00',
            'nama'=>'Bima Saputra',
            'departemen'=>'Maintenance Dept',
            'section'=>'Electrical',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Admin Building',
            'lokasi'=>'UNIT 1500',
            'deskripsi'=>'Perbaikan atap bocor',
            'apd'=>['Helm'],
            'risiko'=>'Risiko Tinggi',
            'catatan'=>[
                'supervisor'=>'APD kurang',
                'safety'=>''
            ]
        ],

        'PRM-003' => [
            'nomor'=>'PRM-003',
            'status'=>'Selesai',
            'jenis'=>'Hot Work Permit',
            'tgl_kerja'=>'06 Februari 2026',
            'pekerjaan'=>'Pengelasan',
            'waktu'=>'08.00 - 14.00',
            'nama'=>'Mira Agustiansyah',
            'departemen'=>'Maintenance',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Control Building',
            'lokasi'=>'UNIT 1500',
            'deskripsi'=>'Perbaikan rangka mesin',
            'apd'=>['Helm','Sarung tangan','Sepatu safety'],
            'risiko'=>'Risiko Tinggi',
            'catatan'=>[
                'supervisor'=>'Area aman',
                'safety'=>'Aman terkendali'
            ]
        ],

        'PRM-002' => [
            'nomor'=>'PRM-002',
            'status'=>'Selesai',
            'jenis'=>'Cold Work Permit',
            'tgl_kerja'=>'29 Januari 2026',
            'pekerjaan'=>'Perbaikan Rak',
            'waktu'=>'10.00 - 13.00',
            'nama'=>'Dinda Cahya',
            'departemen'=>'Logistics',
            'section'=>'Warehouse',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Warehouse',
            'lokasi'=>'UNIT 1150',
            'deskripsi'=>'Perbaikan rak gudang',
            'apd'=>['Helm','Sarung tangan','Sepatu safety'],
            'risiko'=>'Risiko Sedang',
            'catatan'=>[
                'supervisor'=>'Periksa struktur',
                'safety'=>'Area aman'
            ]
        ],

        'PRM-001' => [
            'nomor'=>'PRM-001',
            'status'=>'Selesai',
            'jenis'=>'Hot Work Permit',
            'tgl_kerja'=>'16 Januari 2026',
            'pekerjaan'=>'Pengelasan',
            'waktu'=>'09.00 - 12.00',
            'nama'=>'Mira Agustiansyah',
            'departemen'=>'Maintenance',
            'section'=>'Mechanic',
            'supervisor'=>'Puspitasari Alfaris',
            'gedung'=>'Control Building',
            'lokasi'=>'UNIT 1500',
            'deskripsi'=>'Pengelasan rangka mesin',
            'apd'=>['Helm','Sarung tangan','Sepatu safety'],
            'risiko'=>'Risiko Tinggi',
            'catatan'=>[
                'supervisor'=>'Sterilkan area',
                'safety'=>'Siapkan APAR'
            ]
        ],

    ];

    $permit = $data[$nomor] ?? null;

    if(!$permit){
        abort(404);
    }

    return view('admin.detail_riwayat', compact('permit'));

})->middleware('auth');

/*
| HALAMAN PROFILE (WAJIB ADA)
*/
Route::get('/admin/profile', function () {
    return view('admin.profile');
})->middleware('auth')->name('admin.profile');

/*
| PROFILE 
*/
Route::post('/admin/update-profile', function (Request $request) {

    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'foto' => 'image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $user = auth()->guard('web')->user();

    $user->name = $request->name;
    $user->username = $request->username;
    $user->email = $request->email;

    if($request->hasFile('foto')){
        $file = $request->file('foto');
        $nama = time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $nama);

        $user->foto = $nama;
    }

    $user->save();

    return back()->with('success','Profil berhasil diupdate');

})->middleware('auth');


/*
| INSERT USER DEFAULT (FIX BCRYPT)
*/
Route::get('/generate-user', function () {

    User::updateOrCreate(
        ['email' => 'miraa@gmail.com'],
        [
            'name' => 'Mira Agustiansyah',
            'password' => Hash::make('pekerja123'),
            'role' => 'pekerja'
        ]
    );

    User::updateOrCreate(
        ['email' => 'pitaa@gmail.com'],
        [
            'name' => 'puspitasari Alfaris',
            'password' => Hash::make('supervisor123'),
            'role' => 'supervisor'
        ]
    );

    User::updateOrCreate(
        ['email' => 'ayuu17@gmail.com'],
        [
            'name' => 'Ayu Wulandari',
            'password' => Hash::make('adminadmin'),
            'role' => 'admin'
        ]
    );

    User::updateOrCreate(
        ['email' => 'projekbertiga@gmail.com'],
        [
            'name' => 'Projek Bertiga',
            'password' => Hash::make('magang26'),
            'role' => 'safety officer'
        ]
    );

    return;
});