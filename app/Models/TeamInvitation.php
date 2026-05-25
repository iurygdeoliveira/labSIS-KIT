<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppTeamRole;
use Carbon\CarbonImmutable;
use LaravelDaily\FilaTeams\Models\TeamInvitation as BaseTeamInvitation;

/**
 * @property int $id
 * @property string $code
 * @property int $team_id
 * @property string $email
 * @property AppTeamRole $role
 * @property int $invited_by
 * @property CarbonImmutable|null $expires_at
 * @property CarbonImmutable|null $accepted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Team $team
 * @property-read User $inviter
 */
class TeamInvitation extends BaseTeamInvitation {}
