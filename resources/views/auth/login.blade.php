@extends('layout')

@section('content')
    <div class="container" style="min-height: 100vh;">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-md-5 col-11">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <img src="{{ asset('images/logo.png') }}" alt="" width="70" height="70" class="d-block mx-auto mb-3">
                        <h3 class="text-center mb-4 fw-bold text-primary">Login</h3>

                        <!-- Alert sukses -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ✅ {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Alert error login -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ⚠️ {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <a href="{{ route('auth.google') }}"
                            class="btn btn-outline-secondary btn-lg w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                            <svg width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.6-6 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.5 6 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z" />
                                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.9 18.9 13 24 13c3 0 5.8 1.1 7.9 3l5.7-5.7C34.5 6 29.5 4 24 4 16 4 9.1 8.5 6.3 14.7z" />
                                <path fill="#4CAF50" d="M24 44c5.4 0 10.3-1.9 14-5.9l-6.5-5.5c-2 1.5-4.6 2.4-7.5 2.4-5.3 0-9.7-3.4-11.3-8.1l-6.5 5C9 39.5 15.9 44 24 44z" />
                                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.3 5.6l6.5 5.5C40.9 36.6 44 30.9 44 24c0-1.2-.1-2.3-.4-3.5z" />
                            </svg>
                            <span class="fw-semibold">Login dengan Google</span>
                        </a>

                        <div class="text-center text-muted small mb-3">— atau login dengan email —</div>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg"
                                    placeholder="Masukkan email saat registrasi" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-lg" placeholder="Masukkan password saat registrasi" required>
                                    <button type="button" class="btn btn-outline-primary" id="togglePassword">
                                        👁️
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg mt-3">
                                Login
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <small>Belum punya akun?
                                <a href="{{ route('register') }}" class="text-decoration-none text-primary fw-semibold">
                                    Daftar Sekarang
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script toggle password --}}
    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        toggleBtn.addEventListener('click', function() {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            this.innerHTML = isHidden ? '🙈' : '👁️';
        });
    </script>

    <style>
        /* Card hover effect */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        /* Input focus effect */
        .form-control:focus {
            border-color: #04bef7;
            box-shadow: 0 0 0 0.2rem rgba(4, 190, 247, 0.25);
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .card-body {
                padding: 2rem 1rem;
            }
        }
    </style>
@endsection
