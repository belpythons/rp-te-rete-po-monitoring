<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    // tampilkan halaman manajemen user
    public function index()
    {
        $users = User::latest()->paginate(5);

        return view('admin.users', compact('users'));
    }


    // tambah user
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5',
            'role' => 'required',
            'department' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department
        ]);

        return redirect('/admin/users')->with('success','User berhasil ditambahkan');
    }


    // update user
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'department' => 'required'
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department
        ]);

        return redirect('/admin/users')->with('success','User berhasil diupdate');
    }


    // hapus user
    public function delete($id)
    {

        $user = User::findOrFail($id);

        $user->delete();

        return redirect('/admin/users')->with('success','User berhasil dihapus');
    }

}