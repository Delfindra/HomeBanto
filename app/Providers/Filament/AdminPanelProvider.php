<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Althinect\FilamentSpatieRolesPermissions\Resources\PermissionResource;
use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource;
use App\Filament\Resources\DietResource;
use App\Filament\Resources\IngredientsResource;
use App\Filament\Resources\MasterDataResource;
use App\Filament\Resources\UserResource;
use App\Models\Diet;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Spatie\Permission\Models\Permission;

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
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentEditProfilePlugin::make()
                ->slug('my-profile')
                ->setTitle('My Profile')
                ->setNavigationLabel('My Profile')
                ->setIcon('heroicon-o-user')
                ->setSort(-1)
                ->customProfileComponents([
                    \App\Livewire\CustomProfileComponent::class,
                ]),
                FilamentSpatieRolesPermissionsPlugin::make()
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                         ->items([
                             NavigationItem::make('Dashboard')
                                 ->icon('heroicon-o-home')
                                 ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                                 ->url(fn (): string => Dashboard::getUrl()),
                         ]),
                    NavigationGroup::make('Admin')
                        ->items([
                            ...DietResource::getNavigationItems(),
                            ...IngredientsResource::getNavigationItems(),
                            ...UserResource::getNavigationItems(),
                            ...MasterDataResource::getNavigationItems(),
                            ...PermissionResource::getNavigationItems(),
                            ...RoleResource::getNavigationItems()
                        ]),
                    NavigationGroup::make('Setting')
                    ->items([
                        NavigationItem::make('My Profile')
                            ->icon('heroicon-o-user')
                            ->url(fn (): string => EditProfilePage::getUrl())
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.my-profile')),
                    ])
                ]);
            })
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
