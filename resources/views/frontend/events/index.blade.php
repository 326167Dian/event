@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="text-center mb-5">
            @auth
                <h1 class="fw-bold text-success mb-2">
                    👋 Selamat Datang, {{ auth()->user()->name }}!
                </h1>
                <p class="text-muted">Temukan dan ikuti berbagai event menarik yang cocok untukmu.</p>
            @else
                <h1 class="fw-bold text-success mb-2">📅 Daftar Event</h1>
                <p class="text-muted">Temukan dan ikuti berbagai event menarik di platform kami.</p>
            @endauth
        </div>

        @php
            $activeEvents = $events->where('is_active', 1);
        @endphp

        @if ($activeEvents->isEmpty())
            <div class="alert alert-warning text-center">
                Belum ada event aktif yang tersedia saat ini.
            </div>
        @else
            <div class="row g-4">
                @foreach ($activeEvents as $event)
                    @php
                        $registrationsCount = $event->registrations->count();
                        $isFull = $registrationsCount >= $event->quota;
                    @endphp
                    <div class="col-md-4 col-sm-6">
                        <div class="card shadow-sm border-0 h-100 event-card rounded-4 overflow-hidden">
                            @if ($event->banner)
                                <img src="{{ asset('storage/' . $event->banner) }}" class="card-img-top"
                                    alt="{{ $event->title }}" style="height: 220px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex justify-content-center align-items-center"
                                    style="height: 220px;">
                                    <span class="text-muted">Tidak ada banner</span>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold text-dark">{{ $event->title }}</h5>
                                <p class="text-muted mb-2 small">
                                    📆 {{ \Carbon\Carbon::parse($event->start_at)->format('d M Y, H:i') }} - {{ \Carbon\Carbon::parse($event->end_at)->format('d M Y, H:i') }}
                                </p>
                                <p class="text-secondary flex-grow-1">{!! Str::limit($event->description, 90) !!}</p>

                                @if ($isFull)
                                    <span class="badge bg-danger w-100 py-2 mt-auto">Kuota Penuh</span>
                                @else
                                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-success w-100 mt-auto">
                                        Lihat Detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <style>
        .event-card {
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
