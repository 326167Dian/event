@extends('layout')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <img src="{{ asset('images/logo.png') }}" alt="" width="70" height="70" class="d-block mx-auto mb-3">
                    <h3 class="text-center mb-4 fw-bold text-primary">Daftar Akun Baru</h3>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control form-control-lg"
                                placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg"
                                placeholder="nama@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_tlp" class="form-label fw-semibold">Nomor Telepon</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light">+62</span>
                                <input type="text" name="no_tlp" id="no_tlp" class="form-control"
                                    placeholder="8123..." required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group input-group-lg">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="" required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('password', this)">👁</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-group input-group-lg">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="" required>
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword('password_confirmation', this)">👁</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <small>Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                                Login di sini
                            </a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script show/hide password --}}
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                btn.textContent = "🙈";
            } else {
                input.type = "password";
                btn.textContent = "👁";
            }
        }
    </script>
@endsection
