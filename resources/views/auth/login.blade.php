@extends('layout')

@section('content')
    <div class="container" style="min-height: 70vh;">
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
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ⚠️ {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="alert alert-light border text-center small mb-4">
                            Login hanya melalui akun Google.
                        </div>

                        <a href="{{ route('auth.google') }}"
                            class="btn btn-outline-secondary btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
                            <svg width="20" height="20" viewBox="0 0 48 48">
                                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.6-6 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.5 6 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z" />
                                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.9 18.9 13 24 13c3 0 5.8 1.1 7.9 3l5.7-5.7C34.5 6 29.5 4 24 4 16 4 9.1 8.5 6.3 14.7z" />
                                <path fill="#4CAF50" d="M24 44c5.4 0 10.3-1.9 14-5.9l-6.5-5.5c-2 1.5-4.6 2.4-7.5 2.4-5.3 0-9.7-3.4-11.3-8.1l-6.5 5C9 39.5 15.9 44 24 44z" />
                                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.3 5.6l6.5 5.5C40.9 36.6 44 30.9 44 24c0-1.2-.1-2.3-.4-3.5z" />
                            </svg>
                            <span class="fw-semibold">Login dengan Google</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Card hover effect */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .card-body {
                padding: 2rem 1rem;
            }
        }
    </style>
@endsection
