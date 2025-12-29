<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    // 1. SIMPAN PENGAJUAN PEMINJAMAN
    public function store(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required',
            'ruangan'       => 'required',
            'tanggal'       => 'required|date',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'keperluan'     => 'required',
        ]);

        // Cek Hari Libur 
        $hariLibur = ['2025-12-25', '2025-01-01'];
        if (in_array($request->tanggal, $hariLibur)) {
            return back()->withErrors(['tanggal' => 'Maaf, tanggal tersebut adalah hari libur.']);
        }

        Peminjaman::create([
            'nama_peminjam' => $request->nama_peminjam,
            'ruangan'       => $request->ruangan,
            'tanggal'       => $request->tanggal,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'keperluan'     => $request->keperluan,
            'status'        => 'menunggu'
        ]);

        return redirect()->back()->with('success', 'Pengajuan Peminjaman Berhasil Dikirim!');
    }

    // 2. ACC PEMINJAMAN (ADMIN)
    public function acc($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'disetujui']);
        
        return redirect()->back()->with('success', 'Peminjaman Disetujui!');
    }

    // 3. TOLAK PEMINJAMAN (ADMIN)
    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'ditolak']);
        
        return redirect()->back()->with('success', 'Peminjaman Ditolak!');
    }

    public function update(Request $request, $id)
    {
        $pinjam = Peminjaman::findOrFail($id);

        // Hanya boleh edit jika status masih menunggu
        if($pinjam->status != 'menunggu') {
            return back()->withErrors(['error' => 'Tidak bisa edit peminjaman yang sudah diproses.']);
        }

        $pinjam->update([
            'ruangan' => $request->ruangan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keperluan' => $request->keperluan
        ]);

        return redirect()->back()->with('success', 'Pengajuan Berhasil Diupdate!');
    }
}