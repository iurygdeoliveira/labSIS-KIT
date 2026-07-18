<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AuthPanelProvider;
use App\Providers\Filament\UserPanelProvider;

return [
    AppServiceProvider::class,
    AuthPanelProvider::class,
    AdminPanelProvider::class,
    UserPanelProvider::class,
];
