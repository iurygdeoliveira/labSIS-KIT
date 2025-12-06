<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;

class UserPolicy
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
     * Determina se o usuário pode visualizar a lista de usuários.
     * Chamado ao acessar a página de listagem (ex: /users).
     * Verifica a permissão 'users.view' no tenant atual.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Permission::VIEW);
    }

    /**
     * Determina se o usuário pode visualizar os detalhes de um usuário específico.
     * Chamado ao acessar a página de visualização (ex: /users/{uuid}).
     * Verifica a permissão 'users.view' no tenant atual.
     */
    public function view(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::VIEW);
    }

    /**
     * Determina se o usuário pode criar novos usuários.
     * Chamado ao acessar a página de criação ou ao tentar salvar um novo registro.
     * Verifica a permissão 'users.create' no tenant atual.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, Permission::CREATE);
    }

    /**
     * Determina se o usuário pode editar um usuário específico.
     * Chamado ao acessar a página de edição ou ao tentar atualizar um registro.
     * Verifica a permissão 'users.update' no tenant atual.
     */
    public function update(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::UPDATE);
    }

    /**
     * Determina se o usuário pode excluir um usuário específico.
     * Chamado ao tentar deletar um único registro.
     * Verifica a permissão 'users.delete' no tenant atual.
     */
    public function delete(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::DELETE);
    }

    /**
     * Determina se o usuário pode excluir múltiplos usuários em massa.
     * Chamado ao tentar deletar vários registros de uma vez na tabela.
     * Verifica a permissão 'users.delete' no tenant atual.
     */
    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, Permission::DELETE);
    }

    /**
     * Método auxiliar centralizado para verificação de permissões.
     * Consulta o banco de dados através do Spatie Permission para verificar
     * se o usuário possui a permissão específica no contexto do tenant atual.
     */
    private function hasPermission(User $user, Permission $permission): bool
    {
        return $user->can($permission->for('users'));
    }
}
