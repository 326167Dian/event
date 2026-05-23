<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard Admin';

    protected string $view = 'filament.pages.dashboard';

    public function getViewData(): array
    {
        return [
            'totalParticipants' => User::query()->where('role', 'user')->count(),
            'totalEvents' => Event::query()->count(),
            'activeEvents' => Event::query()->where('is_active', 1)->count(),
            'pendingRegistrations' => Registration::query()->where('status', 'waiting_approval')->count(),
            'approvedRegistrations' => Registration::query()->where('status', 'approved')->count(),
            'rejectedRegistrations' => Registration::query()->where('status', 'rejected')->count(),
            'recentParticipants' => User::query()
                ->select(['id', 'name', 'email', 'created_at'])
                ->where('role', 'user')
                ->latest('id')
                ->limit(5)
                ->get(),
        ];
    }
}
