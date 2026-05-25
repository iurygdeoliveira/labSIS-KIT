<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Models\Team;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DeleteTeam extends ViewRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = TeamResource::class;

    #[\Override]
    public function getView(): string
    {
        return 'filament.resources.teams.pages.delete-team';
    }

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
                ->modalDescription('Tem certeza de que deseja excluir permanentemente este team? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, Excluir')
                ->modalCancelActionLabel('Cancelar')
                ->action(function (): void {
                    $team = $this->getRecord();

                    if (! $team instanceof Team) {
                        return;
                    }

                    if ($team->members()->exists()) {
                        $this->notifyDanger(
                            'Não é possível excluir o team',
                            'Existem usuários associados a este team. Remova as associações antes de excluir.',
                        );

                        return;
                    }

                    Team::destroy($team->getKey());
                    $this->notifySuccess('Team excluído com sucesso');

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    #[\Override]
    public function getTitle(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof Team) {
            return 'Excluir Team';
        }

        return 'Excluir: '.($record->name ?: 'Team sem nome');
    }
}
