<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | WEBGIS BWS Kalimantan I</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core (SB Admin 2) -->
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --brand-blue: #2563eb;
            --brand-blue-hover: #1d4ed8;
            --brand-navy: #0f172a;
            --brand-accent: #38bdf8;
            --pupr-gold: #fbbf24;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(37, 99, 235, 0.4) 0%, transparent 45%),
                        radial-gradient(circle at 85% 85%, rgba(30, 64, 175, 0.5) 0%, transparent 45%),
                        linear-gradient(135deg, #0b132b 0%, #1c2541 50%, #0b132b 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Topography Lines Overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        /* Top Bar Navigation */
        .auth-top-bar {
            width: 100%;
            max-width: 1080px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .btn-back-peta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 9999px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-back-peta:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateX(-2px);
            text-decoration: none;
        }

        .badge-secure-portal {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            border-radius: 9999px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Split Card Container */
        .auth-card-split {
            width: 100%;
            max-width: 1080px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 65px -12px rgba(2, 6, 23, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.1);
            display: flex;
            position: relative;
            z-index: 2;
            min-height: 620px;
        }

        /* Left Hero Panel (Visual & Instansi) */
        .auth-hero-panel {
            flex: 1.15;
            background: linear-gradient(150deg, #1e3a8a 0%, #1e40af 45%, #1d4ed8 100%);
            padding: 44px 42px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Decorative Background Elements in Hero */
        .auth-hero-panel::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-hero-panel::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Institution Header */
        .instansi-header {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .instansi-logo-box {
            width: 58px;
            height: 58px;
            background: #ffffff;
            border-radius: 14px;
            padding: 5px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .instansi-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .instansi-text-wrap {
            display: flex;
            flex-direction: column;
        }

        .instansi-ministry {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #fde68a;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .instansi-ditjen {
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.3;
            margin-top: 2px;
        }

        .instansi-unit {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
            margin-top: 3px;
        }

        /* Hero Content & Features */
        .hero-main-content {
            margin: 32px 0;
            position: relative;
            z-index: 1;
        }

        .hero-title-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .hero-system-title {
            font-size: 25px;
            font-weight: 800;
            line-height: 1.3;
            letter-spacing: -0.4px;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .hero-system-desc {
            font-size: 13.5px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 24px;
        }

        /* Feature List */
        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 12px 14px;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(3px);
        }

        .feature-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18);
            color: #fde68a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .feature-text-title {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            margin-bottom: 3px;
        }

        .feature-text-sub {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.4;
            margin: 0;
        }

        /* Hero Footer */
        .hero-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 18px;
            position: relative;
            z-index: 1;
        }

        .badge-system-version {
            display: inline-flex;
            align-items: center;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Right Form Panel */
        .auth-form-panel {
            flex: 1;
            padding: 44px 42px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .form-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
            width: fit-content;
        }

        .form-header-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        .form-header-subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin-bottom: 24px;
        }

        /* Input Custom With Leading Icon */
        .form-label-custom {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
            display: block;
        }

        .form-label-custom .text-danger {
            color: #ef4444 !important;
            margin-left: 2px;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-group-custom .input-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
            z-index: 4;
            transition: color 0.15s ease;
        }

        .input-custom {
            height: 46px;
            font-size: 13.5px;
            border-radius: 10px !important;
            border: 1.5px solid #cbd5e1;
            color: #0f172a;
            padding: 10px 14px 10px 42px;
            transition: all 0.2s ease;
            width: 100%;
            background: #f8fafc;
        }

        .input-custom:focus {
            background: #ffffff;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.15) !important;
            outline: none;
        }

        .input-group-custom:focus-within .input-icon-left {
            color: #2563eb;
        }

        .input-custom::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }

        /* Toggle Password Button */
        .btn-toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 6px;
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .btn-toggle-pw:hover {
            color: #1e293b;
        }

        /* Google reCAPTCHA v3 Card Styling */
        .recaptcha-v3-card {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            transition: all 0.2s ease;
        }

        .recaptcha-v3-card:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .recaptcha-v3-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eff6ff;
            color: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            border: 1px solid #bfdbfe;
        }

        .recaptcha-v3-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
        }

        .recaptcha-v3-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.35;
        }

        /* Remember Me Checkbox */
        .remember-checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 14px;
            margin-bottom: 22px;
        }

        .remember-checkbox-wrapper input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #2563eb;
            cursor: pointer;
            margin: 0;
            border-radius: 4px;
        }

        .remember-checkbox-wrapper label {
            margin: 0;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        /* Submit Button */
        .btn-submit-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 14.5px;
            height: 46px;
            border-radius: 10px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-submit-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
            text-decoration: none;
        }

        .btn-submit-login:active {
            transform: translateY(1px);
        }

        /* Security Badges & Footer */
        .auth-extra-info {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .security-badge {
            font-size: 12px;
            color: #16a34a;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .admin-note {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.4;
        }

        .link-mode-tamu {
            display: none;
            margin-top: 12px;
            font-size: 12.5px;
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
        }

        .link-mode-tamu:hover {
            text-decoration: underline;
        }

        /* Footer Copyright */
        .login-page-footer {
            color: rgba(255, 255, 255, 0.75);
            font-size: 12px;
            margin-top: 24px;
            text-align: center;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        /* Responsive Layout (< 992px) */
        @media (max-width: 991.98px) {
            .auth-card-split {
                flex-direction: column;
                max-width: 520px;
            }

            .auth-hero-panel {
                padding: 32px 28px;
            }

            .hero-system-title {
                font-size: 21px;
            }

            .hero-features {
                display: none; /* Hemat ruang di mobile agar form tetap fokus */
            }

            .hero-footer {
                display: none;
            }

            .auth-form-panel {
                padding: 32px 28px;
            }

            .link-mode-tamu {
                display: block; /* Munculkan link kembali ke peta di layar HP */
            }
        }
    </style>
</head>

<body>

    <!-- Top Bar Navigation (Tombol Kembali ke Peta Publik & Status Enkripsi) -->
    <div class="auth-top-bar">
        <a href="{{ route('peta') }}" class="btn-back-peta" title="Kembali ke Tampilan Peta Publik / Tamu">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Peta Publik</span>
        </a>
        <div class="badge-secure-portal">
            <i class="fas fa-shield-alt"></i>
            <span>Portal Internal Resmi BWSK I</span>
        </div>
    </div>

    <!-- Split Card Container -->
    <div class="auth-card-split">

        <!-- SISI KIRI: HERO PANEL (Visual, Instansi, & Ringkasan Fitur WebGIS) -->
        <div class="auth-hero-panel">
            
            <!-- Header Instansi -->
            <div class="instansi-header">
                <div class="instansi-logo-box">
                    <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo Kementerian PUPR">
                </div>
                <div class="instansi-text-wrap">
                    <span class="instansi-ministry">Kementerian Pekerjaan Umum</span>
                    <span class="instansi-ditjen">Direktorat Jenderal Sumber Daya Air</span>
                    <span class="instansi-unit">Balai Wilayah Sungai Kalimantan I Pontianak</span>
                </div>
            </div>

            <!-- Content Utama -->
            <div class="hero-main-content">
                <div class="hero-title-badge">
                    <i class="fas fa-satellite"></i>
                    <span>Sistem Informasi Geospasial</span>
                </div>
                <h1 class="hero-system-title">
                    WebGIS Infrastruktur Sumber Daya Air
                </h1>
                <p class="hero-system-desc">
                    Platform geospasial terpadu pemetaan dan pemantauan Daerah Irigasi, Jaringan Saluran Air, serta Bangunan Air di wilayah Kalimantan Barat.
                </p>

                <!-- Poin-Poin Fitur Utama -->
                <div class="hero-features">
                    <div class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div>
                            <div class="feature-text-title">Pemetaan Jaringan & Bangunan Air</div>
                            <p class="feature-text-sub">Visualisasi spasial akurat seluruh titik bangunan air dan trace saluran irigasi.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <div class="feature-text-title">Manajemen Data Spasial Real-Time</div>
                            <p class="feature-text-sub">Pencarian cepat, filter layer interaktif, dan integrasi data SHP/GeoJSON.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <div class="feature-text-title">Akses Otoritas Staf & Super Admin</div>
                            <p class="feature-text-sub">Manajemen hak akses terpusat untuk Staf GIS dan Super Admin.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Hero Panel -->
            <!-- <div class="hero-footer">
                <span class="badge-system-version">
                    <i class="fas fa-code-branch mr-1 text-warning"></i> 
                </span>
                <small style="color: rgba(255,255,255,0.7); font-size: 11.5px;">
                    <i class="fas fa-check-circle mr-1 text-success"></i> 
                </small>
            </div> -->

        </div>

        <!-- SISI KANAN: FORM LOGIN -->
        <div class="auth-form-panel">

            <div class="form-header-badge">
                <i class="fas fa-key"></i> Autentikasi Pengguna
            </div>
            <h2 class="form-header-title">Masuk ke Sistem</h2>
            <p class="form-header-subtitle">Silakan masukkan email/username dan password staf atau admin Anda.</p>

            {{-- Pesan Notifikasi Sukses --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Pesan Notifikasi Info --}}
            @if(session()->has('info'))
                <div class="alert alert-info alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;" role="alert">
                    <i class="fas fa-info-circle mr-1"></i> {{ session('info') }}
                    <button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Pesan Notifikasi Gagal Login --}}
            @if(session()->has('loginError'))
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('loginError') }}
                    <button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Pesan Notifikasi Error --}}
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                    <button type="button" class="close py-2" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" autocomplete="off" id="loginForm">
                @csrf

                <!-- Email / Username -->
                <div class="form-group mb-3">
                    <label for="email" class="form-label-custom">
                        Email atau Username <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope input-icon-left"></i>
                        <input type="text"
                               name="email"
                               id="email"
                               class="form-control input-custom @error('email') is-invalid @enderror"
                               placeholder="Contoh: admin@gmail.com"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="off">
                    </div>
                    @error('email')
                        <div class="invalid-feedback ml-1 d-block" style="font-size: 12px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group mb-3">
                    <label for="password" class="form-label-custom">
                        Password <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control input-custom pr-5 @error('password') is-invalid @enderror"
                               placeholder="Masukkan password Anda"
                               required
                               autocomplete="off">
                        <button type="button"
                                class="btn-toggle-pw"
                                onclick="togglePasswordVisibility('password', this)"
                                title="Lihat / Sembunyikan password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback ml-1 d-block" style="font-size: 12px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Google reCAPTCHA v3 Protection -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <div class="recaptcha-v3-card mb-3">
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <div class="recaptcha-v3-icon">
                            <i class="fab fa-google"></i>
                        </div>
                        <div class="recaptcha-v3-info flex-grow-1">
                            <div class="recaptcha-v3-title">
                                <i class="fas fa-shield-alt text-success mr-1"></i> Terproteksi Google reCAPTCHA v3
                            </div>
                            <div class="recaptcha-v3-sub">
                                Sistem memverifikasi keamanan otomatis di latar belakang tanpa perlu mengetik kode manual.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="remember-checkbox-wrapper">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat saya di perangkat ini (Remember me)</label>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-submit-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk ke WebGIS</span>
                </button>
            </form>

            <!-- Informasi Tambahan & Keamanan -->
            <div class="auth-extra-info">
                <div class="security-badge">
                    <i class="fas fa-lock"></i>
                    <span>Koneksi Aman & Terproteksi Enkripsi SSL</span>
                </div>
                <div class="admin-note">
                    <i class="fas fa-info-circle mr-1 text-primary"></i> Akun staf dan admin dikelola langsung oleh Super Admin BWSK I
                </div>
                <a href="{{ route('peta') }}" class="link-mode-tamu">
                    <i class="fas fa-map mr-1"></i> Kembali ke Mode Peta Publik &rarr;
                </a>
            </div>

        </div>

    </div>

    <!-- Footer Copyright -->
    <div class="login-page-footer">
        &copy; 2026 WebGIS Balai Wilayah Sungai Kalimantan I Pontianak. Hak Cipta Dilindungi.
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Google reCAPTCHA v3 API Script (Aktif jika Site Key dikonfigurasi di .env) -->
    @if(!empty(config('services.recaptcha.site_key')))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif

    <!-- Script Password Toggle & reCAPTCHA v3 Otomatis -->
    <script>
        function togglePasswordVisibility(inputId, btn) {
            var input = document.getElementById(inputId);
            var icon = btn.querySelector('i');
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                btn.setAttribute('title', 'Sembunyikan password');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                btn.setAttribute('title', 'Lihat password');
            }
        }

        // Handle Submit Login dengan Google reCAPTCHA v3
        document.addEventListener('DOMContentLoaded', function() {
            var loginForm = document.getElementById('loginForm');
            if (!loginForm) return;

            loginForm.addEventListener('submit', function(e) {
                var siteKey = "{{ config('services.recaptcha.site_key') }}";

                // Jika reCAPTCHA v3 dikonfigurasi dan script siap, ambil token terlebih dahulu
                if (siteKey && typeof grecaptcha !== 'undefined') {
                    e.preventDefault();
                    var submitBtn = loginForm.querySelector('.btn-submit-login');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> <span>Memverifikasi keamanan...</span>';
                    }

                    grecaptcha.ready(function() {
                        grecaptcha.execute(siteKey, { action: 'login' }).then(function(token) {
                            var responseInput = document.getElementById('g-recaptcha-response');
                            if (responseInput) {
                                responseInput.value = token;
                            }
                            loginForm.submit();
                        }).catch(function(err) {
                            console.warn('reCAPTCHA error:', err);
                            loginForm.submit();
                        });
                    });
                }
            });
        });
    </script>
</body>

</html>
