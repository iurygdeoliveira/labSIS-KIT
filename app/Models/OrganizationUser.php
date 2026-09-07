<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Table(name: 'organization_user')]
#[WithoutIncrementing]
class OrganizationUser extends Pivot {}
