<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit agar tidak bingung dengan bentuk jamak bahasa Inggris
    protected $table = 'inventaris';

    // $fillable mengizinkan kolom-kolom ini diisi secara massal (melalui Controller)
    protected $fillable = [
        'nama_barang',
        'jumlah',
        'kondisi',
        'bukti_foto',
    ];
}