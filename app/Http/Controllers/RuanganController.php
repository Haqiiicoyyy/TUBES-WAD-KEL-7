<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan; 

class RuanganController extends Controller
{
    // 1. TAMPILKAN HALAMAN DASHBOARD (Method ini dipanggil di route '/')
    public function index()
    {
        // Sebenarnya logic dashboard udah ada di routes/web.php
        // Tapi dibiarin kosong atau redirect agar tidak error jika dipanggil
        return redirect()->route('dashboard');
    }

    // 2. SIMPAN DATA RUANGAN BARU
    public function store(Request $request)
    {
        $request->validate([
            'kode_ruang' => 'required|unique:ruangans,kode_ruang',
            'nama_ruangan' => 'required',
            'kapasitas' => 'required|integer'
        ]);

        Ruangan::create([
            'kode_ruang' => $request->kode_ruang,
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
            'status' => 'Tersedia'
        ]);

        return redirect()->back()->with('success', 'Ruangan Berhasil Ditambahkan!');
    }

    // 3. HAPUS RUANGAN
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->back()->with('success', 'Ruangan Berhasil Dihapus!');
    }
}