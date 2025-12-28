<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    // Beritahu Laravel nama tabel & primary key-nya
    protected $table = 'ruangans';
    protected $primaryKey = 'id_ruangan';

    // Kolom mana saja yang boleh diisi manual
    protected $fillable = [
        'kode_ruang',
        'nama_ruangan',
        'kapasitas',
        'status',
    ];
}