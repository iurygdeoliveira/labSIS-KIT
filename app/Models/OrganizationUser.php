<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationUser extends Pivot
{
    protected $table = 'organization_user';

    public $incrementing = false;
}
