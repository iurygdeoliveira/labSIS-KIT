<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\UserApproved;
use App\Events\UserRegistered;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\LogoutResponse;
use App\Http\Responses\RegistrationResponse;
use App\Listeners\LogAuthenticationActivity;
use App\Listeners\NotifyAdminNewUser;
use App\Listeners\SendUserApprovedEmail;
use App\Models\AuthenticationLog;
use App\Models\MediaItem;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User as AppUser;
use App\Models\Video;
use App\Observers\MediaItemObserver;
use App\Observers\OrganizationObserver;
use App\Observers\OrganizationUserObserver;
use App\Observers\UserObserver;
use App\Observers\VideoObserver;
use App\Policies\AuthenticationLogPolicy;
use App\Support\AppDateTime;
use App\Tenancy\SpatieTeamResolver as AppSpatieTeamResolver;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as FilamentLoginResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Override;
use Spatie\Permission\Contracts\PermissionsTeamResolver as SpatiePermissionsTeamResolver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->bind(FilamentLoginResponse::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\RegistrationResponse::class, RegistrationResponse::class);
        $this->app->bind(SpatiePermissionsTeamResolver::class, AppSpatieTeamResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configModels();
        $this->configCommands();
        $this->configUrls();
        $this->configDate();
        $this->configEvents();
        $this->configObservers();
        $this->configGates();

    }

    private function configGates(): void
    {
        Gate::policy(AuthenticationLog::class, AuthenticationLogPolicy::class);
        Gate::define('viewPulse', fn (AppUser $user): bool => $user->hasRole('admin'));
    }

    private function configModels(): void
    {
        // Certificar de que todas as propriedades sendo chamadas existam no modelo
        Model::shouldBeStrict();
    }

    // Configura os comandos do banco de dados para proibir a execução de instruções destrutivas
    // quando a aplicação está em execução em um ambiente de produção.

    private function configCommands(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction()
        );
    }

    private function configUrls(): void
    {
        if (app()->isProduction()) {
            URL::forceHttps();
        }
    }

    private function configDate(): void
    {
        Date::use(AppDateTime::class);
        Date::setLocale('pt_BR');
    }

    private function configEvents(): void
    {
        // Registrar listeners manualmente para evitar duplicação
        Event::listen(UserRegistered::class, NotifyAdminNewUser::class);
        Event::listen(UserApproved::class, SendUserApprovedEmail::class);

        // Logs de Autenticação (MongoDB)
        Event::listen(Login::class, LogAuthenticationActivity::class);
        Event::listen(Logout::class, LogAuthenticationActivity::class);
        Event::listen(Failed::class, LogAuthenticationActivity::class);
    }

    private function configObservers(): void
    {
        Video::observe(VideoObserver::class);
        AppUser::observe(UserObserver::class);
        Organization::observe(OrganizationObserver::class);
        MediaItem::observe(MediaItemObserver::class);
        OrganizationUser::observe(OrganizationUserObserver::class);
    }
}
