@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                @if ($event->banner)
                    <img src="{{ asset('storage-public/' . $event->banner) }}" alt="{{ $event->title }}"
                        class="card-img-top event-banner-image">
                @endif

                <div class="card-body p-4">
                    <h2 class="fw-bold text-success mb-3">{{ $event->title }}</h2>

                    <p class="text-muted mb-3">
                        📅 <strong>{{ \Carbon\Carbon::parse($event->start_at)->format('d M Y, H:i') }} -
                            {{ \Carbon\Carbon::parse($event->end_at)->format('d M Y, H:i') }}</strong>
                    </p>

                    <p class="text-secondary fs-6" style="white-space: pre-line;">
                        {!! $event->description !!}
                    </p>

                    @if ($event->price)
                        <div class="p-3 mb-3 rounded-3 bg-success bg-opacity-10 border border-success">
                            <p class="mb-0 fw-bold text-success fs-6">
                                💰 Biaya Pendaftaran: Rp {{ number_format($event->price, 0, ',', '.') }}
                            </p>
                        </div>
                    @endif

                    @php
                        $registrationsCount = $event->registrations->count();
                        $slotsLeft = $event->quota - $registrationsCount;
                    $isFull = $slotsLeft <= 0;
                        $paymentMode = \App\Models\Setting::paymentMode();
                        // Satu tampilan QRIS saja untuk peserta: QRIS Dinamis (GDC lewat hub)
                        // diutamakan (lihat PaymentController::show()); kalau gagal dibuat,
                        // peserta diarahkan balik ke sini dengan flag ini, dan di bawah jatuh
                        // ke QRIS statis (kalau admin sudah upload) atau transfer manual.
                        $autoAvailable = $paymentMode !== 'manual';
                        $showFallback = ! $autoAvailable || session('qris_dynamic_unavailable');
                        $staticQrisPath = \App\Models\Setting::qrisStaticImagePath();
                        $manualInfo = \App\Models\Setting::get('manual_payment_info'); @endphp @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ✅ {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ❌ {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($isFull)
                        <div class="alert alert-danger text-center">
                            ⚠️ Kuota event sudah penuh.
                        </div>
                    @else
                        <p class="text-muted mb-3">
                            Sisa kuota: <strong>{{ $slotsLeft }}</strong> peserta
                        </p>
                    @endif

                    <hr>

                    @auth
                        @php
                            $registration = $event->registrations->where('user_id', auth()->id())->first();
                        @endphp

                        @if ($registration)
                            @if ($registration->status == 'approved')
                                <div class="alert alert-success d-flex align-items-center">
                                    ✅ <span class="ms-2">Kamu sudah terdaftar pada event ini!</span>
                                </div>

                                @if ($event->link)
                                    <p><strong>🎯 Link Event:</strong>
                                        <a href="{{ $event->link }}" target="_blank"
                                            class="text-decoration-none text-success fw-semibold">
                                            {{ $event->link }}
                                        </a>
                                    </p>
                                @endif
                                @if ($event->link_whatsapp)
                                    <p><strong>🎯 Link WhatsApp Group:</strong>
                                        <a href="{{ $event->link_whatsapp }}" target="_blank"
                                            class="text-decoration-none text-success fw-semibold">
                                            {{ $event->link_whatsapp }}
                                        </a>
                                    </p>
                                @endif
                                @if ($event->link_video)
                                    <p><strong>🎯 Link Video Rekaman Webinar:</strong>
                                        <a href="{{ $event->link_video }}" target="_blank"
                                            class="text-decoration-none text-success fw-semibold">
                                            {{ $event->link_video }}
                                        </a>
                                    </p>
                                @endif
                                @if ($event->link_document)
                                    <p><strong>🎯 Link Dokumen:</strong>
                                        <a href="{{ $event->link_document }}" target="_blank"
                                            class="text-decoration-none text-success fw-semibold">
                                            {{ $event->link_document }}
                                        </a>
                                    </p>
                                @endif
                            @elseif ($registration->foto)
                                <div class="alert alert-warning d-flex align-items-center">
                                    ⏳ <span class="ms-2">Bukti transfer kamu sedang diverifikasi oleh admin. Mohon
                                        ditunggu ya.</span>
                                </div>

                                <a href="https://wa.me/{{ $event->whatsapp_admin }}" target="_blank"
                                    class="btn btn-outline-secondary w-100">
                                    💬 Tanya Admin via WhatsApp
                                </a>
                            @else
                                @if (in_array($registration->payment_status, ['expire', 'cancel', 'deny', 'failure']))
                                    <div class="alert alert-danger">
                                        ❌ Pembayaran sebelumnya {{ $registration->payment_status }}. Silakan coba
                                        bayar lagi.
                                    </div>
                                @elseif ($registration->payment_status === 'pending')
                                    <div class="alert alert-warning">
                                        ⏳ Menunggu pembayaran QRIS kamu diselesaikan.
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        ⚠️ Kamu sudah terdaftar, silahkan selesaikan pembayaran.
                                    </div>
                                @endif

                                @if (! $showFallback)
                                    <a href="{{ route('payments.show', $registration->id) }}"
                                        class="btn btn-success w-100 mb-2">
                                        📱 Bayar Sekarang (QRIS)
                                    </a>
                                @else
                                    @if ($staticQrisPath)
                                        <div class="text-center mb-2">
                                            <img src="{{ asset('storage-public/' . $staticQrisPath) }}" alt="QRIS"
                                                class="img-fluid mb-2" style="max-width: 280px;">
                                            <p class="text-muted small mb-0">Scan QRIS di atas, lalu upload bukti pembayaran di bawah.</p>
                                        </div>
                                    @elseif ($manualInfo)
                                        <div class="p-3 mb-2 rounded-3 bg-light border small" style="white-space: pre-line;">{{ $manualInfo }}</div>
                                    @endif
                                    @error('foto')
                                        <div class="alert alert-danger small">{{ $message }}</div>
                                    @enderror
                                    <form action="{{ route('events.register', $event->id) }}" method="POST"
                                        enctype="multipart/form-data" class="mb-2">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $registration->fullname }}">
                                        <input type="hidden" name="no_tlp" value="{{ ltrim(str_replace('+62', '', (string) $registration->phone), '0') }}">
                                        <input type="hidden" name="payment_method" value="manual">
                                        <label for="foto_retry" class="form-label fw-semibold small">Sudah transfer? Upload bukti transfer</label>
                                        <div class="input-group">
                                            <input type="file" name="foto" id="foto_retry" class="form-control" accept="image/*" required>
                                            <button class="btn btn-outline-success">Kirim</button>
                                        </div>
                                    </form>
                                @endif

                                <a href="https://wa.me/{{ $event->whatsapp_admin }}" target="_blank"
                                    class="btn btn-outline-secondary w-100">
                                    💬 Tanya Admin via WhatsApp
                                </a>
                            @endif
                        @else
                            @if (!$isFull)
                                @if ($event->price > 0)
                                    @error('name')
                                        <div class="alert alert-danger small">{{ $message }}</div>
                                    @enderror
                                    @error('no_tlp')
                                        <div class="alert alert-danger small">{{ $message }}</div>
                                    @enderror
                                    @error('foto')
                                        <div class="alert alert-danger small">{{ $message }}</div>
                                    @enderror
                                    <form action="{{ route('events.register', $event->id) }}" method="POST"
                                        enctype="multipart/form-data" class="mt-3" id="registration-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                            <input type="text" name="name" id="name" class="form-control"
                                                value="{{ old('name', auth()->user()->name) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_tlp" class="form-label fw-semibold">No. WhatsApp</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">+62</span>
                                                <input type="tel" name="no_tlp" id="no_tlp" class="form-control"
                                                    placeholder="8123..." inputmode="numeric"
                                                    value="{{ old('no_tlp', auth()->user()->no_tlp ? ltrim(str_replace('+62', '', auth()->user()->no_tlp), '0') : '') }}"
                                                    required>
                                            </div>
                                        </div>

                                        @if ($paymentMode === 'manual')
                                            <input type="hidden" name="payment_method" value="manual">
                                            @if ($staticQrisPath)
                                                <div class="text-center mb-3">
                                                    <img src="{{ asset('storage-public/' . $staticQrisPath) }}" alt="QRIS"
                                                        class="img-fluid mb-2" style="max-width: 280px;">
                                                    <p class="text-muted small mb-0">Scan QRIS di atas, lalu upload bukti pembayaran di bawah.</p>
                                                </div>
                                            @elseif ($manualInfo)
                                                <div class="p-3 mb-3 rounded-3 bg-light border small" style="white-space: pre-line;">{{ $manualInfo }}</div>
                                            @endif
                                            <div class="mb-3">
                                                <label for="foto" class="form-label fw-semibold">Bukti Transfer</label>
                                                <input type="file" name="foto" id="foto" class="form-control"
                                                    accept="image/*" required>
                                                <div class="form-text">Format gambar, maksimal 5 MB.</div>
                                            </div>
                                        @endif

                                        <button class="btn btn-success w-100 btn-lg" id="registration-submit">
                                            @if ($paymentMode === 'manual')
                                                🎟️ Daftar &amp; Kirim Bukti Transfer
                                            @else
                                                🎟️ Daftar &amp; Lanjut Bayar
                                            @endif
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('events.register', $event->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <button class="btn btn-success w-100 btn-lg">
                                            🎟️ Daftar Sekarang
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    @else
                        @if (!$isFull)
                            <!-- Jika belum login -->
                            <div class="alert alert-info text-center">
                                🔒 Silakan login dengan akun Google untuk mendaftar event ini.
                            </div>
                            <a href="{{ route('auth.google', ['event_id' => $event->id]) }}"
                                class="btn btn-outline-secondary w-100 btn-lg d-flex align-items-center justify-content-center gap-2">
                                <svg width="20" height="20" viewBox="0 0 48 48">
                                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.6-6 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.5 6 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z" />
                                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.9 18.9 13 24 13c3 0 5.8 1.1 7.9 3l5.7-5.7C34.5 6 29.5 4 24 4 16 4 9.1 8.5 6.3 14.7z" />
                                    <path fill="#4CAF50" d="M24 44c5.4 0 10.3-1.9 14-5.9l-6.5-5.5c-2 1.5-4.6 2.4-7.5 2.4-5.3 0-9.7-3.4-11.3-8.1l-6.5 5C9 39.5 15.9 44 24 44z" />
                                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.3 5.6l6.5 5.5C40.9 36.6 44 30.9 44 24c0-1.2-.1-2.3-.4-3.5z" />
                                </svg>
                                <span class="fw-semibold">Login dengan Google untuk Mendaftar</span>
                            </a>
                        @endif
                    @endauth

                    <!-- Tombol Kembali di bawah -->
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mt-4">
                        ⬅️ Kembali ke Daftar Event
                    </a>
                </div>
            </div>
        </div>
    </div>
    @push('pixel')
        <script>
            if (typeof window.fbq === 'function') {
                const eventPixelData = {
                    value: @json((float) $event->price),
                    currency: 'IDR',
                    content_name: @json($event->title),
                    content_ids: [@json('EVENT-' . $event->id)],
                    content_type: 'product'
                };
                fbq('track', 'ViewContent', eventPixelData);

                @if (session('pixel_lead'))
                    fbq('track', 'CompleteRegistration', eventPixelData);
                @endif

                const regForm = document.getElementById('registration-form');
                if (regForm) {
                    regForm.addEventListener('submit', function () {
                        const pm = regForm.querySelector('input[name="payment_method"]:checked, input[type="hidden"][name="payment_method"]');
                        if (pm && pm.value === 'manual') {
                            fbq('track', 'AddPaymentInfo', eventPixelData);
                        }
                    });
                }
            }

            // Cadangan Meta CAPI: kirim cookie _fbp/_fbc lewat field tersembunyi,
            // supaya tetap tersimpan walau cookie tidak sampai ke server.
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form || form.tagName !== 'FORM') return;
                if (new URL(form.action).pathname !== new URL(@json(route('events.register', $event->id))).pathname) return;
                const readCookie = function (name) {
                    const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
                    return m ? decodeURIComponent(m[1]) : '';
                };
                [['meta_fbp', '_fbp'], ['meta_fbc', '_fbc']].forEach(function (pair) {
                    const value = readCookie(pair[1]);
                    if (!value) return;
                    let input = form.querySelector('input[name="' + pair[0] + '"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = pair[0];
                        form.appendChild(input);
                    }
                    input.value = value;
                });
            }, true);
        </script>
    @endpush

    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .event-banner-image {
            width: 100%;
            height: auto;
            object-fit: contain;
            background-color: #f8f9fa;
        }
    </style>
@endsection
