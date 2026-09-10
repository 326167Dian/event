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
                    $isFull = $slotsLeft <= 0; @endphp @if (session('success'))
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

                                <a href="{{ route('payments.show', $registration->id) }}"
                                    class="btn btn-success w-100 mb-2">
                                    📱 Bayar dengan QRIS
                                </a>

                                <a href="https://wa.me/{{ $event->whatsapp_admin }}" target="_blank"
                                    class="btn btn-outline-secondary w-100">
                                    💬 Konfirmasi Manual via WhatsApp
                                </a>
                            @endif
                        @else
                            @if (!$isFull)
                                @if ($event->price > 0)
                                    <div class="alert alert-info small">
                                        📎 Silakan upload bukti transfer untuk mendaftar event berbayar ini.
                                    </div>
                                    @error('foto')
                                        <div class="alert alert-danger small">{{ $message }}</div>
                                    @enderror
                                    <form action="{{ route('events.register', $event->id) }}" method="POST"
                                        enctype="multipart/form-data" class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="foto" class="form-label fw-semibold">Bukti Transfer</label>
                                            <input type="file" name="foto" id="foto" class="form-control"
                                                accept="image/*" required>
                                            <div class="form-text">Format gambar, maksimal 5 MB.</div>
                                        </div>
                                        <button class="btn btn-success w-100 btn-lg">
                                            🎟️ Daftar &amp; Kirim Bukti Transfer
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
