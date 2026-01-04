<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventaris;
use Illuminate\Support\Facades\Storage; 

class InventarisController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'jumlah'      => 'required|integer',
            'kondisi'     => 'required',
            'bukti_foto'  => 'nullable|image|mimes:jpg,png,jpeg|max:5120', 
        ]);

        $url_foto = null;

        if ($request->hasFile('bukti_foto')) {
            $url_foto = $this->uploadKeImgBB($request->file('bukti_foto'));
        }

        Inventaris::create([
            'nama_barang' => $request->nama_barang,
            'jumlah'      => $request->jumlah,
            'kondisi'     => $request->kondisi,
            'bukti_foto'  => $url_foto, 
        ]);

        return redirect()->back()->with('success', 'Barang Inventaris Berhasil Disimpan!');
    }

    public function destroy($id)
    {
        $barang = Inventaris::findOrFail($id);
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

        if ($request->hasFile('bukti_foto')) {
            // Upload ke ImgBB
            $url_baru = $this->uploadKeImgBB($request->file('bukti_foto'));
            $data['bukti_foto'] = $url_baru;
        }

        $barang->update($data);

        return redirect()->back()->with('success', 'Data Inventaris Berhasil Diupdate!');
    }


    private function uploadKeImgBB($imageFile)
    {
        $apiKey = '8306330a5261756cebbe8c8f3f107bda'; 

        $data = file_get_contents($imageFile->path());
        $base64 = base64_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.imgbb.com/1/upload?key='.$apiKey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
                'image' => $base64
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        if (isset($result['data']['url'])) {
            return $result['data']['url'];
        }

        return null; 
    }
}