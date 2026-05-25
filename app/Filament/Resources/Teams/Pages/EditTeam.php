<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Enums\AppTeamRole;
use App\Filament\Resources\Teams\TeamResource;
use App\Models\Membership;
use App\Models\Team;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

/**
 * @property Team|null $record
 * @property-read bool $canDelete
 * @property-read bool $canEditUsers
 * @property-read int $userCount
 * @property-read array $teamStats
 */
class EditTeam extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = TeamResource::class;

    public function canDelete(): bool
    {
        $record = $this->getRecord();

        return $record instanceof Team && $record->members()->count() === 0;
    }

    #[Computed]
    public function canEditUsers(): bool
    {
        return $this->record->is_active === true;
    }

    public function userCount(): int
    {
        $record = $this->getRecord();

        return $record instanceof Team ? $record->members()->count() : 0;
    }

    #[Computed]
    public function teamStats(): array
    {
        $record = $this->record;

        if (! $record) {
            return [
                'user_count' => 0,
                'is_active' => false,
                'can_delete' => false,
                'can_edit_users' => false,
            ];
        }

        return [
            'user_count' => $this->userCount,
            'is_active' => $record->is_active,
            'can_delete' => $this->canDelete,
            'can_edit_users' => $this->canEditUsers,
        ];
    }

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof Team) {
            return $record;
        }

        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $record->update($data);

        $current = $record->members()->pluck('users.id')->all();
        $toRemove = array_diff($current, $users);
        $toAdd = array_diff($users, $current);

        foreach ($toRemove as $userId) {
            Membership::query()
                ->where('team_id', $record->id)
                ->where('user_id', $userId)
                ->delete();
        }

        foreach ($toAdd as $userId) {
            Membership::create([
                'team_id' => $record->id,
                'user_id' => $userId,
                'role' => AppTeamRole::MEMBER->value,
            ]);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getSaveFormAction()->formId('form'),
            $this->getCancelFormAction(),
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
        $this->notifySuccess('Team atualizado com sucesso.');
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
