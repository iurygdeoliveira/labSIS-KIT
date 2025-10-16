<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\UserApproved;
use App\Events\UserRegistered;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\LogoutResponse;
use App\Listeners\NotifyAdminNewUser;
use App\Listeners\SendUserApprovedEmail;
use App\Models\User as AppUser;
use App\Models\Video;
use App\Observers\UserObserver;
use App\Observers\VideoObserver;
use App\Tenancy\SpatieTeamResolver as AppSpatieTeamResolver;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as FilamentLoginResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\RegistrationResponse::class, \App\Http\Responses\RegistrationResponse::class);
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
        $this->configFilamentColors();
        $this->configStorage();
        $this->configEvents();
        $this->configObservers();
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
        Date::use(CarbonImmutable::class);
        Carbon::setLocale('pt_BR');
    }

    private function configFilamentColors(): void
    {
        FilamentColor::register([
            'danger' => Color::hex('#D93223'),
            'warning' => Color::hex('#F28907'),
            'success' => Color::hex('#52a0fa'),
            'primary' => Color::hex('#014029'),
            'secondary' => Color::Gray,
        ]);
    }

    private function configStorage(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        // Garante as pastas de mídia no MinIO (disco s3) apenas se estiver configurado
        try {
            if (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) {
                $directories = ['audios', 'images', 'documents', 'avatar'];

                foreach ($directories as $directory) {
                    Storage::disk('s3')->makeDirectory($directory);
                    Storage::disk('s3')->put(
                        "{$directory}/.keep",
                        '',
                        [
                            'visibility' => 'private',
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            // Silenciosamente ignora erros de configuração do S3/MinIO
            // Útil para ambientes de produção onde o S3 não está configurado
        }
    }

    private function configEvents(): void
    {
        // Registrar listeners manualmente para evitar duplicação
        $this->app['events']->listen(UserRegistered::class, NotifyAdminNewUser::class);
        $this->app['events']->listen(UserApproved::class, SendUserApprovedEmail::class);
    }

    private function configObservers(): void
    {
        Video::observe(VideoObserver::class);
        AppUser::observe(UserObserver::class);
    }
}
