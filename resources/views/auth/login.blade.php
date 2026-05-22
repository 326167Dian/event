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

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" id="email" class="form-control form-control-lg"
                                    placeholder="Masukkan email anda" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password"
                                        class="form-control form-control-lg" placeholder="Masukkan password" required>
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
