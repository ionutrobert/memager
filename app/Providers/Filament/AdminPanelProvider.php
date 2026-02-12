<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\BoringAvatarsProvider;
use App\Filament\AvatarProviders\DicebearAvatarsProvider;
use App\Filament\AvatarProviders\OtherAvatarsProvider;
use App\Filament\Pages\ViewLog;
use Awcodes\FilamentQuickCreate\QuickCreatePlugin;
use Edwink\FilamentUserActivity\FilamentUserActivityPlugin;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\SetLang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Saade\FilamentLaravelLog\FilamentLaravelLogPlugin;
use Illuminate\Support\Facades\Gate;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->breadcrumbs(true)
            ->colors([
                'primary' => Color::Green,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
                SetLang::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->defaultAvatarProvider(DicebearAvatarsProvider::class)
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Administrare')
                     ->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('Tribunalul Valcea Dosare')
                    ->url('
                    https://portal.just.ro/90/SitePages/Rezultate_dosare.aspx?k=288&a=%20MJmpIdInstitutie=90', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group('Link-uri Utile')
                    ->sort(6)
                    ->visible(fn(): bool => auth()->user()->can('page_ViewLog')),

            ])
            ->plugins([
                FilamentUserActivityPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                ->gridColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 2
                ])
                ->sectionColumnSpan(1)
                ->checkboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 4,
                ])
                ->resourceCheckboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                ]),
                FilamentLaravelLogPlugin::make()
                   // ->viewLog(ViewLog::class)
                    ->navigationGroup('System Tools')
                    ->navigationLabel('Application Logs')
                    ->navigationIcon('heroicon-o-bug-ant')
                    ->navigationSort(99)
                    ->slug('logs')
                    ->authorize(
                        //fn(): bool => Filament::auth()->user()->can('page_ViewLog')
                    ),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        navigationGroup: 'Settings', // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: true, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())
                    ->passwordUpdateRules(
                        rules: [Password::default()->mixedCase()->uncompromised(3)], // you may pass an array of validation rules as well. (default = ['min:8'])
                        requiresCurrentPassword: true, // when false, the user can update their password without entering their current password. (default = true)
                    ),


                FilamentAuthenticationLogPlugin::make(),

                QuickCreatePlugin::make()
                    ->includes([
                        \App\Filament\Resources\UserResource::class,
                    ]),
            ]);

        // Locale switcher — see git history for prior implementation.
        $panel = $panel
            ->renderHook(
                PanelsRenderHook::USER_MENU_PROFILE_AFTER,
                fn (): \Illuminate\Contracts\View\View => view('filament.hooks.lang-switcher'),
            );

        return $panel;
    }
}
