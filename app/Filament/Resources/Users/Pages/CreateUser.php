<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\RoleType;
use App\Filament\Resources\Users\UserResource;
use App\Models\Tenant;
use App\Models\User;
use App\Trait\Filament\NotificationsTrait;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use NotificationsTrait;

    protected static string $resource = UserResource::class;

    // Removido onboarding; seleção de tenant acontece no form (UserForm)

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $currentUser = Filament::auth()->user();

        $isAdmin = $currentUser instanceof User && method_exists($currentUser, 'hasRole')
            ? (bool) $currentUser->hasRole(RoleType::ADMIN->value)
            : false;

        if ($isAdmin) {
            $tenantId = (int) ($this->data['tenant_id'] ?? 0);
            if ($tenantId > 0) {
                $this->record->tenants()->syncWithoutDetaching([$tenantId]);
            }
        } else {
            $currentTenant = Filament::getTenant();
            if ($currentTenant instanceof Tenant) {
                $this->record->tenants()->syncWithoutDetaching([$currentTenant->id]);
            }
        }

        $this->notifySuccess('Usuário criado com sucesso.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    // Sem onboarding; regra aplicada no afterCreate
}
