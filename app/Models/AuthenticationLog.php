<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property string $authenticatable_type
 * @property string|int $authenticatable_id
 * @property string $ip_address
 * @property string $user_agent
 * @property Carbon|null $login_at
 * @property Carbon|null $logout_at
 * @property bool $login_successful
 * @property bool $cleared_by_user
 * @property array|null $location
 */
#[Fillable([
    'authenticatable_type',
    'authenticatable_id',
    'ip_address',
    'user_agent',
    'login_at',
    'logout_at',
    'login_successful',
    'cleared_by_user',
    'location',
])]
class AuthenticationLog extends Model
{
    protected $table = 'authentication_log';

    public $timestamps = false;

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'login_successful' => 'boolean',
        'cleared_by_user' => 'boolean',
        'location' => 'array',
    ];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }
}
