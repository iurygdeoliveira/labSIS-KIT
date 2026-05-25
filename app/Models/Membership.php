<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppTeamRole;
use Carbon\CarbonImmutable;
use LaravelDaily\FilaTeams\Models\Membership as BaseMembership;

/**
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property AppTeamRole $role
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Team $team
 * @property-read User $user
 */
class Membership extends BaseMembership {}
