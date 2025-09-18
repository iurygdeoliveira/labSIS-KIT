<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Trait\Filament\HasBackButtonAction;
use App\Trait\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class DeleteTenant extends ViewRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = TenantResource::class;

    public function getView(): string
    {
        return 'filament.resources.tenants.pages.delete-tenant';
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
                ->modalDescription('Tem certeza de que deseja excluir permanentemente este tenant? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, Excluir')
                ->modalCancelActionLabel('Cancelar')
                ->action(function (): void {
                    $tenant = $this->getRecord();

                    if ($tenant->users()->exists()) {
                        $this->notifyDanger(
                            'Não é possível excluir o tenant',
                            'Existem usuários associados a este tenant. Remova as associações antes de excluir.',
                        );

                        return;
                    }

                    $tenant->delete();
                    $this->notifySuccess('Tenant excluído com sucesso');

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if (! $record) {
            return 'Excluir Tenant';
        }

        return 'Excluir: '.($record->name ?? 'Tenant sem nome');
    }
}
