@php
    use App\Filament\Resources\Users\UserResource;
    use App\Filament\Resources\Events\EventResource;
    use App\Filament\Resources\Registrations\RegistrationResource;

    $participantsUrl = UserResource::getUrl('index', [
        'filters' => ['role' => ['value' => 'user']],
    ]);
    $activeEventsUrl = EventResource::getUrl('index', [
        'filters' => ['is_active' => ['value' => '1']],
    ]);
    $approvedUrl = RegistrationResource::getUrl('index', [
        'filters' => ['status' => ['value' => 'approved']],
    ]);
    $pendingUrl = RegistrationResource::getUrl('index', [
        'filters' => ['status' => ['value' => 'waiting_approval']],
    ]);
@endphp

<x-filament-panels::page>
    <div class="container-fluid p-0">
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ $participantsUrl }}" class="text-decoration-none espire-stat-link">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="espire-stat-icon bg-primary">
                                <i class="feather icon-users"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Peserta Register</p>
                                <h2 class="mb-0">{{ number_format($totalParticipants) }}</h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ $activeEventsUrl }}" class="text-decoration-none espire-stat-link">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="espire-stat-icon bg-info">
                                <i class="feather icon-calendar"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Event Aktif</p>
                                <h2 class="mb-0">{{ number_format($activeEvents) }} / {{ number_format($totalEvents) }}</h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ $approvedUrl }}" class="text-decoration-none espire-stat-link">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="espire-stat-icon bg-success">
                                <i class="feather icon-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Approved</p>
                                <h2 class="mb-0">{{ number_format($approvedRegistrations) }}</h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <a href="{{ $pendingUrl }}" class="text-decoration-none espire-stat-link">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="espire-stat-icon bg-warning">
                                <i class="feather icon-clock"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Menunggu Approval</p>
                                <h2 class="mb-0 text-warning">{{ number_format($pendingRegistrations) }}</h2>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Status Pendaftaran Event</h5>
                        <div class="espire-chart-ctn">
                            <canvas
                                id="espire-registration-chart"
                                data-approved="{{ $approvedRegistrations }}"
                                data-pending="{{ $pendingRegistrations }}"
                                data-rejected="{{ $rejectedRegistrations }}"
                            ></canvas>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-top mt-3">
                            <span><i class="feather icon-circle text-success"></i> Approved</span>
                            <span class="badge bg-success">{{ number_format($approvedRegistrations) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span><i class="feather icon-circle text-warning"></i> Waiting Approval</span>
                            <span class="badge bg-warning text-dark">{{ number_format($pendingRegistrations) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span><i class="feather icon-circle text-danger"></i> Rejected</span>
                            <span class="badge bg-danger">{{ number_format($rejectedRegistrations) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-7">
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

    <script>
        (function () {
            function initEspireDashboardChart() {
                const canvas = document.getElementById('espire-registration-chart');

                if (! canvas || typeof Chart === 'undefined') {
                    return;
                }

                if (window.espireDashboardChart) {
                    window.espireDashboardChart.destroy();
                }

                window.espireDashboardChart = new Chart(canvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Approved', 'Waiting Approval', 'Rejected'],
                        datasets: [{
                            data: [
                                Number(canvas.dataset.approved),
                                Number(canvas.dataset.pending),
                                Number(canvas.dataset.rejected),
                            ],
                            backgroundColor: ['#00c569', '#ffc833', '#f46363'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutoutPercentage: 65,
                        legend: {
                            display: false,
                        },
                    },
                });
            }

            document.addEventListener('DOMContentLoaded', initEspireDashboardChart);
            document.addEventListener('livewire:navigated', initEspireDashboardChart);
        })();
    </script>
</x-filament-panels::page>
