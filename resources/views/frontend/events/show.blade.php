@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                @if ($event->banner)
                    <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->title }}"
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
                            @else
                                <div class="alert alert-warning">
                                    ⚠️ Kamu sudah terdaftar, tetapi belum menyelesaikan pembayaran.
                                </div>

                                <!-- Pilihan Pembayaran -->
                                <div class="card bg-light border-0 mb-3">
                                    <div class="card-body">
                                        <!--<h6 class="fw-bold text-success mb-2">💳 Pilihan Pembayaran:</h6>-->
                                        <!--<p class="text-muted mb-2">Silakan pilih metode pembayaran:</p>-->

                                        <!-- Atau manual transfer -->
                                        <div class="border-top pt-3">
                                            <p class="fw-bold text-secondary mb-1">💸 Silahkan Transfer :</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Bank:</strong> Bank Syariah Indonesia (BSI)</li>
                                                <li><strong>Nama:</strong> Ahmad Nurdiansyah</li>
                                                <li><strong>No. Rekening:</strong> 7336613455</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <a href="https://wa.me/{{ $event->whatsapp_admin }}" target="_blank"
                                    class="btn btn-outline-success w-100">
                                    💬 Konfirmasi Pembayaran via WhatsApp
                                </a>
                            @endif
                        @else
                            @if (!$isFull)
                                <form action="{{ route('events.register', $event->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    <button class="btn btn-success w-100 btn-lg">
                                        🎟️ Daftar Sekarang
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        @if (!$isFull)
                            <!-- Jika belum login -->
                            <div class="alert alert-info text-center">
                                🔒 Silakan daftar akun dan upload bukti pembayaran untuk mendaftar event ini.
                            </div>
                            <a href="{{ route('register', ['event_id' => $event->id]) }}" class="btn btn-success w-100 btn-lg">
                                🎟️ Registrasi dan Upload Bukti Pembayaran
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
