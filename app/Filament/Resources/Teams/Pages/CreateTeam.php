<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Enums\AppTeamRole;
use App\Filament\Resources\Teams\TeamResource;
use App\Models\Membership;
use App\Models\Team;
use App\Traits\Filament\HasSaveAndCreateAnotherAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTeam extends CreateRecord
{
    use HasSaveAndCreateAnotherAction;
    use NotificationsTrait;

    protected static string $resource = TeamResource::class;

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $data['is_active'] = true;
        $data['is_personal'] = false;

        $team = static::getModel()::create($data);

        if (! $team instanceof Team) {
            return $team;
        }

        foreach ($users as $userId) {
            Membership::create([
                'team_id' => $team->id,
                'user_id' => $userId,
                'role' => AppTeamRole::MEMBER->value,
            ]);
        }

        return $team;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Team criado com sucesso.');
    }
}
