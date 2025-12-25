<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris; // Pastikan Model ini sudah ada (nanti kita buat jika belum)
use Illuminate\Support\Facades\Storage;

class InventarisController extends Controller
{
    // Fungsi untuk menyimpan data (Store)
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama_barang' => 'required',
            'jumlah'      => 'required|integer',
            'kondisi'     => 'required',
            'bukti_foto'  => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Validasi foto
        ]);

        $nama_file = null;

        // 2. Logika Upload Foto
        // Cek apakah user mengupload file 'bukti_foto'
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            
            // Buat nama file unik (waktu + nama asli)
            $nama_file = time() . "_" . $file->getClientOriginalName();
            
            // Simpan file ke folder: storage/app/public/bukti
            // Nanti bisa diakses via: public/storage/bukti/nama_file
            $file->storeAs('public/bukti', $nama_file);
        }

        // 3. Simpan ke Database
        Inventaris::create([
            'nama_barang' => $request->nama_barang,
            'jumlah'      => $request->jumlah,
            'kondisi'     => $request->kondisi,
            'bukti_foto'  => $nama_file, // Hanya simpan nama filenya saja
        ]);

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Data inventaris berhasil disimpan!');
    }

    // Fungsi untuk menghapus data (Destroy)
    public function destroy($id)
    {
        // Cari barang berdasarkan ID
        $barang = Inventaris::findOrFail($id);

        // (Opsional) Hapus foto dari folder jika ada, biar tidak menuhin memori
        if ($barang->bukti_foto && Storage::exists('public/bukti/' . $barang->bukti_foto)) {
            Storage::delete('public/bukti/' . $barang->bukti_foto);
        }

        // Hapus data dari database
        $barang->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}