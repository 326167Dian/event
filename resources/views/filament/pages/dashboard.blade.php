<x-filament-panels::page>
    <div class="container-fluid p-0">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Peserta Register</p>
                        <h2 class="mb-0">{{ number_format($totalParticipants) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Event Aktif</p>
                        <h2 class="mb-0">{{ number_format($activeEvents) }} / {{ number_format($totalEvents) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pendaftaran Menunggu Approval</p>
                        <h2 class="mb-0 text-warning">{{ number_format($pendingRegistrations) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Status Pendaftaran Event</h5>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span>Approved</span>
                            <span class="badge bg-success">{{ number_format($approvedRegistrations) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span>Waiting Approval</span>
                            <span class="badge bg-warning text-dark">{{ number_format($pendingRegistrations) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span>Rejected</span>
                            <span class="badge bg-danger">{{ number_format($rejectedRegistrations) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Peserta Terbaru</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentParticipants as $participant)
                                        <tr>
                                            <td>{{ $participant->name }}</td>
                                            <td>{{ $participant->email }}</td>
                                            <td>{{ $participant->created_at?->format('d M Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada data peserta.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
