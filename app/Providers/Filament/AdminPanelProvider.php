<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Auth\CustomLogin;
use App\Filament\Resources\Auth\EditProfile;
use App\Filament\Resources\Auth\Register;
use App\Http\Middleware\VerifyActivationUser;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use FilipFonal\FilamentLogManager\FilamentLogManager;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditEnv\FilamentEditEnvPlugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Vormkracht10\FilamentMails\FilamentMailsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(CustomLogin::class)
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                VerifyActivationUser::class,
            ])
            ->plugins([
                FilamentMailsPlugin::make(),
                FilamentLogManager::make(),
                FilamentShieldPlugin::make(),
                FilamentEditEnvPlugin::make()
                    ->showButton(fn () => auth()->user()->id === 1)
                    ->setIcon('heroicon-o-cog'),
                ActivitylogPlugin::make()->label('Log')
                    ->pluralLabel('Logs')
                    ->navigationItem(true)
                    ->navigationGroup('Activity Log')
                    ->navigationIcon('heroicon-o-exclamation-triangle')
                    ->navigationCountBadge(true)
                    ->navigationSort(2)
                    ->authorize(
                        fn () => auth()->user()->id === 1
                    ),
            ])
            ->authMiddleware([
                Authenticate::class,
                VerifyActivationUser::class,
            ])
            ->unsavedChangesAlerts()
            ->sidebarFullyCollapsibleOnDesktop()
            ->spa()
            ->databaseNotifications()
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class,isSimple: false);
    }
}

