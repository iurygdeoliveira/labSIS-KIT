<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @property string $authenticatable_type
 * @property string|int $authenticatable_id
 * @property string $ip_address
 * @property string $user_agent
 * @property \Illuminate\Support\Carbon|null $login_at
 * @property \Illuminate\Support\Carbon|null $logout_at
 * @property bool $login_successful
 * @property bool $cleared_by_user
 * @property array|null $location
 */
class AuthenticationLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'authentication_logs';

    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'login_successful',
        'cleared_by_user',
        'location',
    ];

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
