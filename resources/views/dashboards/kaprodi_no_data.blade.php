<!DOCTYPE html>
<html>
<head>
    <title>Akses Ditolak</title>
    <style>body { font-family: sans-serif; padding: 50px; text-align: center; }</style>
</head>
<body>
    <h1 style="color: red;">Tidak Ada Data Kelas</h1>
    <p>Halo, <strong>{{ Auth::user()->nama }}</strong>.</p>
    <p>Anda terdaftar sebagai Kaprodi, namun sistem belum mencatat jurusan mana yang Anda ampu.</p>
    <p>Silakan hubungi Operator Sekolah / Waka Kurikulum untuk mapping kelas.</p>
    
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="padding: 10px 20px; cursor: pointer;">Logout</button>
    </form>
</body>
</html>