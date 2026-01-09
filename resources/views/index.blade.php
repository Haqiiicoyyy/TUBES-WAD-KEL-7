<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIP-CACUK - Sistem Terintegrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        .section-content { display: none; animation: fadeIn 0.4s ease-out; }
        .section-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    {{-- 1. JIKA BELUM LOGIN: TAMPILKAN FORM LOGIN --}}
    @guest
    <div id="login-view" class="login-container">
        <div class="login-card">
            <div class="login-brand"><i class="fas fa-university"></i> SIP-CACUK</div>
            <p class="login-subtitle">Silakan login menggunakan NIM Anda</p>
            
            {{-- Form Login Laravel --}}
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-input" placeholder="Contoh: 12022001" required>
                </div>
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                
                {{-- Error Message --}}
                @if($errors->any())
                    <div style="color: red; font-size: 12px; margin-bottom: 10px;">{{ $errors->first() }}</div>
                @endif

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    Masuk ke Sistem <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            <div class="login-footer">&copy; 2025 Kelompok 7 Web Development</div>
        </div>
    </div>
    @endguest


    {{-- 2. JIKA SUDAH LOGIN: TAMPILKAN DASHBOARD --}}
    @auth
    <div id="dashboard-view" style="width: 100%; display: flex;">
        
        {{-- SIDEBAR --}}
        <div class="sidebar">
            <div class="brand"><i class="fas fa-university"></i> SIP-CACUK</div>
            <nav>
                <a href="#" class="menu-item active" onclick="showSection('ketua', this)">
                    <i class="fas fa-door-open"></i> Data Ruangan
                </a>
                <a href="#" class="menu-item" onclick="showSection('anggota1', this)">
                    <i class="fas fa-calendar-alt"></i> Peminjaman
                </a>

                {{-- Menu Khusus Admin --}}
                @if(Auth::user()->role == 'admin')
                    <a href="#" class="menu-item" onclick="showSection('anggota2', this)">
                        <i class="fas fa-users"></i> Data User
                    </a>
                    <a href="#" class="menu-item" onclick="showSection('anggota3', this)">
                        <i class="fas fa-box"></i> Inventaris
                    </a>
                @endif
            </nav>
            <div style="margin-top: auto;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="menu-item logout-btn" style="background:none; border:none; width:100%; text-align:left;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="main-content">
            
            {{-- Alert Notifikasi Sukses --}}
            @if(session('success'))
                <div class="alert-info" style="margin-bottom: 20px; background: #D1FAE5; color: #065F46; border: 1px solid #34D399;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- SECTION 1: KETUA (RUANGAN) --}}
            <div id="section-ketua" class="section-content active">
                <div class="header">
                    <div class="page-title">
                        <h1>Manajemen Ruangan</h1>
                        <p>Kelola data fisik ruangan.</p>
                    </div>
                    <div class="user-profile">
                        <span>Halo, <b>{{ Auth::user()->name }}</b></span>
                        <div class="avatar">K</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header-actions">
                        <h3>Daftar Ruangan</h3>
                        {{-- FORM SEARCH RUANGAN --}}
                            <form action="{{ route('dashboard') }}" method="GET" style="display:flex; gap:10px;">
                                <input type="text" name="search_ruang" class="form-input" placeholder="Cari ruangan..." value="{{ $request->search_ruang ?? '' }}" style="width: 200px;">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                @if($request->has('search_ruang'))
                                    <a href="{{ route('dashboard') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
                                @endif
                            </form>
                        @if(Auth::user()->role == 'admin')
                            <label for="modal-ketua" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Ruangan</label>
                        @endif
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>No</th><th>Kode</th><th>Nama Ruangan</th><th>Kapasitas</th><th>Status</th><th>QR Code</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @foreach($ruangan as $key => $r)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><b>{{ $r->kode_ruang }}</b></td>
                                    <td>{{ $r->nama_ruangan }}</td>
                                    <td>{{ $r->kapasitas }}</td>
                                    <td><span class="badge badge-success">{{ $r->status }}</span></td>
                                    <td><img src="https://quickchart.io/qr?text={{ $r->kode_ruang }}&size=80" width="50"></td>
                                    <td>
                                        @if(Auth::user()->role == 'admin')
                                            {{-- TOMBOL EDIT (Kuning) --}}
                                            <button type="button" class="btn btn-warning" 
                                                onclick="openEditRuangan({{ $r->id_ruangan }}, '{{ addslashes($r->kode_ruang) }}', '{{ addslashes($r->nama_ruangan) }}', '{{ $r->kapasitas }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            
                                            {{-- TOMBOL HAPUS (Merah) --}}
                                            <form action="{{ route('ruangan.destroy', $r->id_ruangan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus ruangan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @else
                                            {{-- MAHASISWA: Hanya bisa pilih untuk pinjam --}}
                                            <label for="modal-anggota1" class="btn btn-primary btn-sm" onclick="document.getElementsByName('ruangan')[0].value='{{ $r->nama_ruangan }}'; showSection('anggota1', document.querySelector('[onclick*=\'anggota1\']'))">
                                                <i class="fas fa-calendar-plus"></i> Pilih Ruangan
                                            </label>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: ANGGOTA 1 (PEMINJAMAN) --}}
            <div id="section-anggota1" class="section-content">
                <div class="header">
                    <div class="page-title"><h1>Peminjaman</h1><p>Riwayat peminjaman saya.</p></div>
                    <div class="user-profile">
                        <span>Halo, <b>{{ Auth::user()->name }}</b></span>
                        <div class="avatar">A1</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header-actions">
                        <h3>Riwayat</h3>
                        <form action="{{ route('dashboard') }}" method="GET" style="display:flex; gap:10px;">
                            <input type="text" name="search_pinjam" class="form-input" placeholder="Cari peminjam/ruang..." value="{{ $request->search_pinjam ?? '' }}" style="width: 200px;">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            @if($request->has('search_pinjam'))
                                <a href="{{ route('dashboard') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
                            @endif
                        </form>
                        <label for="modal-anggota1" class="btn btn-primary"><i class="fas fa-plus"></i> Ajukan</label>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>No</th><th>Tanggal</th><th>Jam</th><th>Ruangan</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @foreach($peminjaman as $idx => $p)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $p->tanggal }}</td>
                                    <td>{{ $p->jam_mulai }} - {{ $p->jam_selesai }}</td>
                                    <td>{{ $p->ruangan }}</td>
                                    <td><span class="badge {{ $p->status == 'disetujui' ? 'badge-success' : ($p->status == 'ditolak' ? 'badge-danger' : 'badge-warning') }}">{{ ucfirst($p->status) }}</span></td>
                                    <td>
                                        @if(Auth::user()->role == 'admin')
                                            @if($p->status == 'menunggu')
                                            <a href="{{ route('peminjaman.acc', $p->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-check"></i></a>
                                            <a href="{{ route('peminjaman.tolak', $p->id) }}" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></a>
                                        @endif
                                        @else
                                             {{-- MAHASISWA: Hanya bisa melihat status --}}
                                            <span class="text-muted"><i class="fas fa-lock"></i> No Action</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: PROFIL & RIWAYAT (MAHASISWA) / MANAJEMEN USER (ADMIN) --}}
            <div id="section-anggota2" class="section-content">
                <div class="header">
                    <div class="page-title">
                        <h1>{{ Auth::user()->role == 'admin' ? 'Manajemen User' : 'Profil & Riwayat Peminjaman' }}</h1>
                        <p>{{ Auth::user()->role == 'admin' ? 'Kelola pengguna sistem.' : 'Informasi akun dan histori aktivitas Anda.' }}</p>
                    </div>
                    <div class="user-profile">
                        <span>Halo, <b>{{ Auth::user()->name }}</b></span>
                        <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    </div>
                </div>

                <div class="layout-grid">
                    {{-- Sisi Kiri: Kartu Profil --}}
                    <div class="card profile-card">
                        <div class="profile-center">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ Auth::user()->name }}" class="avatar-big">
                            <h4 style="margin-top: 15px;">{{ Auth::user()->name }}</h4>
                            <p class="text-muted">{{ Auth::user()->nim }}</p>
                            <span class="badge {{ Auth::user()->role == 'admin' ? 'badge-danger' : 'badge-success' }}" style="margin-top: 10px;">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                        <div style="font-size: 14px;">
                            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                            <p style="margin-top: 10px;"><strong>Status Akun:</strong> Aktif</p>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Tabel (Berbeda isi tergantung Role) --}}
                    <div class="card table-card">
                        @if(Auth::user()->role == 'admin')
                            <div class="card-header-actions">
                                <h3>Daftar Pengguna</h3>
                                <form action="{{ route('dashboard') }}" method="GET" style="display:flex; gap:10px;">
                                    <input type="text" name="search_user" class="form-input" placeholder="Cari Nama/NIM..." value="{{ request('search_user') }}" style="width: 200px;">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                </form>
                                <label for="modal-anggota2" class="btn btn-primary"><i class="fas fa-user-plus"></i></label>
                            </div>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr><th>Nama</th><th>NIM</th><th>Role</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->nim }}</td>
                                            <td><span class="badge {{ $u->role == 'admin' ? 'badge-danger' : 'badge-success' }}">{{ $u->role }}</span></td>
                                            <td>
                                                <button class="btn btn-warning" onclick="openEditUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}')"><i class="fas fa-pen"></i></button>
                                                @if(Auth::id() != $u->id)
                                                <form action="{{ route('user.destroy', $u->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="card-header-actions">
                                <h3>Riwayat Peminjaman Anda</h3>
                                <span class="text-muted">Total: {{ $peminjaman->count() }} Kegiatan</span>
                            </div>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr><th>Tanggal</th><th>Ruangan</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        @forelse($peminjaman as $p)
                                        <tr>
                                            <td>{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                                            <td><b>{{ $p->ruangan }}</b></td>
                                            <td><span class="badge {{ $p->status == 'disetujui' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($p->status) }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" style="text-align:center;">Belum ada riwayat.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SECTION 4: ANGGOTA 3 (INVENTARIS) --}}
            <div id="section-anggota3" class="section-content">
                @if(Auth::user()->role == 'admin')
                    {{-- TAMPILAN KHUSUS ADMIN --}}
                    <div class="header">
                        <div class="page-title">
                            <h1>Manajemen Inventaris</h1>
                            <p>Kelola aset dan logistik gedung.</p>
                        </div>
                        <div class="user-profile">
                            <span>Halo, <b>Admin</b></span>
                            <div class="avatar">A3</div>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="card stat-card">
                            <div>
                                <h3>{{ $total_item ?? 0 }}</h3>
                                <p>Total Item</p>
                            </div>
                        </div>
                        <div class="card stat-card">
                            <div>
                                <h3>{{ $kondisi_baik ?? 0 }}</h3>
                                <p>Kondisi Baik</p>
                            </div>
                        </div>
                        <div class="card stat-card">
                            <div>
                                <h3>{{ $kondisi_rusak ?? 0 }}</h3>
                                <p>Kondisi Rusak</p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header-actions">
                            <h3>Daftar Barang</h3>
                            <div style="display:flex; gap:10px;">
                                {{-- Form Search Barang --}}
                                <form action="{{ route('dashboard') }}" method="GET" style="display:flex; gap:10px;">
                                    <input type="text" name="search_barang" class="form-input" placeholder="Cari barang..." value="{{ $request->search_barang ?? '' }}" style="width: 200px;">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                    @if($request->has('search_barang'))
                                        <a href="{{ route('dashboard') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
                                    @endif
                                </form>
                                <label for="modal-anggota3" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Barang</label>
                            </div>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Kondisi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventaris as $idx => $inv)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $inv->nama_barang }}</td>
                                        <td>{{ $inv->jumlah }}</td>
                                        <td>
                                            <span class="badge {{ $inv->kondisi == 'Baik' ? 'badge-success' : 'badge-danger' }}">
                                                {{ $inv->kondisi }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning" 
                                                onclick="openEditInventaris({{ $inv->id }}, '{{ addslashes($inv->nama_barang) }}', '{{ $inv->jumlah }}', '{{ $inv->kondisi }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            <form action="{{ route('inventaris.destroy', $inv->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus barang ini?');">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    {{-- TAMPILAN KHUSUS MAHASISWA --}}
                    <div class="card" style="text-align: center; padding: 80px 20px;">
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-lock" style="font-size: 64px; color: #e5e7eb;"></i>
                        </div>
                        <h2 style="font-family: 'Poppins', sans-serif; color: #374151;">Akses Terbatas</h2>
                        <p style="color: #6b7280; max-width: 400px; margin: 10px auto;">Maaf, halaman Inventaris hanya dapat diakses oleh Administrator untuk keperluan pengelolaan aset.</p>
                        <button class="btn btn-primary" style="margin-top: 20px;" onclick="showSection('ketua', document.querySelector('.menu-item'))">
                            Kembali ke Dashboard
                        </button>
                    </div>
                @endif
            </div> 

    {{-- MODALS --}}
    
    {{-- Modal Ketua (Ruangan) --}}
    <input type="checkbox" id="modal-ketua" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Tambah Ruangan</h2><label for="modal-ketua" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form action="{{ route('ruangan.store') }}" method="POST">
                @csrf
                <div class="form-group"><label class="form-label">Kode</label><input type="text" name="kode_ruang" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Nama</label><input type="text" name="nama_ruangan" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Kapasitas</label><input type="number" name="kapasitas" class="form-input" required></div>
                <div class="modal-footer"><label for="modal-ketua" class="btn btn-cancel">Batal</label><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>

    {{-- Modal Anggota 1 (Peminjaman) --}}
    <input type="checkbox" id="modal-anggota1" style="display: none;">
    
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Ajukan Peminjaman</h2><label for="modal-anggota1" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form action="{{ route('peminjaman.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nama_peminjam" value="{{ Auth::user()->name }}">
                <div class="form-group"><label class="form-label">Ruangan</label><input type="text" name="ruangan" class="form-input" placeholder="R-301" required></div>
                <div class="form-group"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-input" required></div>
                <div class="form-row">
                    <div class="form-group half"><label class="form-label">Mulai</label><input type="time" name="jam_mulai" class="form-input" required></div>
                    <div class="form-group half"><label class="form-label">Selesai</label><input type="time" name="jam_selesai" class="form-input" required></div>
                </div>
                 <div class="form-group">
                    <label class="form-label">Nomor HP (WhatsApp)</label>
                    <input type="number" name="no_hp" class="form-input" placeholder="08xxxxxxxx" required>
                </div>  
                <div class="form-group"><label class="form-label">Keperluan</label><textarea name="keperluan" class="form-input" required></textarea></div>
                <div class="modal-footer"><label for="modal-anggota1" class="btn btn-cancel">Batal</label><button class="btn btn-primary">Ajukan</button></div>  
            </form>
        </div>
    </div>

    {{-- Modal Anggota 2 (User) --}}
    <input type="checkbox" id="modal-anggota2" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Tambah User</h2><label for="modal-anggota2" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="form-group"><label class="form-label">Nama</label><input type="text" name="name" class="form-input" required></div>
                <div class="form-group"><label class="form-label">NIM</label><input type="text" name="nim" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" required></div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-input">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-footer"><label for="modal-anggota2" class="btn btn-cancel">Batal</label><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>

    {{-- Modal Anggota 3 (Inventaris) --}}
    <input type="checkbox" id="modal-anggota3" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Input Barang</h2><label for="modal-anggota3" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form action="{{ route('inventaris.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label class="form-label">Nama Barang</label><input type="text" name="nama_barang" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Jumlah</label><input type="number" name="jumlah" class="form-input" required></div>
                <div class="form-group">
                    <label class="form-label">Kondisi</label>
                    <select name="kondisi" class="form-input">
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Foto</label><input type="file" name="bukti_foto" class="form-input"></div>
                <div class="modal-footer"><label for="modal-anggota3" class="btn btn-cancel">Batal</label><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
    @endauth
    
    {{-- Modal Edit Ruangan --}}
    <input type="checkbox" id="modal-edit-ruangan" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Edit Ruangan</h2><label for="modal-edit-ruangan" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form id="form-edit-ruangan" method="POST">
                @csrf @method('PUT')
                <div class="form-group"><label>Kode</label><input type="text" name="kode_ruang" id="edit-kode-ruang" class="form-input"></div>
                <div class="form-group"><label>Nama</label><input type="text" name="nama_ruangan" id="edit-nama-ruangan" class="form-input"></div>
                <div class="form-group"><label>Kapasitas</label><input type="number" name="kapasitas" id="edit-kapasitas-ruangan" class="form-input"></div>
                <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>

    {{-- Modal Edit User --}}
    <input type="checkbox" id="modal-edit-user" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Edit User</h2><label for="modal-edit-user" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form id="form-edit-user" method="POST">
                @csrf @method('PUT')
                <div class="form-group"><label>Nama</label><input type="text" name="name" id="edit-nama-user" class="form-input"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" id="edit-email-user" class="form-input"></div>
                <div class="form-group"><label>Role</label>
                    <select name="role" id="edit-role-user" class="form-input">
                        <option value="admin">Admin</option>
                        <option value="mahasiswa">Mahasiswa</option>
                    </select>
                </div>
                <div class="form-group"><label>Password Baru (Opsional)</label><input type="password" name="password" class="form-input" placeholder="Isi jika ingin ganti"></div>
                <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Inventaris --}}
    <input type="checkbox" id="modal-edit-inventaris" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Edit Barang</h2><label for="modal-edit-inventaris" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form id="form-edit-inventaris" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" id="edit-nama-barang" class="form-input"></div>
                <div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" id="edit-jumlah-barang" class="form-input"></div>
                <div class="form-group"><label>Kondisi</label>
                    <select name="kondisi" id="edit-kondisi-barang" class="form-input">
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div class="form-group"><label>Ganti Foto (Opsional)</label><input type="file" name="bukti_foto" class="form-input"></div>
                <div class="modal-footer"><button class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
    
    {{-- Javascript SPA Logic --}}
    <script>
        function showSection(sectionId, element) {
            document.querySelectorAll('.section-content').forEach(sec => sec.classList.remove('active'));
            document.getElementById('section-' + sectionId).classList.add('active');
            
            if(element) {
                document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
                element.classList.add('active');
            }
     }

        document.addEventListener("DOMContentLoaded", function() {
            
            let activeTab = "{{ $active_tab ?? 'ketua' }}"; 
            
            let menuId = '';
            if(activeTab === 'ketua') menuId = 0;
            if(activeTab === 'anggota1') menuId = 1; 
            if(activeTab === 'anggota2') menuId = 2; 
            if(activeTab === 'anggota3') menuId = 3; 

            let menuItems = document.querySelectorAll('.sidebar nav .menu-item');
            
            if(menuItems[menuId]) {
                showSection(activeTab, menuItems[menuId]);
            }
        });

        function openEditRuangan(id, kode, nama, kapasitas) {
            document.getElementById('edit-kode-ruang').value = kode;
            document.getElementById('edit-nama-ruangan').value = nama;
            document.getElementById('edit-kapasitas-ruangan').value = kapasitas;

            document.getElementById('form-edit-ruangan').action = "/ruangan/update/" + id;
            document.getElementById('modal-edit-ruangan').checked = true;
        }

        function openEditUser(id, nama, email, role) {
            document.getElementById('edit-nama-user').value = nama;
            document.getElementById('edit-email-user').value = email;
            document.getElementById('edit-role-user').value = role;

            document.getElementById('form-edit-user').action = "/user/update/" + id;
            document.getElementById('modal-edit-user').checked = true;
        }

        function openEditInventaris(id, nama, jumlah, kondisi) {
            document.getElementById('edit-nama-barang').value = nama;
            document.getElementById('edit-jumlah-barang').value = jumlah;
            document.getElementById('edit-kondisi-barang').value = kondisi;

            document.getElementById('form-edit-inventaris').action = "/inventaris/update/" + id;
            document.getElementById('modal-edit-inventaris').checked = true;
        }
    </script>
</body>
</html>