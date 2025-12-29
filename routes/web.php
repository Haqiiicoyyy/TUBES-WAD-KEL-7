<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Inventaris;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventarisController;

// --- 1. ROUTE HALAMAN UTAMA (DASHBOARD) ---
Route::get('/', function () {
    // Jika belum login, tampilkan view login
    if (!Auth::check()) {
        return view('index'); // View index bakal ngedeteksi @auth dan menampilkan form login
    }

    // Jika SUDAH login, ambil semua data untuk dashboard
    $data = [
        'ruangan'    => Ruangan::all(),
        // Jika admin ambil semua, jika mhs ambil punya sendiri
        'peminjaman' => Auth::user()->role == 'admin' 
                        ? Peminjaman::all() 
                        : Peminjaman::where('nama_peminjam', Auth::user()->name)->get(),
        'users'      => User::all(),
        'inventaris' => Inventaris::all(),
        'total_item' => Inventaris::sum('jumlah'),
        'kondisi_baik' => Inventaris::where('kondisi', 'Baik')->sum('jumlah'),
        'kondisi_rusak'=> Inventaris::where('kondisi', 'Rusak')->sum('jumlah'),
    ];

    return view('index', $data);
})->name('dashboard');

// --- 2. ROUTE AUTH (LOGIN & LOGOUT) ---
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'nim' => ['required'], // Login pakai NIM
        'password' => ['required'],
    ]);

    // Custom login pakai NIM (karena default Laravel pakai Email)
    // Pastikan input NIM di database user cocok
    if (Auth::attempt(['nim' => $request->nim, 'password' => $request->password])) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors(['nim' => 'NIM atau Password salah.']);
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// --- 3. ROUTE CRUD ANGGOTA ---
Route::middleware('auth')->group(function () {
    // Ketua: Ruangan
    Route::post('/ruangan/store', [RuanganController::class, 'store'])->name('ruangan.store');
    Route::delete('/ruangan/{id}', [RuanganController::class, 'destroy'])->name('ruangan.destroy');
    Route::put('/ruangan/update/{id}', [RuanganController::class, 'update'])->name('ruangan.update');
    // Anggota 1: Peminjaman
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/acc/{id}', [PeminjamanController::class, 'acc'])->name('peminjaman.acc');
    Route::get('/peminjaman/tolak/{id}', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
    Route::put('/peminjaman/update/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');

    // Anggota 2: User
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    // Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update'); // Aktifkan jika controller update sudah fix
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Anggota 3: Inventaris
    Route::post('/inventaris/store', [InventarisController::class, 'store'])->name('inventaris.store');
    Route::delete('/inventaris/{id}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    Route::put('/inventaris/update/{id}', [InventarisController::class, 'update'])->name('inventaris.update');
});