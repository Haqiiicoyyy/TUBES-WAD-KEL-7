<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // SIMPAN USER BARU
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nim'      => 'required|unique:users,nim',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,mahasiswa',
        ]);

        User::create([
            'name'     => $request->name,
            'nim'      => $request->nim,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Enkripsi Password NOTE: HARUS HASH TERLEBIH DAHULU
            'role'     => $request->role,
        ]);

        return redirect()->back()->with('success', 'User Berhasil Ditambahkan!');
    }
}