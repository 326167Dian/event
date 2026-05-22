<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mysifa - Event Platform</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        /* 🌿 Navbar hijau elegan */
        .navbar {
            background-color: #04bef7 !important;
            /* hijau utama */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #fff !important;
        }

        .navbar .nav-link {
            color: #fff !important;
            transition: color 0.2s;
        }

        .navbar .nav-link:hover {
            color: #d1e7dd !important;
        }

        /* 🌿 Tombol */
        .btn-login {
            background-color: #fff;
            color: #04bef7;
            border: none;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background-color: #d1e7dd;
            color: #145c32;
        }

        .btn-register {
            border: 2px solid #fff;
            color: #fff;
            background: transparent;
            transition: all 0.2s;
        }

        .btn-register:hover {
            background-color: #fff;
            color: #198754;
        }

        footer {
            background: #04bef7;
            color: #e9ecef;
            padding: 20px 0;
            margin-top: auto;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- 🌿 Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">🎟️ Event Platform</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                @auth
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white fw-semibold">👋 {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-light btn-sm px-3">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-login btn-sm px-3 fw-semibold">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-register btn-sm px-3 fw-semibold">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- 🌿 Footer -->
    <footer class="text-center">
        <div class="container">
            <small>
                © {{ date('Y') }} <strong>MySIFA</strong> — All Rights Reserved |
                <a href="{{ route('home') }}">Home</a>
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
