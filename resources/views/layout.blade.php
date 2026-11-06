<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mysifa - Event Platform</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @include('partials.meta-pixel')
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

        /* 🌿 Tombol WhatsApp melayang */
        .whatsapp-float {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 1050;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #25d366;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.08);
        }

        .whatsapp-float svg {
            width: 30px;
            height: 30px;
            fill: #fff;
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

    {{-- 🌿 Tombol WhatsApp melayang --}}
    @php($waNumber = \App\Models\Setting::whatsappNumber())
    @if ($waNumber)
        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo Admin, saya ingin bertanya terkait pendaftaran/pembayaran event.') }}"
            class="whatsapp-float" target="_blank" rel="noopener" title="Hubungi Admin via WhatsApp"
            aria-label="Hubungi Admin via WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path
                    d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.628.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.337-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
            </svg>
        </a>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Event Meta Pixel per halaman (ViewContent, InitiateCheckout, Purchase, dll) --}}
    @stack('pixel')
</body>

</html>
