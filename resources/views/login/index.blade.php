<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | FFWS BWS Kalimantan I</title>

    <!-- Google Fonts: Plus Jakarta Sans / Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core (SB Admin 2) -->
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(145deg, #1d4fd8 0%, #2563eb 50%, #1e40af 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* Container Card */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Card Header */
        .login-header {
            background-color: #2563eb;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .login-logo {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .login-header-text {
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header-title {
            font-size: 16.5px;
            font-weight: 700;
            line-height: 1.25;
            color: #ffffff;
            letter-spacing: -0.2px;
        }

        .login-header-subtitle {
            font-size: 13px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 3px;
        }

        /* Card Body */
        .login-body {
            padding: 26px 28px 24px 28px;
        }

        .form-label-custom {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            display: block;
        }

        .form-label-custom .text-danger {
            color: #ef4444 !important;
            margin-left: 2px;
        }

        .input-custom {
            height: 44px;
            font-size: 13.5px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            color: #1f2937;
            padding: 10px 14px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            width: 100%;
        }

        .input-custom:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
            outline: none;
        }

        .input-custom::placeholder {
            color: #9ca3af;
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
            padding: 4px;
            color: #6b7280;
            cursor: pointer;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-toggle-pw:hover {
            color: #1f2937;
        }

        /* CAPTCHA Styling */
        .captcha-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            letter-spacing: 6px;
            font-size: 19px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
            color: #1e3a8a;
            padding: 8px 16px;
            user-select: none;
            flex-grow: 1;
            text-align: center;
        }

        .btn-refresh-captcha {
            height: 44px;
            width: 44px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: #f8fafc;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .btn-refresh-captcha:hover {
            background: #e2e8f0;
            color: #1f2937;
        }

        /* Remember Me Checkbox */
        .remember-checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            margin-bottom: 20px;
        }

        .remember-checkbox-wrapper input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #2563eb;
            cursor: pointer;
            margin: 0;
        }

        .remember-checkbox-wrapper label {
            margin: 0;
            font-size: 13px;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }

        /* Submit Button */
        .btn-submit-fews {
            background-color: #2563eb;
            border: 1px solid #2563eb;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            height: 44px;
            border-radius: 8px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
            transition: background-color 0.15s ease, transform 0.1s ease;
            cursor: pointer;
        }

        .btn-submit-fews:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
            text-decoration: none;
        }

        .btn-submit-fews:active {
            transform: translateY(1px);
        }

        /* Version Badge */
        .version-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: #3b82f6;
            border: 1px solid #dbeafe;
            border-radius: 9999px;
            padding: 3px 14px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Footer Copyright */
        .login-footer-text {
            color: rgba(255, 255, 255, 0.85);
            font-size: 12.5px;
            margin-top: 22px;
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.1px;
        }
    </style>
</head>

<body>

    <!-- Card Wrapper -->
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo PU" class="login-logo">
            <div class="login-header-text">
                <span class="login-header-title">Login</span>
                <span class="login-header-subtitle">BWS Kalimantan I Pontianak</span>
            </div>
        </div>

        <!-- Body -->
        <div class="login-body">

            {{-- Pesan Notifikasi Sukses --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" style="font-size: 13px; border-radius: 8px;" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
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

            <form action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf

                <!-- Username -->
                <div class="form-group mb-3">
                    <label for="email" class="form-label-custom">
                        Username <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="email"
                           id="email"
                           class="form-control input-custom @error('email') is-invalid @enderror"
                           placeholder="Masukkan username"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="off">
                    @error('email')
                        <div class="invalid-feedback ml-1" style="font-size: 12px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group mb-3">
                    <label for="password" class="form-label-custom">
                        Password <span class="text-danger">*</span>
                    </label>
                    <div class="position-relative">
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control input-custom pr-5 @error('password') is-invalid @enderror"
                               placeholder="Masukkan password"
                               required
                               autocomplete="off">
                        <button type="button"
                                class="btn-toggle-pw"
                                onclick="togglePasswordVisibility('password', this)"
                                title="Lihat password">
                            <i class="fas fa-eye" style="font-size: 14px;"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback ml-1 d-block" style="font-size: 12px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- CAPTCHA -->
                <div class="form-group mb-3">
                    <label for="captcha" class="form-label-custom">
                        Kode CAPTCHA <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex align-items-center mb-2" style="gap: 8px;">
                        <div class="captcha-box">
                            {{ $captcha }}
                        </div>
                        <button type="button"
                                class="btn-refresh-captcha"
                                onclick="location.reload()"
                                title="Ganti CAPTCHA">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <input type="text"
                           name="captcha"
                           id="captcha"
                           class="form-control input-custom @error('captcha') is-invalid @enderror"
                           placeholder="Masukkan kode CAPTCHA"
                           autocomplete="off"
                           required>
                    @error('captcha')
                        <div class="invalid-feedback ml-1" style="font-size: 12px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="remember-checkbox-wrapper">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember me</label>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-submit-fews">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In to FEWS</span>
                </button>
            </form>

            <!-- Versi -->
            <div class="text-center mt-3">
                <span class="version-badge">Version 1.0.0</span>
            </div>

            <!-- Link Registrasi (Opsional) -->
            <div class="text-center mt-3 pt-1 border-top" style="border-color: #f1f5f9 !important;">
                <span class="text-muted" style="font-size: 12px;">Belum punya akun?</span>
                <a href="{{ route('register') }}" style="font-size: 12px; font-weight: 600; color: #2563eb; text-decoration: none; margin-left: 4px;">Daftar Sekarang</a>
            </div>

        </div>
    </div>

    <!-- Footer Copyright -->
    <div class="login-footer-text">
        &copy; 2026 FEWS BWS Kalimantan I Pontianak
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Script Toggle Password Visibility -->
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
    </script>
</body>

</html>