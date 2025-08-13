<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
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
}
