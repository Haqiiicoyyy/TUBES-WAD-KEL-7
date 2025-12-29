<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris;
use Illuminate\Support\Facades\Storage;

class InventarisController extends Controller
{
    // 1. SIMPAN BARANG
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'jumlah'      => 'required|integer',
            'kondisi'     => 'required',
            'bukti_foto'  => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $nama_file = null;

        // Cek apakah ada upload foto
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            // Simpan ke folder: storage/app/public/bukti
            $file->storeAs('public/bukti', $nama_file);
        }

        Inventaris::create([
            'nama_barang' => $request->nama_barang,
            'jumlah'      => $request->jumlah,
            'kondisi'     => $request->kondisi,
            'bukti_foto'  => $nama_file,
        ]);

        return redirect()->back()->with('success', 'Barang Inventaris Berhasil Disimpan!');
    }

    // 2. HAPUS BARANG
    public function destroy($id)
    {
        $barang = Inventaris::findOrFail($id);

        // Hapus foto jika ada
        if ($barang->bukti_foto) {
            Storage::delete('public/bukti/' . $barang->bukti_foto);
        }

        $barang->delete();

        return redirect()->back()->with('success', 'Barang Inventaris Dihapus!');
    }

    public function update(Request $request, $id)
    {
        $barang = Inventaris::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required',
            'jumlah' => 'required|integer',
            'kondisi' => 'required'
        ]);

        $data = [
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'kondisi' => $request->kondisi
        ];

        // Cek jika ada foto baru
        if ($request->hasFile('bukti_foto')) {
            // Hapus foto lama
            if ($barang->bukti_foto && Storage::exists('public/bukti/' . $barang->bukti_foto)) {
                Storage::delete('public/bukti/' . $barang->bukti_foto);
            }
            
            // Upload foto baru
            $file = $request->file('bukti_foto');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->storeAs('public/bukti', $nama_file);
            
            $data['bukti_foto'] = $nama_file;
        }

        $barang->update($data);

        return redirect()->back()->with('success', 'Data Inventaris Berhasil Diupdate!');
    }
}