<?php

use App\Models\User;

return [
    // 'user-resource' => \App\Filament\Resources\UserResource::class,
    'resources' => [
        'AutenticationLogResource' => \App\Filament\Resources\Authentication\CustomAuthenticationLogResource::class,
    ],

    'authenticable-resources' => [
        User::class,
    ],

    'authenticatable' => [
        'field-to-display' => null,
    ],

    'navigation' => [
        'authentication-log' => [
            'register' => true,
            'sort' => 1,
            'icon' => 'icon-admin',
            'group' => 'Administração',
        ],
    ],

    'sort' => [
        'column' => 'login_at',
        'direction' => 'desc',
    ],
];
