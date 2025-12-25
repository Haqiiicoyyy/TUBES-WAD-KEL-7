<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'admin'; 
    $_SESSION['nama'] = 'Budi Santoso';
    $_SESSION['nim']  = '12022001';
}

$role = $_SESSION['role'];
?>

<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIP-CACUK</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .section-content {
            display: none; 
            animation: fadeIn 0.4s ease-out;
        }
        .section-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div id="login-view" class="login-container">
        <div class="login-card">
            <div class="login-brand">
                <i class="fas fa-university"></i> SIP-CACUK
            </div>
            <p class="login-subtitle">Sistem Informasi Peminjaman Gedung Cacuk</p>
            
            <form onsubmit="handleLogin(event)">
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">Username / NIM</label>
                    <input type="text" class="form-input" placeholder="Masukkan NIM..." required>
                </div>
                <div class="form-group" style="text-align: left;">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    Masuk ke Sistem <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="login-footer">
                &copy; 2025 Kelompok 7 Web Development Telkom University
            </div>
        </div>
    </div>


    <div id="dashboard-view" style="display: none; width: 100%;">
        
        <div class="sidebar">
            <div class="brand">
                <i class="fas fa-university"></i> SIP-CACUK
            </div>
                <nav>
                    <a href="#" class="menu-item active" onclick="showSection('ketua', this)">
                        <i class="fas fa-door-open"></i> Data Ruangan
                    </a>
                    <a href="#" class="menu-item" onclick="showSection('anggota1', this)">
                        <i class="fas fa-calendar-alt"></i> Peminjaman
                    </a>

                    <?php if ($role == 'admin') { ?>
                        <a href="#" class="menu-item" onclick="showSection('anggota2', this)">
                            <i class="fas fa-users"></i> Data User
                        </a>
                        <a href="#" class="menu-item" onclick="showSection('anggota3', this)">
                            <i class="fas fa-box"></i> Inventaris
                        </a>
                    <?php } ?>
                </nav>
            <div style="margin-top: auto;">
                <a href="#" onclick="handleLogout()" class="menu-item logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="main-content">

            <div id="section-ketua" class="section-content active">
                <div class="header">
                    <div class="page-title">
                        <h1>Manajemen Ruangan</h1>
                        <p>Kelola data fisik ruangan dan fasilitas Gedung Cacuk.</p>
                    </div>
                    <div class="user-profile">
                        <span>Halo, <b>Ketua Kelompok</b></span>
                        <div class="avatar">K</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header-actions">
                        <h3>Daftar Ruangan</h3>
                        
                        <?php if ($role == 'admin') { ?>
                            <label for="modal-ketua" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Ruangan</label>
                        <?php } ?>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr><th>No</th> <th>Foto</th> <th>Kode</th> <th>Nama Ruangan</th> <th>Kapasitas</th> <th>Status</th> <th>QR Code (API)</th> <th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 1;
                                    $data = mysqli_query($conn, "SELECT * FROM ruangan");
                                    while($d = mysqli_fetch_array($data)){
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><img src="https://via.placeholder.com/60x40" class="room-img"></td> <td><b><?php echo $d['kode_ruang']; ?></b></td>
                                        <td><?php echo $d['nama_ruangan']; ?></td>
                                        <td><?php echo $d['kapasitas']; ?> Orang</td>
                                        <td><span class="badge badge-success"><?php echo $d['status']; ?></span></td>
                                        <td>
                                            <img src="https://quickchart.io/qr?text=<?php echo $d['kode_ruang']; ?>&size=100" class="qr-code">
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-warning"><i class="fas fa-pen"></i></a>
                                            <a href="#" class="btn btn-danger"><i class="fas fa-trash"></i></a>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="section-anggota1" class="section-content">
                <div class="header">
                    <div class="page-title">
                        <h1>Transaksi Peminjaman</h1>
                        <p>Ajukan peminjaman ruang, kelola jadwal.</p>
                    </div>
                    <div class="user-profile">
                        <span>Halo, <b>Anggota 1</b></span>
                        <div class="avatar">A1</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header-actions">
                        <h3>Jadwal & Riwayat Saya</h3>
                        <label for="modal-anggota1" class="btn btn-primary"><i class="fas fa-calendar-plus"></i> Buat Peminjaman</label>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>No</th> <th>Tanggal</th> <th>Jam</th> <th>Ruangan</th> <th>Status</th> <th>Aksi</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td>1</td> <td>25 Okt 2025</td> <td>08:00 - 10:00</td> <td><b>R-301</b></td>
                                    <td><span class="badge badge-warning">Menunggu</span></td>
                                    <td>
                                        <?php if ($role == 'mahasiswa') { ?>
                                            <button class="btn btn-warning" title="Reschedule"><i class="fas fa-clock"></i></button>
                                            <button class="btn btn-danger" title="Batal"><i class="fas fa-times"></i></button>
                                        
                                        <?php } elseif ($role == 'admin') { ?>
                                            <a href="proses_acc.php?id=..." class="btn btn-primary" title="Setujui"><i class="fas fa-check"></i></a>
                                            <a href="proses_tolak.php?id=..." class="btn btn-danger" title="Tolak"><i class="fas fa-times"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="section-anggota2" class="section-content">
                <div class="header">
                    <div class="page-title">
                        <h1>Manajemen Pengguna</h1>
                        <p>Kelola data mahasiswa dan profil pengguna.</p>
                    </div>
                    <div class="user-profile">
                        <span>Halo, <b>Admin User</b></span>
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Admin" class="avatar-circle-sm">
                    </div>
                </div>
                <div class="layout-grid">
                    <div class="card profile-card">
                        <h3><i class="fas fa-id-card"></i> Profil Saya</h3>
                        <div class="profile-center">
                            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" class="avatar-big">
                            <h4 style="margin-top: 10px;">Felix Anggara</h4>
                            <p class="text-muted">NIM: 12022001</p>
                        </div>
                        <button class="btn btn-primary" style="width: 100%; justify-content: center;">Update Profil</button>
                    </div>
                    <div class="card table-card">
                <div class="card-header-actions">
                    <h3>Daftar Pengguna Aktif</h3>
                    <label for="modal-anggota2" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah User</label>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>NIM</th>
                                <th>Nama Lengkap</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ $user->name }}" class="avatar-table">
                                </td>
                                <td>{{ $user->nim }}</td>
                                <td><b>{{ $user->name }}</b></td>
                                <td>
                                    <span class="badge {{ $user->role == 'admin' ? 'badge-danger' : 'badge-success' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-warning"><i class="fas fa-pen"></i></button>
                                    <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
                </div>
            </div>

            <div id="section-anggota3" class="section-content">
                <div class="header">
                    <div class="page-title">
                        <h1>Inventaris Gedung</h1>
                        <p>Kelola aset, stok barang, dan pelaporan kerusakan.</p>
                    </div>
                    <div class="user-profile">
                        <span>Halo, <b>Anggota 3</b></span>
                        <div class="avatar" style="background-color: #F59E0B;">A3</div>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="card stat-card">
                        <div class="stat-icon" style="background: #DBEAFE; color: #1E3A8A;"><i class="fas fa-boxes"></i></div>
                        <div><h3>124</h3><p>Total Item</p></div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-check-circle"></i></div>
                        <div><h3>110</h3><p>Kondisi Baik</p></div>
                    </div>
                    <div class="card stat-card">
                        <div class="stat-icon" style="background: #FEE2E2; color: #B91C1C;"><i class="fas fa-exclamation-triangle"></i></div>
                        <div><h3>14</h3><p>Rusak</p></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header-actions">
                        <h3>Daftar Aset</h3>
                        <label for="modal-anggota3" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Barang</label>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>No</th> <th>Nama Barang</th> <th>Kondisi</th> <th>Bukti (API)</th> <th>Aksi</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td>1</td> <td><b>Proyektor LCD</b></td>
                                    <td><span class="badge badge-success">Baik</span></td>
                                    <td>-</td>
                                    <td>
                                        <button class="btn btn-warning"><i class="fas fa-tools"></i></button>
                                        <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div> 
        </div> 
    <input type="checkbox" id="modal-ketua" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Tambah Ruangan</h2><label for="modal-ketua" class="close-btn"><i class="fas fa-times"></i></label></div>
                <form action="proses_tambah_ruangan.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Kode Ruangan</label>
                        <input type="text" name="kode_ruang" class="form-input" placeholder="R-305" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" class="form-input" placeholder="Lab Multimedia" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" name="kapasitas" class="form-input" placeholder="40" required>
                    </div>
                    <div class="modal-footer">
                        <label for="modal-ketua" class="btn btn-cancel">Batal</label>
                        <button type="submit" name="simpan_ruangan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
        </div>
    </div>

    <input type="checkbox" id="modal-anggota1" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Ajukan Peminjaman</h2><label for="modal-anggota1" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form>
                <div class="alert-info"><i class="fas fa-info-circle"></i> Integrasi <b>Calendarific API</b>.</div>
                <div class="form-group"><label class="form-label">Tanggal</label><input type="date" class="form-input"></div>
                <div class="modal-footer"><label for="modal-anggota1" class="btn btn-cancel">Batal</label><button class="btn btn-primary">Ajukan</button></div>
            </form>
        </div>
    </div>

   <input type="checkbox" id="modal-anggota2" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Registrasi User Baru</h2>
                <label for="modal-anggota2" class="close-btn"><i class="fas fa-times"></i></label>
            </div>
            
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" placeholder="Masukkan nama..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-input" placeholder="Contoh: 12022001" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="email@student.telkomuniversity.ac.id" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-input" style="width: 100%;" required>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <label for="modal-anggota2" class="btn btn-cancel">Batal</label>
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <input type="checkbox" id="modal-anggota3" style="display: none;">
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header"><h2>Input Inventaris</h2><label for="modal-anggota3" class="close-btn"><i class="fas fa-times"></i></label></div>
            <form>
                <div class="form-group"><label class="form-label">Nama Barang</label><input type="text" class="form-input"></div>
                <div class="modal-footer"><label for="modal-anggota3" class="btn btn-cancel">Batal</label><button class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>


    <script>
        // 1. Fungsi Login
        function handleLogin(event) {
            event.preventDefault(); // Mencegah form refresh halaman
            
            // Animasi sederhana
            const btn = event.target.querySelector('button');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
            
            setTimeout(() => {
                // Sembunyikan Login, Tampilkan Dashboard
                document.getElementById('login-view').style.display = 'none';
                document.getElementById('dashboard-view').style.display = 'flex'; // Flex agar sidebar & content rapi
            }, 1000); // Delay 1 detik seolah-olah loading ke server
        }

        // 2. Fungsi Logout
        function handleLogout() {
            if(confirm("Apakah Anda yakin ingin keluar?")) {
                document.getElementById('dashboard-view').style.display = 'none';
                document.getElementById('login-view').style.display = 'flex';
                
                // Reset tombol login
                const loginBtn = document.querySelector('#login-view button');
                loginBtn.innerHTML = 'Masuk ke Sistem <i class="fas fa-arrow-right"></i>';
            }
        }

        // 3. Fungsi Pindah Menu (Seperti sebelumnya)
        function showSection(sectionId, element) {
            document.querySelectorAll('.section-content').forEach(sec => sec.classList.remove('active'));
            document.getElementById('section-' + sectionId).classList.add('active');

            document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
            if(element) element.classList.add('active');
        }
    </script>

</body>
</html>