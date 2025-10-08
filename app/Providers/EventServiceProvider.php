<?php

namespace App\Providers;

use App\Events\TenantCreated;
use App\Events\UserEmailVerified;
use App\Events\UserRegistered;
use App\Listeners\AssociateUserAsOwner;
use App\Listeners\NotifyAdminNewUser;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            // SendWelcomeEmail::class,
            // NotifyAdminNewUser::class,
        ],

        TenantCreated::class => [
            // AssociateUserAsOwner::class,
        ],

        UserEmailVerified::class => [
            // Adicionar listeners para email verificado se necessário
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
