<?php

declare(strict_types=1);

namespace App\Enums;

use LaravelDaily\FilaTeams\Contracts\TeamPermissionContract;

/**
 * Permissões granulares dentro de um Team.
 *
 * O Spatie continua sendo a fonte da verdade de autorização global.
 * Estas permissões refinam ações de UI dentro do contexto de team (settings).
 */
enum AppTeamPermission: string implements TeamPermissionContract
{
    case UpdateTeam = 'team:update';
    case DeleteTeam = 'team:delete';

    case AddMember = 'member:add';
    case UpdateMember = 'member:update';
    case RemoveMember = 'member:remove';

    case CreateInvitation = 'invitation:create';
    case CancelInvitation = 'invitation:cancel';
}
