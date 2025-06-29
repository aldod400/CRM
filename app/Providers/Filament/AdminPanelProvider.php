<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ReminderNotifications;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Livewire\Livewire;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Gray,
                'info' => Color::Cyan,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'secondary' => Color::Slate,
                'danger' => Color::Red,
            ])
            ->plugin(FilamentFullCalendarPlugin::make())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
            ])
            ->userMenuItems([
                'language_en' => MenuItem::make()
                    ->label('English')
                    ->icon('heroicon-o-language')
                    ->url('/language/en')
                    ->color(fn() => app()->getLocale() == 'en' ? 'primary' : ''),
                'language_ar' => MenuItem::make()
                    ->label('العربية')
                    ->icon('heroicon-o-language')
                    ->url('/language/ar')
                    ->color(fn() => app()->getLocale() == 'ar' ? 'primary' : '')
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
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::topbar.end',
                fn(): HtmlString => new HtmlString(
                    Livewire::mount('notification-bell'),
                )
            );
    }
}
