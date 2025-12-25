<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman user.
     */
    public function index()
    {
        // Ambil riwayat peminjaman milik user yang login
        $peminjaman = Peminjaman::where('nama_peminjam', Auth::user()->name)->get();

        return view('peminjaman.index', compact('peminjaman'));
    }

    /**
     * Menyimpan pengajuan peminjaman baru.
     */
    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'tanggal'       => 'required|date',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'keperluan'     => 'required|string|max:500',
            'ruangan'       => 'required|string|max:100',
        ]);

        $tanggal = $validated['tanggal'];

        // --- Logika cek hari libur (sementara dummy) ---
        $hariLibur = [
            '2025-01-01', // Tahun Baru
            '2025-12-25', // Natal
        ];

        if (in_array($tanggal, $hariLibur)) {
            return back()->withErrors([
                'tanggal' => 'Tanggal yang dipilih adalah hari libur. Silakan pilih tanggal lain.'
            ]);
        }

        // Simpan data ke database
        Peminjaman::create([
            'nama_peminjam' => $validated['nama_peminjam'],
            'tanggal'       => $validated['tanggal'],
            'jam_mulai'     => $validated['jam_mulai'],
            'jam_selesai'   => $validated['jam_selesai'],
            'status'        => 'menunggu','Approved', // default
        ]);

        return redirect()->route('peminjaman.index')
                         ->with('success', 'Pengajuan peminjaman berhasil disimpan.');
    }

    /**
     * Admin menyetujui peminjaman.
     */
    public function acc($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'disetujui';
        $peminjaman->save();

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman disetujui.');
    }

    /**
     * Admin menolak peminjaman.
     */
    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'ditolak';
        $peminjaman->save();

        return redirect()->route('peminjaman.index')->with('error', 'Peminjaman ditolak.');
    }
}