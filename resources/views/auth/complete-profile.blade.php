<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil | {{ config('app.name', 'SMK Negeri 1') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* RESET & BASE */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary-600: #4F46E5;
            --primary-700: #4338CA;
            --primary-gradient: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            --bg-color: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-main: #111827;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;
            --error-color: #EF4444;
            --success-color: #10B981;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 100%, hsla(225,39%,30%,1) 0, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        /* CONTAINER */
        .page-container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
        }

        /* DECORATION */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            z-index: -1;
            animation: float 10s infinite ease-in-out;
        }
        .blob-1 { background: #4F46E5; top: -100px; left: -100px; }
        .blob-2 { background: #EC4899; bottom: -100px; right: -100px; animation-delay: -5s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 20px); }
        }

        /* CARD */
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.5), 
                0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* HEADER */
        .card-header {
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
            background: linear-gradient(to bottom, #ffffff, #f9fafb);
            border-bottom: 1px solid var(--border-color);
        }

        .icon-wrapper {
            width: 64px;
            height: 64px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            transform: rotate(-10deg);
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* BODY */
        .card-body {
            padding: 2rem;
        }

        .user-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #EEF2FF;
            color: var(--primary-700);
            padding: 10px 16px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 1px solid #E0E7FF;
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .label-desc {
            font-weight: 400;
            font-size: 0.8em;
            color: var(--text-muted);
            margin-left: 4px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s;
            background: #fff;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-600);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .input-hint {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .current-value {
            font-size: 0.85rem;
            background: #F3F4F6;
            padding: 6px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 8px;
            color: #4B5563;
        }

        /* PASSWORD SECTION */
        .password-section {
            background: #FFF1F2;
            border: 1px solid #FECDD3;
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .password-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #BE123C;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        /* BUTTON */
        .btn-submit {
            display: block;
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 1.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* ALERT */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #fecaca;
        }

        /* DEV SKIP */
        .dev-skip {
            margin-top: 20px;
            text-align: center;
        }
        .dev-skip a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.8rem;
            border-bottom: 1px dotted rgba(255,255,255,0.4);
        }
        .dev-skip a:hover { color: white; }

        /* RESPONSIVE */
        @media (max-width: 640px) {
            .card-header { padding: 2rem 1.5rem; }
            .card-body { padding: 1.5rem; }
            h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>

    <div class="page-container">
        <!-- Background Blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <div class="card">
            <div class="card-header">
                <div class="icon-wrapper">🎓</div>
                <h1>Selamat Datang</h1>
                <p class="subtitle">Demi keamanan, mohon lengkapi data profil<br>Anda sebelum masuk ke sistem.</p>
            </div>

            <div class="card-body">
                <div class="user-badge">
                    <span>👤</span>
                    <span>{{ $user->nama }}</span>
                    <span style="opacity:0.3">|</span>
                    <span>{{ $user->role->nama_role }}</span>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 1.2rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.complete.store') }}" method="POST">
                    @csrf

                    <!-- USERNAME -->
                    <div class="form-group">
                        <label>Username <span class="label-desc">(Opsional)</span></label>
                        <div class="current-value">Saat ini: <strong>{{ $user->username }}</strong></div>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Ubah hanya jika perlu">
                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label>Email <span style="color:var(--error-color)">*</span></label>
                        @if($user->email)
                            <div class="current-value">Saat ini: <strong>{{ $user->email }}</strong></div>
                        @endif
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="email@sekolah.sch.id" required>
                        <div class="input-hint">Email aktif untuk pemulihan akun & notifikasi.</div>
                    </div>

                    <!-- PHONE (Conditional) -->
                    @if(!$isWaliMurid)
                    <div class="form-group">
                        <label>WhatsApp <span class="label-desc">(Opsional)</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                    </div>
                    @endif

                    <!-- PASSWORD CHANGE -->
                    @if($needsPasswordChange)
                    <div class="password-section">
                        <div class="password-title">
                            <span>🔒</span> Keamanan Password (Wajib)
                        </div>

                        <div class="form-group">
                            <label>Password Lama <span style="color:var(--error-color)">*</span></label>
                            <input type="password" name="current_password" placeholder="Password dari operator" required>
                        </div>

                        <div class="form-group">
                            <label>Password Baru <span style="color:var(--error-color)">*</span></label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 0">
                            <label>Ulangi Password Baru <span style="color:var(--error-color)">*</span></label>
                            <input type="password" name="password_confirmation" placeholder="Ketik ulang password baru" required>
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="btn-submit">Simpan & Lanjutkan →</button>
                </form>

                <!-- Logout Link -->
                <div style="margin-top: 1.5rem; text-align: center; padding-top: 1.5rem; border-top: 1px solid #E5E7EB;">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #6B7280; font-size: 0.85rem; cursor: pointer; font-weight: 500;">
                            ← Logout / Kembali ke Login
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(app()->environment('local'))
        <div class="dev-skip">
            <a href="{{ route('profile.complete.skip') }}">[Developer: Skip Langkah Ini]</a>
        </div>
        @endif
    </div>

</body>
</html>
