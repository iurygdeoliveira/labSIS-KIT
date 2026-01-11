<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class AuthenticationLogPolicy
{
    /**
     * Executado antes de qualquer verificação de autorização.
     * Permite atalhos hierárquicos sem consultar permissões específicas:
     * - Admin: acesso total global (todos os tenants)
     * - Owner: acesso total dentro do tenant atual
     * Retorna null para delegar a verificação aos métodos específicos.
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        $currentTenant = Filament::getTenant();
        if ($currentTenant instanceof Tenant && $user->isOwnerOfTenant($currentTenant)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'authentication-log.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuthenticationLog $authenticationLog): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AuthenticationLog $authenticationLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AuthenticationLog $authenticationLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AuthenticationLog $authenticationLog): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AuthenticationLog $authenticationLog): bool
    {
        return false;
    }

    protected function hasPermissionTo(User $user, string $permission): bool
    {
        // Se estiver num contexto de tenant, checa a permissão NAQUELE tenant
        if ($tenant = Filament::getTenant()) {
            // Nota: O pacote Spatie Permission usa setPermissionsTeamId globalmente às vezes.
            // Mas $user->hasPermissionTo() deve respeitar o contexto se configurado corretamente.
            // Filament geralmente gerencia o team permission.
            return $user->hasPermissionTo($permission);
        }

        return $user->hasPermissionTo($permission);
    }
}
