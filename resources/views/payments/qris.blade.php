@extends('layout')

@section('content')
    <div class="col-lg-6 mx-auto">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 text-center">
                <h4 class="fw-bold mb-3">Pembayaran QRIS</h4>
                <p class="text-muted mb-1">{{ $registration->event->title }}</p>
                <p class="fs-4 fw-bold text-success mb-4">
                    Rp {{ number_format($registration->amount, 0, ',', '.') }}
                </p>

                <div id="payment-pending">
                    @if ($registration->qr_url)
                        <img src="{{ $registration->qr_url }}" alt="QRIS" class="img-fluid mb-3"
                            style="max-width: 280px;">
                    @endif
                    <p class="text-muted small">
                        Scan QR di atas menggunakan aplikasi e-wallet atau m-banking yang mendukung QRIS
                        (GoPay, OVO, DANA, ShopeePay, mobile banking, dll).
                    </p>
                    @if ($registration->expired_at)
                        <p class="text-warning small" id="expiry-info">
                            Selesaikan pembayaran sebelum
                            {{ $registration->expired_at->format('d M Y, H:i') }}
                        </p>
                    @endif
                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                    <span class="ms-2 text-muted">Menunggu pembayaran...</span>
                </div>

                <div id="payment-success" class="d-none">
                    <div class="alert alert-success">
                        ✅ Pembayaran berhasil! Pendaftaran kamu sudah dikonfirmasi.
                    </div>
                    <a href="{{ route('events.show', $registration->event_id) }}" class="btn btn-success w-100">
                        Kembali ke Event
                    </a>
                </div>

                <div id="payment-failed" class="d-none">
                    <div class="alert alert-danger">
                        QR sudah tidak berlaku atau pembayaran gagal/dibatalkan.
                    </div>
                    <a href="{{ route('payments.show', $registration->id) }}" class="btn btn-outline-success w-100">
                        Buat QR Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const statusUrl = @json(route('payments.status', $registration->id));
            const finalStatuses = ['settlement', 'capture', 'expire', 'cancel', 'deny', 'failure'];

            const pendingEl = document.getElementById('payment-pending');
            const successEl = document.getElementById('payment-success');
            const failedEl = document.getElementById('payment-failed');

            function render(status) {
                pendingEl.classList.add('d-none');
                successEl.classList.add('d-none');
                failedEl.classList.add('d-none');

                if (status === 'settlement' || status === 'capture') {
                    successEl.classList.remove('d-none');
                } else if (['expire', 'cancel', 'deny', 'failure'].includes(status)) {
                    failedEl.classList.remove('d-none');
                } else {
                    pendingEl.classList.remove('d-none');
                }
            }

            let currentStatus = @json($registration->payment_status);
            render(currentStatus);

            if (!finalStatuses.includes(currentStatus)) {
                const poll = setInterval(() => {
                    fetch(statusUrl, { headers: { Accept: 'application/json' } })
                        .then((res) => res.json())
                        .then((data) => {
                            render(data.payment_status);
                            if (finalStatuses.includes(data.payment_status)) {
                                clearInterval(poll);
                            }
                        })
                        .catch(() => {});
                }, 5000);
            }
        })();
    </script>
@endsection
