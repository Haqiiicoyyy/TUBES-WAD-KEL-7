<?php

namespace App\Http\Controllers;

use Illuminate\HttpA\Request;

class Controller extends BaseController
{
    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'tanggal'   => 'required|date',
            'keperluan' => 'required|string|max:500',
        ]);

        $tanggal = $validated['tanggal'];

        // --- Logika cek hari libur (sementara dummy) ---
        // Misalnya kita punya array hari libur (nanti bisa diganti dengan DB atau API)
        $hariLibur = [
            '2025-01-01', // Tahun Baru
            '2025-12-25', // Natal
        ];

        if (in_array($tanggal, $hariLibur)) {
            return back()->withErrors([
                'tanggal' => 'Tanggal yang dipilih adalah hari libur. Silakan pilih tanggal lain.'
            ]);
        }

        // Simpan data ke database (contoh model Peminjaman)
        // Peminjaman::create($validated);

        return redirect()->route('peminjaman.index')
                         ->with('success', 'Pengajuan peminjaman berhasil disimpan.');
    }
}