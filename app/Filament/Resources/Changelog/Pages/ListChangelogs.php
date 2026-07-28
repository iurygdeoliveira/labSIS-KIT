<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Pages;

use App\Filament\Resources\Changelog\ChangelogResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Override;

class ListChangelogs extends ListRecords
{
    protected static string $resource = ChangelogResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncGithub')
                ->label('Sincronizar do GitHub')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Sincronizar Changelog')
                ->modalDescription('Deseja ler o arquivo CHANGELOG.md e atualizar os registros do banco de dados?')
                ->action(function (): void {
                    Artisan::call('changelog:sync-github', ['--fresh' => true]);
                    Notification::make()
                        ->title('Sincronização concluída com sucesso!')
                        ->success()
                        ->send();
                }),
            CreateAction::make()->label('Novo Registro'),
        ];
    }
}
