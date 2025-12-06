<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\MediaItem;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;

class MediaItemPolicy
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
     * Determina se o usuário pode visualizar a lista de mídias.
     * Chamado ao acessar a página de listagem (ex: /media).
     * Verifica a permissão 'media.view' no tenant atual.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::VIEW->for('media'));
    }

    /**
     * Determina se o usuário pode visualizar os detalhes de uma mídia específica.
     * Chamado ao acessar a página de visualização (ex: /media/{id}).
     * Verifica a permissão 'media.view' no tenant atual.
     */
    public function view(User $user, MediaItem $record): bool
    {
        return $user->can(Permission::VIEW->for('media'));
    }

    /**
     * Determina se o usuário pode criar novas mídias.
     * Chamado ao acessar a página de criação ou ao tentar fazer upload.
     * Verifica a permissão 'media.create' no tenant atual.
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('media'));
    }

    /**
     * Determina se o usuário pode editar uma mídia específica.
     * Chamado ao acessar a página de edição ou ao tentar atualizar metadados.
     * Verifica a permissão 'media.update' no tenant atual.
     */
    public function update(User $user, MediaItem $record): bool
    {
        return $user->can(Permission::UPDATE->for('media'));
    }

    /**
     * Determina se o usuário pode excluir uma mídia específica.
     * Chamado ao tentar deletar um único arquivo/vídeo.
     * Verifica a permissão 'media.delete' no tenant atual.
     */
    public function delete(User $user, MediaItem $record): bool
    {
        return $user->can(Permission::DELETE->for('media'));
    }

    /**
     * Determina se o usuário pode excluir múltiplas mídias em massa.
     * Chamado ao tentar deletar vários arquivos de uma vez na tabela.
     * Verifica a permissão 'media.delete' no tenant atual.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can(Permission::DELETE->for('media'));
    }
}
