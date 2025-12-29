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

        $hariLibur = ['2025-12-25', '2025-01-01'];
        if (in_array($request->tanggal, $hariLibur)) {
            return back()->withErrors(['tanggal' => 'Maaf, tanggal tersebut adalah hari libur.']);
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


        $qrData = "ID:{$peminjaman->id}|{$request->nama_peminjam}|{$request->ruangan}";
        $linkQR = "https://quickchart.io/qr?text=" . urlencode($qrData) . "&size=300";

        $pesan  = "*NOTIFIKASI SIP-CACUK*\n\n";
        $pesan .= "Halo *{$request->nama_peminjam}*,\n";
        $pesan .= "Pengajuan peminjaman ruangan kamu berhasil dikirim.\n\n";
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
            $pesan = "*KABAR GEMBIRA!* 🟢\n\n";
            $pesan .= "Halo *$peminjaman->nama_peminjam*,\n";
            $pesan .= "Peminjaman ruangan *$peminjaman->ruangan* untuk tgl *$peminjaman->tanggal* telah *DISETUJUI*.\n";
            $pesan .= "Silakan datang tepat waktu.";
            
            $this->kirimPesanWA($peminjaman->no_hp, $pesan);
        }
        
        return redirect()->back()->with('success', 'Peminjaman Disetujui & Notifikasi Terkirim!');
    }

    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update(['status' => 'ditolak']);
        
        if(!empty($peminjaman->no_hp)) {
            $pesan = "*MOHON MAAF* 🔴\n\n";
            $pesan .= "Halo *$peminjaman->nama_peminjam*,\n";
            $pesan .= "Peminjaman ruangan *$peminjaman->ruangan* tgl *$peminjaman->tanggal* *DITOLAK* karena penuh/bentrok.";
            
            $this->kirimPesanWA($peminjaman->no_hp, $pesan);
        }
        
        return redirect()->back()->with('success', 'Peminjaman Ditolak!');
    }

    public function update(Request $request, $id)
    {
        $pinjam = Peminjaman::findOrFail($id);

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

    private function kirimPesanWA($nomor, $pesan)
    {
        $token = 'TOKEN_FONNTE_KAMU_DISINI'; 

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $nomor,
                'message' => $pesan,
                'countryCode' => '62', 
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }
}