<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Livewire\CustomProfileComponent;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->emailVerification()
            ->colors([
                'primary' => '#133E87',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentEditProfilePlugin::make()
                ->slug('my-profile')
                ->setTitle('My Profile')
                ->setNavigationLabel('My Profile')
                ->setIcon('heroicon-o-user')
                ->setSort(-1)
                ->customProfileComponents([
                    \App\Livewire\CustomProfileComponent::class,
                ]),
                \Hasnayeen\Themes\ThemesPlugin::make(),
            ])
//            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
//                return $builder->groups([
//                    NavigationGroup::make()
//                        ->items([
//                            NavigationItem::make('Dashboard')
//                                ->icon('heroicon-o-home')
//                                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard'))
//                                ->url(fn(): string => Dashboard::getUrl()),
//                        ]),
//                    NavigationGroup::make('Admin')
//                        ->items([
//                            ...DietResource::getNavigationItems(),
//                            ...IngredientsResource::getNavigationItems(),
//                            ...UserResource::getNavigationItems(),
//                            ...MasterDataResource::getNavigationItems(),
//
//                        ]),
//                    NavigationGroup::make('Setting')
//                        ->items([
//                            NavigationItem::make('My Profile')
//                                ->icon('heroicon-o-user')
//                                ->url(fn(): string => EditProfilePage::getUrl())
//                                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.my-profile')),
//                        ])
//                ]);
//            })
            ->authMiddleware([
                Authenticate::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ]);
    }
}
