<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

/**
 * @property-read \App\Models\User|null $record
 */
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
        return [
            $this->getBackButtonAction(),
            $this->getSaveFormAction()->formId('form')->icon('heroicon-s-check'),
            Action::make('delete')
                ->label(__('filament-actions::delete.single.label'))
                ->icon('heroicon-s-trash')
                ->color('danger')
                ->url(fn (): string => UserResource::getUrl('delete', ['record' => $this->getRecord()])),
            // ViewAction::make(),
        ];
    }

    #[\Override]
    protected function getFormActions(): array
    {
        return [];
    }

    #[\Override]
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();
        if (! $record instanceof \App\Models\User) {
            return;
        }

        // Sincroniza suspended_at com is_suspended
        if ($record->is_suspended && $record->suspended_at === null) {
            $record->suspended_at = now();
            $record->save();
        }

        if (! $record->is_suspended && $record->suspended_at !== null) {
            $record->suspended_at = null;
            $record->save();
        }

        if ($record->getKey() === Auth::id() && $record->is_suspended) {
            // Evita auto-suspensão via edição direta
            $record->forceFill([
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
