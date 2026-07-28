<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationRole;
use App\Models\Changelog;
use App\Models\User;

class ChangelogPolicy
{
    /**
     * Executado antes de qualquer verificação de autorização.
     * Administradores possuem acesso total global ao gerenciamento do changelog.
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole(OrganizationRole::Admin->value)) {
            return true;
        }

        return null;
    }

    /**
     * Determina se o usuário pode visualizar a listagem do recurso de changelog.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determina se o usuário pode visualizar os detalhes de um registro do changelog.
     */
    public function view(User $user, Changelog $changelog): bool
    {
        return false;
    }

    /**
     * Determina se o usuário pode criar um novo registro no changelog.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determina se o usuário pode atualizar um registro do changelog.
     */
    public function update(User $user, Changelog $changelog): bool
    {
        return false;
    }

    /**
     * Determina se o usuário pode excluir um registro do changelog.
     */
    public function delete(User $user, Changelog $changelog): bool
    {
        return false;
    }
}
