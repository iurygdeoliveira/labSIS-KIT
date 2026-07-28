<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Pages;

use App\Filament\Resources\Changelog\ChangelogResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Override;

class DeleteChangelog extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = ChangelogResource::class;

    #[Override]
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->authorize('delete', $this->getRecord());
    }

    #[Override]
    public function getView(): string
    {
        return 'filament.resources.changelog-resource.pages.delete-changelog';
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('delete')
                ->label('Confirmar Exclusão')
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading('Confirmar Exclusão Permanente')
                ->modalDescription('Tem certeza de que deseja excluir permanentemente este registro? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, Excluir')
                ->modalCancelActionLabel('Cancelar')
                ->visible(fn (): bool => Filament::auth()->user()?->can('delete', $this->getRecord()) ?? false)
                ->action(function (): void {
                    $this->authorize('delete', $this->getRecord());
                    $this->getRecord()->delete();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
