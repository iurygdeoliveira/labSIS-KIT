<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\Users\UserResource;
use App\Models\Tenant;
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
        if (! $record instanceof \App\Models\User) {
            return;
        }

        $currentUser = Filament::auth()->user();

        $isAdmin = $currentUser instanceof User && $currentUser->hasRole(RoleType::ADMIN->value);

        if ($isAdmin) {
            $tenantId = (int) ($this->data['tenant_id'] ?? 0);
            if ($tenantId > 0) {
                $record->tenants()->syncWithoutDetaching([$tenantId]);
            }
        } else {
            $currentTenant = Filament::getTenant();
            if ($currentTenant instanceof Tenant) {
                $record->tenants()->syncWithoutDetaching([$currentTenant->id]);
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
