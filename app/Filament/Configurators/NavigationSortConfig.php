<?php

declare(strict_types=1);

namespace App\Filament\Configurators;

use App\Filament\Clusters\Permissions\PermissionsCluster;
use App\Filament\Pages\Mail\Templates;
use App\Filament\Resources\Authentication\AuthenticationLogResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Organization\OrganizationResource;
use App\Filament\Resources\Security\SecurityEventResource;
use App\Filament\Resources\Users\UserResource;

class NavigationSortConfig
{
    public static function getSortOrder(string $class): ?int
    {
        $order = [
            // Admin Panel / User Panel sorting config
            UserResource::class => 2,
            MediaResource::class => 1,
            AuthenticationLogResource::class => 3,
            SecurityEventResource::class => 4,
            OrganizationResource::class => 5,
            Templates::class => 6,
            PermissionsCluster::class => 7,
        ];

        // Busca exata pela classe
        if (isset($order[$class])) {
            return $order[$class];
        }

        if (! class_exists($class)) {
            return null;
        }

        // Fallback: busca pela classe pai (herança)
        foreach ($order as $registeredClass => $sort) {
            if (is_subclass_of($class, $registeredClass)) {
                return $sort;
            }
        }

        return null;
    }
}
