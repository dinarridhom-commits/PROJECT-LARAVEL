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
                                    User
                                </option>

                                <option value="admin"
                                    {{ $u->role == 'admin' ? 'selected' : '' }}>
                                    Admin
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