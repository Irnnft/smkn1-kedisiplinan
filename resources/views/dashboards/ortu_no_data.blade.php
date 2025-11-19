<!DOCTYPE html>
<html>
<body style="text-align:center; padding:50px; font-family:sans-serif;">
    <h2 style="color:red">Data Tidak Ditemukan</h2>
    <p>Akun Orang Tua ini belum terhubung dengan data Siswa manapun.</p>
    <p>Silakan hubungi Operator Sekolah.</p>
    <form action="{{ route('logout') }}" method="POST"> @csrf <button>Logout</button> </form>
</body>
</html>