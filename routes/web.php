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

/*
|--------------------------------------------------------------------------
| 1. ROUTE AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return redirect('/');
})->name('login');

// Proses Login (POST)
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'nim' => ['required'],
        'password' => ['required'],
    ]);

    if (Auth::attempt(['nim' => $request->nim, 'password' => $request->password])) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors(['nim' => 'NIM atau Password salah.']);
})->name('login.post'); // Ganti nama jadi login.post biar tidak bentrok

// Proses Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| 2. ROUTE HALAMAN UTAMA (DASHBOARD & SEARCH DIGABUNG)
|--------------------------------------------------------------------------
*/

Route::get('/', function (Request $request) {
    // A. Jika BELUM login, tampilkan view index (yang isinya form login)
    if (!Auth::check()) {
        return view('index'); 
    }

    // B. Jika SUDAH login, jalankan logika Dashboard + Search
    $user = Auth::user();

    // 1. Query Ruangan
    $ruanganQuery = Ruangan::query();
    if ($request->has('search_ruang')) {
        $ruanganQuery->where('nama_ruangan', 'like', '%' . $request->search_ruang . '%')
                     ->orWhere('kode_ruang', 'like', '%' . $request->search_ruang . '%');
    }

    // 2. Query Peminjaman
    $peminjamanQuery = Peminjaman::query();
    // Filter: Mahasiswa hanya lihat punya sendiri, Admin lihat semua
    if ($user->role == 'mahasiswa') {
        $peminjamanQuery->where('nama_peminjam', $user->name);
    }
    // Filter: Search
    if ($request->has('search_pinjam')) {
        $peminjamanQuery->where(function($q) use ($request) {
            $q->where('ruangan', 'like', '%' . $request->search_pinjam . '%')
              ->orWhere('nama_peminjam', 'like', '%' . $request->search_pinjam . '%');
        });
    }

    // 3. Query User
    $usersQuery = User::query();
    if ($request->has('search_user')) {
        $usersQuery->where('name', 'like', '%' . $request->search_user . '%')
                   ->orWhere('nim', 'like', '%' . $request->search_user . '%');
    }

    // 4. Query Inventaris
    $inventarisQuery = Inventaris::query();
    if ($request->has('search_barang')) {
        $inventarisQuery->where('nama_barang', 'like', '%' . $request->search_barang . '%');
    }

    // 5. Tentukan Tab Aktif
    $activeTab = 'ketua'; // Default tab
    if ($request->has('search_ruang')) $activeTab = 'ketua';
    if ($request->has('search_pinjam')) $activeTab = 'anggota1';
    if ($request->has('search_user')) $activeTab = 'anggota2';
    if ($request->has('search_barang')) $activeTab = 'anggota3';

    // Data untuk dikirim ke View
    $data = [
        'ruangan'       => $ruanganQuery->get(),
        'peminjaman'    => $peminjamanQuery->get(),
        'users'         => $usersQuery->get(),
        'inventaris'    => $inventarisQuery->get(),
        'total_item'    => Inventaris::sum('jumlah'),
        'kondisi_baik'  => Inventaris::where('kondisi', 'Baik')->sum('jumlah'),
        'kondisi_rusak' => Inventaris::where('kondisi', 'Rusak')->sum('jumlah'),
        'active_tab'    => $activeTab,
        'request'       => $request,
    ];

    return view('index', $data);
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. ROUTE CRUD (Hanya bisa diakses jika login)
|--------------------------------------------------------------------------
*/
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
    Route::put('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    // Anggota 3: Inventaris
    Route::post('/inventaris/store', [InventarisController::class, 'store'])->name('inventaris.store');
    Route::delete('/inventaris/{id}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    Route::put('/inventaris/update/{id}', [InventarisController::class, 'update'])->name('inventaris.update');
});