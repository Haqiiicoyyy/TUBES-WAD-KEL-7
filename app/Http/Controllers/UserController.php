<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Menyimpan user baru ke database (SIP-CACUK).
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:255',
            'nim'      => 'required|string|unique:users,nim', // Pastikan NIM unik
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8', // Minimal 8 karakter
            'role'     => 'required|in:admin,mahasiswa', // Validasi sesuai ENUM migrasi
        ]);

        // 2. Simpan Data ke Database
        User::create([
            'name'     => $request->name,
            'nim'      => $request->nim,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Enkripsi Password
            'role'     => $request->role,
        ]);

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'User berhasil ditambahkan ke sistem SIP-CACUK!');
    }
    public function update(Request $request)
    {
        // 1. Ambil data user yang sedang login
        $user = Auth::user();

        // 2. Validasi Input
        $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed', // nullable: boleh kosong jika tidak ganti pass
        ]);

        // 3. Siapkan data yang akan diupdate (Nama selalu diupdate)
        $updateData = [
            'name' => $request->name,
        ];

        // 4. Cek apakah user mengisi kolom password
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi Update ke Database
        $user->update($updateData);

        // 6. Kembali ke halaman profil dengan notifikasi
        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}