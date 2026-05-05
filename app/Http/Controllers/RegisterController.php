<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{

    // tampilkan halaman register
    public function showRegister()
    {
        return view('register');
    }

    // proses register
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        // simpan user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pekerja',
            'status' => 'Aktif' // 🔥 hanya ini tambahan
        ]);

        // login otomatis
        Auth::login($user);

        // arahkan ke dashboard pekerja
        return redirect('/pekerja/dashboard');

    }

}