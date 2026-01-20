<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use MongoDB\Laravel\Eloquent\Model;

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
