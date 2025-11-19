<!DOCTYPE html>
<html>
<head>
    <title>Surat Panggilan Orang Tua</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 0; font-size: 11pt; }
        
        .meta { margin-bottom: 15px; }
        .content { margin-bottom: 20px; text-align: justify;}
        
        table.data-siswa { width: 100%; margin-bottom: 15px; }
        table.data-siswa td { vertical-align: top; padding: 2px 0; }
        table.data-siswa td:first-child { width: 150px; }
        
        .ttd-container { width: 100%; margin-top: 40px; }
        .ttd-box { float: right; width: 40%; text-align: center; }
        .ttd-box-left { float: left; width: 40%; text-align: center; }
        
        /* Helper untuk Tanda Tangan Banyak (Surat 2 & 3) */
        .ttd-row { clear: both; height: 120px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>PEMERINTAH PROVINSI RIAU</h2>
        <h2>DINAS PENDIDIKAN</h2>
        <h2>{{ $sekolah['nama'] }}</h2>
        <p>{{ $sekolah['alamat'] }} | Telp: {{ $sekolah['telp'] }}</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="100">Nomor</td>
                <td>: {{ $surat->nomor_surat }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>: -</td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>: <strong>{{ $kasus->sanksi_deskripsi }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="meta" style="margin-top: 20px;">
        Kepada Yth,<br>
        Bapak/Ibu Orang Tua/Wali dari <strong>{{ $siswa->nama_siswa }}</strong><br>
        di Tempat
    </div>

    <div class="content">
        <p>Dengan hormat,</p>
        <p>Sehubungan dengan ketertiban dan kedisiplinan siswa di sekolah, kami memberitahukan bahwa putra/putri Bapak/Ibu:</p>

        <table class="data-siswa">
            <tr><td>Nama</td><td>: {{ $siswa->nama_siswa }}</td></tr>
            <tr><td>Kelas</td><td>: {{ $siswa->kelas->nama_kelas }}</td></tr>
            <tr><td>NISN</td><td>: {{ $siswa->nisn }}</td></tr>
            <tr><td>Masalah / Pelanggaran</td><td>: <strong>{{ $kasus->pemicu }}</strong></td></tr>
        </table>

        <p>Berdasarkan tata tertib sekolah, siswa tersebut perlu mendapatkan pembinaan lebih lanjut. Oleh karena itu, kami mengharapkan kehadiran Bapak/Ibu pada:</p>

        <table class="data-siswa" style="margin-left: 20px;">
            <tr><td>Hari/Tanggal</td><td>: ...................................................</td></tr>
            <tr><td>Pukul</td><td>: ...................................................</td></tr>
            <tr><td>Tempat</td><td>: Ruang BK / Kesiswaan {{ $sekolah['nama'] }}</td></tr>
            <tr><td>Acara</td><td>: Pembinaan dan Konsultasi Siswa</td></tr>
        </table>

        <p>Demikian surat panggilan ini kami sampaikan. Atas perhatian dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.</p>
    </div>

    <div class="ttd-container">
        <div style="text-align: right; margin-bottom: 10px;">
            Siak, {{ date('d F Y') }}
        </div>

        @if($surat->tipe_surat == 'Surat 1')
            <div class="ttd-box">
                Mengetahui,<br>
                Wali Kelas
                <br><br><br><br>
                <strong>{{ $siswa->kelas->waliKelas->nama }}</strong>
            </div>
        @endif

        @if($surat->tipe_surat == 'Surat 2')
            <div class="ttd-row">
                <div class="ttd-box-left">
                    Mengetahui,<br>
                    Wali Kelas
                    <br><br><br><br>
                    <strong>{{ $siswa->kelas->waliKelas->nama }}</strong>
                </div>
                <div class="ttd-box">
                    Mengetahui,<br>
                    Waka Kesiswaan
                    <br><br><br><br>
                    <strong>(Nama Waka Kesiswaan)</strong>
                </div>
            </div>
        @endif
        
        @if($surat->tipe_surat == 'Surat 3')
             <div class="ttd-row">
                <div class="ttd-box-left">
                    Mengetahui,<br>
                    Wali Kelas
                    <br><br><br><br>
                    <strong>{{ $siswa->kelas->waliKelas->nama }}</strong>
                </div>
                <div class="ttd-box">
                    Mengetahui,<br>
                    Waka Kesiswaan
                    <br><br><br><br>
                    <strong>(Nama Waka Kesiswaan)</strong>
                </div>
            </div>
            <div style="text-align: center; clear: both; margin-top: 20px;">
                Mengetahui,<br>
                Kepala Sekolah
                <br><br><br><br>
                <strong>(Nama Kepala Sekolah)</strong>
            </div>
        @endif

    </div>

</body>
</html>