<!-- ================= MODAL TAMBAH USER / BUAT AKUN ================= -->
<div class="modal fade"
     id="modalCreateUser"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalCreateUserLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content shadow">

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalCreateUserLabel">
                        <i class="fas fa-user-plus mr-2"></i>
                        Tambah Akun Pengguna
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Registrasi mandiri dinonaktifkan. Akun staf/admin dibuat langsung oleh Super Admin dan dapat segera digunakan untuk login.
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="Contoh: Budi Santoso"
                                   value="{{ old('name') }}"
                                   required>
                        </div>
                    </div>

                    <!-- Alamat Email -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">
                            Alamat Email (Username Login) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="Contoh: budi@pu.go.id"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                    </div>

                    <!-- Role / Hak Akses -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">
                            Role / Hak Akses <span class="text-danger">*</span>
                        </label>
                        <select name="role" class="form-control" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role Akun --</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                Admin (Staf Pengelola Data & GIS)
                            </option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                User / Staf (Viewer Peta & Unduh Data)
                            </option>
                            <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>
                                Super Admin (Akses Penuh Manajemen Sistem)
                            </option>
                        </select>
                        <small class="text-muted">
                            Pilih hak akses yang sesuai untuk akun staf atau admin ini.
                        </small>
                    </div>

                    <!-- Password & Konfirmasi -->
                    <div class="form-row">
                        <div class="form-group col-md-6 mb-2">
                            <label class="font-weight-bold small text-dark">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Minimal 6 karakter"
                                   required
                                   minlength="6">
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label class="font-weight-bold small text-dark">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="Ulangi password"
                                   required
                                   minlength="6">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan & Buat Akun
                    </button>
                </div>
            </form>

        </div>

    </div>

</div>

@foreach($users as $u)

    <!-- ================= EDIT USER ================= -->

    <div class="modal fade"
         id="editUser{{ $u->id }}"
         tabindex="-1">

        <div class="modal-dialog">

            <div class="modal-content">

                <form action="{{ route('users.update', $u->id) }}"
                      method="POST">

                    @csrf
                    @method('PUT')


                    <!-- Header -->
                    <div class="modal-header bg-primary text-white">

                        <h5 class="modal-title">

                            <i class="fas fa-user-edit mr-2"></i>
                            Edit Pengguna

                        </h5>

                        <button type="button"
                                class="close text-white"
                                data-dismiss="modal">

                            &times;

                        </button>

                    </div>


                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Email -->
                        <div class="form-group">

                            <label class="font-weight-bold">
                                Email
                            </label>

                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $u->email }}"
                                   readonly>

                        </div>


                        <!-- Nama -->
                        <div class="form-group">

                            <label class="font-weight-bold">
                                Nama Pengguna
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $u->name) }}"
                                   required>

                        </div>


                        <!-- Role -->
                        <div class="form-group">

                            <label class="font-weight-bold">
                                Role / Hak Akses
                            </label>

                            <select name="role"
                                    class="form-control"
                                    required
                                    {{ Auth::id() == $u->id ? 'disabled' : '' }}>

                                <option value="user"
                                    {{ $u->role == 'user' ? 'selected' : '' }}>
                                    User / Staf (Viewer)
                                </option>

                                <option value="admin"
                                    {{ $u->role == 'admin' ? 'selected' : '' }}>
                                    Admin (Staf GIS)
                                </option>

                                <option value="superadmin"
                                    {{ $u->role == 'superadmin' ? 'selected' : '' }}>
                                    Super Admin
                                </option>

                            </select>


                            @if(Auth::id() == $u->id)

                                <input type="hidden"
                                       name="role"
                                       value="{{ $u->role }}">

                                <small class="text-muted">

                                    <i class="fas fa-info-circle mr-1"></i>

                                    Role akun Anda tidak dapat diubah.

                                </small>

                            @endif

                        </div>

                        <!-- Reset Password (Opsional) -->
                        <div class="border-top pt-3 mt-3">
                            <label class="font-weight-bold text-dark d-flex align-items-center mb-1">
                                <i class="fas fa-key text-warning mr-1"></i>
                                Ganti Password (Opsional)
                            </label>
                            <small class="text-muted d-block mb-2">Kosongkan jika tidak ingin mengubah password user ini.</small>

                            <div class="form-row">
                                <div class="form-group col-md-6 mb-2">
                                    <label class="small font-weight-bold">Password Baru</label>
                                    <input type="password"
                                           name="password"
                                           class="form-control form-control-sm"
                                           placeholder="Minimal 6 karakter"
                                           minlength="6">
                                </div>
                                <div class="form-group col-md-6 mb-2">
                                    <label class="small font-weight-bold">Ulangi Password</label>
                                    <input type="password"
                                           name="password_confirmation"
                                           class="form-control form-control-sm"
                                           placeholder="Konfirmasi password"
                                           minlength="6">
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Footer -->
                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="fas fa-save mr-1"></i>
                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- ================= DELETE USER ================= -->

    @if(Auth::id() != $u->id)

        <div class="modal fade"
             id="deleteUser{{ $u->id }}"
             tabindex="-1">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form action="{{ route('users.destroy', $u->id) }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <!-- Header -->
                        <div class="modal-header bg-danger text-white">

                            <h5 class="modal-title">

                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Hapus Pengguna

                            </h5>

                            <button type="button"
                                    class="close text-white"
                                    data-dismiss="modal">

                                &times;

                            </button>

                        </div>


                        <!-- Body -->
                        <div class="modal-body">

                            <p>
                                Apakah Anda yakin ingin menghapus akun:
                            </p>

                            <h5 class="font-weight-bold">
                                {{ $u->name }}
                            </h5>

                            <p class="text-muted">
                                {{ $u->email }}
                            </p>


                            <div class="alert alert-warning">

                                <i class="fas fa-info-circle mr-1"></i>

                                Data yang dihapus tidak dapat dikembalikan.

                            </div>

                        </div>


                        <!-- Footer -->
                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-secondary"
                                    data-dismiss="modal">

                                Batal

                            </button>

                            <button type="submit"
                                    class="btn btn-danger">

                                <i class="fas fa-trash mr-1"></i>

                                Ya, Hapus

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

@endforeach