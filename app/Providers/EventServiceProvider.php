<?php

namespace App\Providers;

use App\Events\UserEmailVerified;
use App\Events\UserRegistered;
use App\Listeners\NotifyAdminNewUser;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeEmail::class,
            SendEmailVerificationNotification::class,
            NotifyAdminNewUser::class,
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
