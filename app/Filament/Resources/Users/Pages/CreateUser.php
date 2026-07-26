<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\OrganizationRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\Organization;
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

        $isAdmin = $currentUser instanceof User && $currentUser->hasRole(OrganizationRole::Admin->value);

        if ($isAdmin) {
            $teamId = (int) ($this->data['tenant_id'] ?? 0);
            $role = (string) ($this->data['organization_role'] ?? OrganizationRole::User->value);
            if ($teamId > 0) {
                $organization = Organization::find($teamId);
                if ($organization instanceof Organization) {
                    $record->organizations()->syncWithoutDetaching([
                        $organization->id => ['role' => $role],
                    ]);

                    $spatieRole = match ($role) {
                        OrganizationRole::Owner->value, OrganizationRole::Admin->value => OrganizationRole::ensureOwnerRoleForTeam($organization->id, config('auth.defaults.guard', 'web')),
                        default => OrganizationRole::ensureUserRoleForTeam($organization->id, config('auth.defaults.guard', 'web')),
                    };
                    $record->assignRoleInTeam($spatieRole, $organization);
                }
            }
        } else {
            $currentTeam = Filament::getTenant();
            if ($currentTeam instanceof Organization) {
                $record->organizations()->syncWithoutDetaching([
                    $currentTeam->id => ['role' => OrganizationRole::User->value],
                ]);

                $spatieRole = OrganizationRole::ensureUserRoleForTeam($currentTeam->id, config('auth.defaults.guard', 'web'));
                $record->assignRoleInTeam($spatieRole, $currentTeam);
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
        if ($currentUser instanceof User && $currentUser->hasRole(OrganizationRole::Admin->value)) {
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
