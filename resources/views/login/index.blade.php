<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $title ?? 'Login' }} - FEWS BWS KALIMANTAN I</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Selamat Datang Kembali!</h1>
                            </div>

                            {{-- Pesan Notifikasi Sukses Logout --}}
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

            
                            {{-- Pesan Notifikasi Gagal Login --}}
                            @if(session()->has('loginError'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('loginError') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form action="{{ route('login') }}" method="POST" class="user" autocomplete="off">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" 
                                           class="form-control form-control-user @error('email') is-invalid @enderror"
                                           id="email" 
                                           placeholder="Masukkan Email..." 
                                           value="{{ old('email') }}" 
                                           required autofocus autocomplete="off">
                                    @error('email')
                                        <div class="invalid-feedback ml-3">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group position-relative">
                                    <input type="password" name="password" 
                                           class="form-control form-control-user pr-5"
                                           id="password" 
                                           placeholder="Password" 
                                           required autocomplete="off">
                                    <button type="button" 
                                            class="btn-toggle-password" 
                                            onclick="togglePasswordVisibility('password', this)" 
                                            title="Lihat Password"
                                            style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6e707e; z-index: 5; padding: 4px 6px; outline: none;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>

                                <div class="form-group">
                                  <label class="font-weight-bold ml-2">
                                   Kode CAPTCHA
                                  </label>

                                   <div class="d-flex align-items-center mb-2">
                                   <div class="border rounded bg-light px-3 py-2 font-weight-bold"
                                   style="letter-spacing: 5px; font-size: 20px;">
                                    {{ $captcha }}
                                   </div>

                                   <button type="button"
                                   class="btn btn-secondary ml-2"
                                   onclick="location.reload()"
                                   title="Ganti CAPTCHA">
                                   <i class="fas fa-sync-alt"></i>
                                   </button>
                                </div>

                                   <input type="text"
                                   name="captcha"
                                   id="captcha"
                                   class="form-control form-control-user @error('captcha') is-invalid @enderror"
                                   placeholder="Masukkan kode CAPTCHA"
                                   autocomplete="off"
                                   required>

                                   @error('captcha')
                                   <div class="invalid-feedback ml-3">
                                   {{ $message }}
                                   </div>
                                   @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block font-weight-bold">
                                    Login
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('register') }}">Daftar Sekarang!</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin/js/sb-admin-2.min.js') }}"></script>
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
                btn.setAttribute('title', 'Sembunyikan Password');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                btn.setAttribute('title', 'Lihat Password');
            }
        }
    </script>

</body>
</html>