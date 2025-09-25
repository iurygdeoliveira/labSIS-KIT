<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'team_id');
    }
}
