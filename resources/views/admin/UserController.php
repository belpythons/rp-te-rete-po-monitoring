<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    // --- MANAJEMEN USER (TETAP LENGKAP) ---

    public function index()
    {
        $users = User::latest()->paginate(5);
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'role' => 'required',
            'department' => 'required',
            'sub_department' => 'required',
            'status' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'sub_department' => $request->sub_department,
            'role' => $request->role,
            'status' => $request->status ?? 'active',
        ]);

        return redirect('/admin/users')->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required',
            'department' => 'required',
            'sub_department' => 'required',
            'status' => 'required'
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department,
            'sub_department' => $request->sub_department,
            'status' => $request->status ?? 'active'
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect('/admin/users')->with('success', 'User berhasil diupdate');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->foto && File::exists(public_path('uploads/' . $user->foto))) {
            File::delete(public_path('uploads/' . $user->foto));
        }

        $user->delete();
        return redirect('/admin/users')->with('success', 'User berhasil dihapus');
    }


    // --- UPDATE PROFIL (BAGIAN PROFIL ADMIN) ---

    public function profile()
    {
        // Pastikan admin sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id()); // Pakai cara ini lebih aman dibanding Auth::user() langsung

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'foto'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($user->foto && File::exists(public_path('uploads/' . $user->foto))) {
                File::delete(public_path('uploads/' . $user->foto));
            }

            // Upload foto baru
            $file = $request->file('foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            
            // Cek folder uploads, jika tidak ada buat otomatis
            if (!File::isDirectory(public_path('uploads'))) {
                File::makeDirectory(public_path('uploads'), 0777, true, true);
            }

            $file->move(public_path('uploads'), $nama_file);
            $user->foto = $nama_file;
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        
        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }

    // --- DETAIL PERMIT (TAMBAHAN) ---
public function detail_permit($id)
{
    $permit = [
        'nomor' => 'PRM-009',
        'status' => 'Ditolak',

        'jenis' => 'Permit Confined Space',
        'tgl_kerja' => '03 April 2026',
        'pekerjaan' => 'Pemindahan bahan kimia',
        'waktu' => '11.00 - 13.00',

        'nama' => 'Dinda Cahya',
        'departemen' => 'Logistics Dept',
        'section' => 'Warehouse',
        'supervisor' => 'Puspitasari Alfaris',

        'gedung' => 'Klorin Unit',
        'lokasi' => 'UNIT 1200 – FIRE TANK SYSTEM (Storage 3)',

        'deskripsi' => 'Pemindahan bahan kimia cair dengan prosedur khusus.',

        'apd' => ['Masker'],

        'risiko' => 'Risiko Tinggi',

        'catatan' => [
            'supervisor' => 'Ditolak karena APD tidak lengkap.',
            'safety' => ''
        ]
    ];

    return view('admin.detail_permit', compact('permit'));
}
}