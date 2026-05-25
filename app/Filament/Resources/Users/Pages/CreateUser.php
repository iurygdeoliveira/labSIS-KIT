<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\AppTeamRole;
use App\Enums\RoleType;
use App\Filament\Resources\Users\UserResource;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Traits\Filament\HasStandardCreateFooterActions;
use App\Traits\Filament\HasStandardCreateHeaderActions;
use App\Traits\Filament\NotificationsTrait;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use HasStandardCreateFooterActions;
    use HasStandardCreateHeaderActions;
    use NotificationsTrait;

    protected static string $resource = UserResource::class;

    // Removido onboarding; seleção de tenant acontece no form (UserForm)

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        if (! $record instanceof User) {
            return;
        }

        $currentUser = Filament::auth()->user();

        $isAdmin = $currentUser instanceof User && $currentUser->hasRole(RoleType::ADMIN->value);

        if ($isAdmin) {
            $teamId = (int) ($this->data['tenant_id'] ?? 0);
            if ($teamId > 0 && ! Membership::query()->where('team_id', $teamId)->where('user_id', $record->id)->exists()) {
                Membership::create([
                    'team_id' => $teamId,
                    'user_id' => $record->id,
                    'role' => AppTeamRole::MEMBER->value,
                ]);
            }
        } else {
            $currentTeam = Filament::getTenant();
            if ($currentTeam instanceof Team
                && ! Membership::query()->where('team_id', $currentTeam->id)->where('user_id', $record->id)->exists()) {
                Membership::create([
                    'team_id' => $currentTeam->id,
                    'user_id' => $record->id,
                    'role' => AppTeamRole::MEMBER->value,
                ]);
            }
        }

        // Usuário criado pelo admin - não precisa notificar

        $this->notifySuccess('Usuário criado com sucesso.');
    }

    #[\Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();

        $currentUser = Filament::auth()->user();
        if ($currentUser instanceof User && $currentUser->hasRole(RoleType::ADMIN->value)) {
            $data['is_approved'] = true;
            $data['approved_by'] = $currentUser->id;
        }

        return $data;
    }

    #[\Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    // Sem onboarding; regra aplicada no afterCreate
}
