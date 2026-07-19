<?php

use App\Enums\OrganizationLandlordRole;
use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;

return [
    'key_type' => 'id',

    'models' => [
        'user' => User::class,
        'organization' => Organization::class,
    ],

    'role_enum' => OrganizationRole::class,
    'landlord_role_enum' => OrganizationLandlordRole::class,

    'panel_id' => 'user',

    'default_role' => 'user',
    'invite_expires_days' => 7,
    'max_invites_per_batch' => 5,
    'resend_cooldown_minutes' => 5,
    'tenant_slug_attribute' => 'slug',

    'routes' => [
        'prefix' => 'invite',
        'middleware' => ['web', 'throttle:10,1'],
    ],
];
