<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\MediaItem;
use App\Models\User;

class MediaItemPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can(Permission::VIEW->for('media'));
    }

    public function view(User $user, MediaItem $record): bool
    {
        return $user->can(Permission::VIEW->for('media'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('media'));
    }

    public function update(User $user, MediaItem $record): bool
    {
        return $user->can(Permission::UPDATE->for('media'));
    }

    public function delete(User $user, MediaItem $record): bool
    {
        return $user->can(Permission::DELETE->for('media'));
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(Permission::DELETE->for('media'));
    }
}
