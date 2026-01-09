<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class DeleteUser extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = UserResource::class;

    #[\Override]
    public function getView(): string
    {
        return 'filament.resources.users.pages.delete-user';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('delete')
                ->label('Confirmar Exclusão')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Exclusão Permanente')
                ->modalDescription('Tem certeza de que deseja excluir permanentemente este usuário? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, Excluir')
                ->modalCancelActionLabel('Cancelar')
                ->action(function (): void {
                    $this->getRecord()->delete();

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

        if (! $record instanceof \App\Models\User) {
            return 'Excluir Usuário';
        }

        return 'Excluir: '.($record->name);
    }
}
