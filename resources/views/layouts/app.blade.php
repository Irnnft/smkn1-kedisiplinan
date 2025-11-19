<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sistem Kedisiplinan') | SMKN 1 Siak</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item d-flex align-items-center">
        <span class="mr-2 d-none d-md-inline text-gray-600 small">
            Halo, <strong>{{ Auth::user()->nama }}</strong> ({{ Auth::user()->role->nama_role }})
        </span>
      </li>
      <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <span class="brand-text font-weight-light px-3">Sistem Kedisiplinan</span>
    </a>

    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
            @php $role = Auth::user()->role->nama_role; @endphp

            <li class="nav-header">MENU UTAMA</li>

            <li class="nav-item">
                @if($role == 'Operator Sekolah' || $role == 'Waka Kesiswaan')
                    <a href="{{ route('dashboard.admin') }}" class="nav-link">
                @elseif($role == 'Kepala Sekolah')
                    <a href="{{ route('dashboard.kepsek') }}" class="nav-link">
                @elseif($role == 'Wali Kelas')
                    <a href="{{ route('dashboard.walikelas') }}" class="nav-link">
                @elseif($role == 'Kaprodi')
                    <a href="{{ route('dashboard.kaprodi') }}" class="nav-link">
                @elseif($role == 'Orang Tua')
                    <a href="{{ route('dashboard.ortu') }}" class="nav-link">
                @else
                    <a href="#" class="nav-link"> 
                @endif
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            @if(in_array($role, ['Guru', 'Wali Kelas', 'Waka Kesiswaan', 'Kaprodi']))
            <li class="nav-item">
                <a href="{{ route('pelanggaran.create') }}" class="nav-link">
                    <i class="nav-icon fas fa-edit"></i>
                    <p>Catat Pelanggaran</p>
                </a>
            </li>
            @endif

            @if($role == 'Operator Sekolah' || $role == 'Waka Kesiswaan')
            <li class="nav-header">DATA & LAPORAN</li>
            
            <li class="nav-item">
                <a href="{{ route('siswa.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-user-graduate"></i>
                    <p>Data Siswa</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('riwayat.index') }}" class="nav-link {{ Request::is('riwayat-pelanggaran*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-history"></i>
                    <p>Data Riwayat Lengkap</p>
                </a>
            </li>
            @endif

            @if($role == 'Operator Sekolah')
            <li class="nav-header">ADMINISTRASI</li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Manajemen User</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('jenis-pelanggaran.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-gavel"></i>
                    <p>Aturan & Poin</p>
                </a>
            </li>
            @endif

            @if($role == 'Kepala Sekolah')
            <li class="nav-header">EKSEKUTIF</li>
            <li class="nav-item">
                 <a href="{{ route('riwayat.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-search"></i>
                    <p>Cari Data Siswa</p>
                </a>
            </li>
            @endif

        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('title')</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Sistem Informasi SMK
    </div>
    <strong>Copyright &copy; 2025 <a href="#">SMKN 1 Siak Lubuk Dalam</a>.</strong>
  </footer>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>