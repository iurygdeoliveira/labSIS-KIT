<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $user_id
 * @property int $tenant_id
 * @property-read User $user
 * @property-read Tenant $tenant
 */
class TenantUser extends Pivot
{
    protected $table = 'tenant_user';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Accessor para o atributo virtual role_virtual
     * Retorna a role atual baseada nas permissões do Spatie
     */
    protected function getRoleVirtualAttribute(): string
    {
        if ($this->user->isOwnerOfTenant($this->tenant)) {
            return 'owner';
        }

        if ($this->user->isUserOfTenant($this->tenant)) {
            return 'user';
        }

        return 'none';
    }

    /**
     * Mutator para o atributo virtual role_virtual
     * Este mutator não salva nada - apenas dispara a atualização via página
     */
    protected function setRoleVirtualAttribute(string $value): void
    {
        // O save será interceptado e redirecionado para o método assignRole
        // através do beforeStateUpdated no SelectColumn
        // Não fazemos nada aqui para evitar tentativa de salvar no banco
    }
}
