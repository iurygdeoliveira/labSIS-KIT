<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Trait\Filament\HasBackButtonAction;
use App\Trait\Filament\NotificationsTrait;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class EditUser extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = UserResource::class;

    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->getKey() !== Auth::id();
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            $this->getBackButtonAction(),
            ViewAction::make(),
        ];

        // Só mostra o botão de deletar se não for o usuário logado
        if ($this->canDelete) {
            $actions[] = DeleteAction::make()
                ->successNotification(Notification::make())
                ->after(fn () => $this->notifySuccess('Usuário excluído com sucesso.'));
        }

        return $actions;
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        // Sincroniza suspended_at com is_suspended
        if ($this->record->is_suspended && $this->record->suspended_at === null) {
            $this->record->suspended_at = now();
            $this->record->save();
        }

        if (! $this->record->is_suspended && $this->record->suspended_at !== null) {
            $this->record->suspended_at = null;
            $this->record->save();
        }

        if ($this->record->getKey() === Auth::id() && $this->record->is_suspended) {
            // Evita auto-suspensão via edição direta
            $this->record->forceFill([
                'is_suspended' => false,
                'suspended_at' => null,
            ])->save();

            $this->notifyDanger('Você não pode suspender a si mesmo. Alteração revertida.');

            $this->redirect($this->getResource()::getUrl('index'));

            return;
        }

        $this->notifySuccess('Usuário atualizado com sucesso.');
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
