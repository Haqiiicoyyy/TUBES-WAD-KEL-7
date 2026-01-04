<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required',
            'ruangan'       => 'required',
            'tanggal'       => 'required|date',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'keperluan'     => 'required',
            'no_hp'         => 'required', 
        ]);

        // --- API CALENDARIFIC START (Validasi Hari Libur) ---
        $cekLibur = $this->cekHariLiburAPI($request->tanggal);
        
        if ($cekLibur['is_holiday']) {
            return back()->withErrors(['tanggal' => 'Gagal! Tanggal tersebut adalah hari libur: ' . $cekLibur['name']]);
        }

        $peminjaman = Peminjaman::create([
            'nama_peminjam' => $request->nama_peminjam,
            'ruangan'       => $request->ruangan,
            'tanggal'       => $request->tanggal,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'keperluan'     => $request->keperluan,
            'status'        => 'menunggu',
            'no_hp'         => $request->no_hp 
        ]);

        // --- API QR & WA ---
        $qrData = "ID:{$peminjaman->id}|{$request->nama_peminjam}|{$request->ruangan}";
        $linkQR = "https://quickchart.io/qr?text=" . urlencode($qrData) . "&size=300";

        $pesan  = "*NOTIFIKASI SIP-CACUK*\n\n";
        $pesan .= "Halo *{$request->nama_peminjam}*,\n";
        $pesan .= "Pengajuan peminjaman ruangan berhasil dikirim.\n\n";
        $pesan .= "📅 Tgl: {$request->tanggal}\n";
        $pesan .= "🏠 Ruang: {$request->ruangan}\n";
        $pesan .= "⏳ Status: *MENUNGGU KONFIRMASI*\n\n";
        $pesan .= "Simpan QR Code ini sebagai bukti:\n" . $linkQR;

        $this->kirimPesanWA($request->no_hp, $pesan);

        return redirect()->back()->with('success', 'Pengajuan Berhasil! Notifikasi WhatsApp telah dikirim.');
    }

    public function acc($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'disetujui']);
        
        if(!empty($peminjaman->no_hp)) {
            $pesan = "*DISETUJUI!* 🟢\n\nHalo *$peminjaman->nama_peminjam*,\nPeminjaman ruangan *$peminjaman->ruangan* tgl *$peminjaman->tanggal* telah DISETUJUI.";
            $this->kirimPesanWA($peminjaman->no_hp, $pesan);
        }
        return redirect()->back()->with('success', 'Peminjaman Disetujui!');
    }

    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'ditolak']);
        
        if(!empty($peminjaman->no_hp)) {
            $pesan = "*DITOLAK* 🔴\n\nHalo *$peminjaman->nama_peminjam*,\nPeminjaman ruangan *$peminjaman->ruangan* tgl *$peminjaman->tanggal* DITOLAK.";
            $this->kirimPesanWA($peminjaman->no_hp, $pesan);
        }
        return redirect()->back()->with('success', 'Peminjaman Ditolak!');
    }

    public function update(Request $request, $id)
    {
        $pinjam = Peminjaman::findOrFail($id);
        if($pinjam->status != 'menunggu') return back()->withErrors(['error' => 'Tidak bisa edit.']);

        $pinjam->update([
            'ruangan' => $request->ruangan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keperluan' => $request->keperluan
        ]);
        return redirect()->back()->with('success', 'Data Updated!');
    }

    private function cekHariLiburAPI($tanggalInput)
    {
        $tahun = date('Y', strtotime($tanggalInput));
        
        $apiKey = 'CItwYN5dZGIHqwCpfvjaqhuIfHCJuckC'; 

        $url = "https://calendarific.com/api/v2/holidays?&api_key=$apiKey&country=ID&year=$tahun";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        if (isset($data['response']['holidays'])) {
            foreach ($data['response']['holidays'] as $libur) {
                // iso format: 2025-12-25
                if ($libur['date']['iso'] == $tanggalInput) {
                    return [
                        'is_holiday' => true, 
                        'name' => $libur['name'] 
                    ]; 
                }
            }
        }

        return ['is_holiday' => false, 'name' => ''];
    }

    private function kirimPesanWA($nomor, $pesan)
    {
        $token = 'pFYsfhAFYpmRTQfMYwjz'; 
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array('target' => $nomor, 'message' => $pesan, 'countryCode' => '62'),
            CURLOPT_HTTPHEADER => array("Authorization: $token"),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}