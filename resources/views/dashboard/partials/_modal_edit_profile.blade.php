{{-- ============================================================
     MODAL EDIT PROFIL PENGGUNA
     File : resources/views/dashboard/partials/_modal_edit_profile.blade.php
     Dapat dipanggil dari : peta.blade.php & layout/master.blade.php
     ============================================================ --}}
@auth
<div class="modal fade" id="modalEditProfile" tabindex="-1" role="dialog" aria-labelledby="modalEditProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px;">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);">

            {{-- Header Modal --}}
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f2e59 0%, #1e3a8a 100%); border-bottom: none; padding: 18px 24px;">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fbbf24;">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0 text-white" id="modalEditProfileLabel" style="font-size: 16px; letter-spacing: -0.2px;">
                            Edit Profil Saya
                        </h5>
                        <small style="color: rgba(255, 255, 255, 0.8); font-size: 11.5px;">
                            Perbarui identitas akun, email, atau kata sandi Anda
                        </small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.85; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Form Edit Profil --}}
            <form action="{{ route('profile.update') }}" method="POST" id="formEditProfileModal" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="modal-body p-4" style="background: #f8fafc; font-size: 13px;">

                    {{-- Ringkasan Akun Saat Ini --}}
                    <div class="bg-white p-3 rounded-lg border mb-3 d-flex align-items-center justify-content-between" style="border-radius: 12px; border-color: #e2e8f0 !important;">
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: #1e3a8a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ Auth::user()->name }}</div>
                                <div class="text-muted" style="font-size: 12px;">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        <div>
                            @if(Auth::user()->role === 'superadmin')
                                <span class="badge badge-danger px-2.5 py-1" style="font-size: 11px; border-radius: 6px;">
                                    <i class="fas fa-crown mr-1"></i> Super Admin
                                </span>
                            @else
                                <span class="badge badge-warning text-dark px-2.5 py-1" style="font-size: 11px; border-radius: 6px;">
                                    <i class="fas fa-user-shield mr-1"></i> Admin / Staf
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Nama Pengguna --}}
                    <div class="form-group mb-3">
                        <label for="edit_profile_name" class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">
                            Nama Lengkap / Username <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; color: #64748b;">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <input type="text"
                                   name="name"
                                   id="edit_profile_name"
                                   class="form-control border-left-0 @error('name') is-invalid @enderror"
                                   style="border-radius: 0 8px 8px 0; border-color: #cbd5e1; font-size: 13px;"
                                   value="{{ old('name', Auth::user()->name) }}"
                                   placeholder="Masukkan nama pengguna"
                                   required>
                        </div>
                        @error('name')
                            <small class="text-danger d-block mt-1 font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Alamat Email --}}
                    <div class="form-group mb-3">
                        <label for="edit_profile_email" class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; color: #64748b;">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <input type="email"
                                   name="email"
                                   id="edit_profile_email"
                                   class="form-control border-left-0 @error('email') is-invalid @enderror"
                                   style="border-radius: 0 8px 8px 0; border-color: #cbd5e1; font-size: 13px;"
                                   value="{{ old('email', Auth::user()->email) }}"
                                   placeholder="Contoh: admin@pu.go.id"
                                   required>
                        </div>
                        @error('email')
                            <small class="text-danger d-block mt-1 font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Bagian Ubah Password (Opsional) --}}
                    <div class="p-3 bg-white rounded border mt-3" style="border-radius: 10px; border-color: #e2e8f0 !important;">
                        <div class="d-flex align-items-center mb-2" style="gap: 6px;">
                            <i class="fas fa-key text-warning" style="font-size: 13px;"></i>
                            <span class="font-weight-bold text-dark" style="font-size: 12.5px;">Ganti Password (Opsional)</span>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 11.5px; line-height: 1.4;">
                            Biarkan kolom di bawah ini <b>kosong</b> jika Anda tidak ingin mengganti kata sandi.
                        </p>

                        {{-- Password Baru --}}
                        <div class="form-group mb-2">
                            <label for="edit_profile_password" class="text-secondary mb-1" style="font-size: 12px;">Password Baru</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password"
                                       id="edit_profile_password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; font-size: 12.5px;"
                                       placeholder="Minimal 6 karakter"
                                       autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary border-left-0"
                                            type="button"
                                            style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;"
                                            onclick="toggleProfilePw('edit_profile_password', this)">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <small class="text-danger d-block mt-1 font-weight-bold">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="form-group mb-0">
                            <label for="edit_profile_password_confirmation" class="text-secondary mb-1" style="font-size: 12px;">Ulangi Password Baru</label>
                            <div class="input-group">
                                <input type="password"
                                       name="password_confirmation"
                                       id="edit_profile_password_confirmation"
                                       class="form-control"
                                       style="border-radius: 8px 0 0 8px; border-color: #cbd5e1; font-size: 12.5px;"
                                       placeholder="Ulangi password baru"
                                       autocomplete="new-password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary border-left-0"
                                            type="button"
                                            style="border-radius: 0 8px 8px 0; border-color: #cbd5e1;"
                                            onclick="toggleProfilePw('edit_profile_password_confirmation', this)">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer bg-white border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light px-3 font-weight-bold text-secondary" data-dismiss="modal" style="border-radius: 8px; font-size: 12.5px; border: 1px solid #cbd5e1;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px; font-size: 12.5px; background: #1e3a8a; border-color: #1e3a8a;">
                        <i class="fas fa-check-circle mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function toggleProfilePw(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('i');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    @if($errors->has('name') || $errors->has('email') || $errors->has('password'))
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined') {
                $('#modalEditProfile').modal('show');
            }
        });
    @endif
</script>
@endauth

