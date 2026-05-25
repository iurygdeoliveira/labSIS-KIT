<?php

declare(strict_types=1);

use App\Enums\AppTeamPermission;
use App\Enums\AppTeamRole;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamInvitation;

return [
    'enums' => [
        'role' => AppTeamRole::class,
        'permission' => AppTeamPermission::class,
    ],
    'models' => [
        'team' => Team::class,
        'membership' => Membership::class,
        'invitation' => TeamInvitation::class,
    ],
    'invitation' => [
        'expires_after_days' => 7,
    ],
    /*
     * O fluxo de registro do labSIS-KIT (app/Filament/Pages/Auth/Register.php)
     * cria o team explicitamente com o nome informado pelo usuário. Por isso o
     * listener `CreatePersonalTeam` do pacote fica desativado para não
     * duplicar o team.
     */
    'create_personal_team_on_registration' => false,
];
