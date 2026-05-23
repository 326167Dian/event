<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::LAYOUT_START,
                fn (): HtmlString => new HtmlString(
                    '<div x-cloak x-data="{}" x-bind:class="{ \"espire-sidebar-handle-open\": $store.sidebar.isOpen }" class="espire-sidebar-handle-ctn">' .
                        '<button type="button" class="espire-sidebar-handle" x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()" x-bind:aria-label="$store.sidebar.isOpen ? \"Sembunyikan menu\" : \"Tampilkan menu\"" title="Sembunyikan / Tampilkan Menu">' .
                            '<i class="fa-solid fa-chevron-left" x-show="$store.sidebar.isOpen" aria-hidden="true"></i>' .
                            '<i class="fa-solid fa-chevron-right" x-show="! $store.sidebar.isOpen" aria-hidden="true"></i>' .
                        '</button>' .
                    '</div>'
                ),
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString(
                    '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" data-navigate-track />' .
                    '<link href="' . asset('espire/css/app.min.css') . '" rel="stylesheet" data-navigate-track />' .
                    '<link href="' . asset('espire/css/filament-espire.css') . '" rel="stylesheet" data-navigate-track />'
                ),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
