<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Operator</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* Grid Menu Besar */
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 30px; }
        
        .card-menu { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; transition: transform 0.2s; border-top: 5px solid #ccc; text-decoration: none; color: inherit; display: block; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        
        .icon { font-size: 3rem; margin-bottom: 15px; display: block; }
        .menu-title { font-size: 1.2rem; font-weight: bold; color: #333; margin-bottom: 10px; }
        .menu-desc { font-size: 0.9rem; color: #777; }
        .menu-count { background: #eee; display: inline-block; padding: 5px 10px; border-radius: 15px; margin-top: 10px; font-weight: bold; font-size: 0.8rem; }

        .btn-logout { background-color: #333; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>Panel Operator Sekolah</h1>
            <small>Selamat Datang, {{ Auth::user()->nama }}</small>
        </div>
        <form action="{{ route('logout') }}" method="POST"> @csrf <button class="btn-logout">Logout</button> </form>
    </div>

    <h3 style="border-bottom: 2px solid #ddd; padding-bottom: 10px;">🔧 Manajemen Data Induk (Master Data)</h3>

    <div class="menu-grid">
        
        <a href="{{ route('users.index') }}" class="card-menu" style="border-top-color: #17a2b8;">
            <span class="icon">👥</span>
            <div class="menu-title">Data Pengguna</div>
            <div class="menu-desc">Kelola akun Guru, Wali Kelas, Kaprodi & Staff.</div>
            <div class="menu-count">{{ $totalUser }} Akun Terdaftar</div>
        </a>

        <a href="{{ route('siswa.index') }}" class="card-menu" style="border-top-color: #007bff;">
            <span class="icon">🎓</span>
            <div class="menu-title">Data Siswa</div>
            <div class="menu-desc">Kelola data siswa, NISN, dan pemetaan kelas.</div>
            <div class="menu-count">{{ $totalSiswa }} Siswa Aktif</div>
        </a>

        <a href="{{ route('jenis-pelanggaran.index') }}" class="card-menu" style="border-top-color: #dc3545;">
            <span class="icon">⚠️</span>
            <div class="menu-title">Aturan & Poin</div>
            <div class="menu-desc">Kelola daftar pelanggaran, bobot poin, dan kategori.</div>
            <div class="menu-count">{{ $totalAturan }} Aturan</div>
        </a>

        <div class="card-menu" style="border-top-color: #6c757d; opacity: 0.6; cursor: not-allowed;">
            <span class="icon">🏫</span>
            <div class="menu-title">Data Kelas</div>
            <div class="menu-desc">Fitur pengelolaan kelas belum diaktifkan.</div>
            <div class="menu-count">{{ $totalKelas }} Kelas</div>
        </div>

    </div>

    <h3 style="border-bottom: 2px solid #ddd; padding-bottom: 10px;">⚙️ Pengaturan Sistem</h3>
    <div class="menu-grid">
         <a href="{{ route('users.create') }}" class="card-menu" style="border-top-color: #28a745;">
            <span class="icon">➕</span>
            <div class="menu-title">Tambah User Baru</div>
        </a>
    </div>

</div>

</body>
</html>