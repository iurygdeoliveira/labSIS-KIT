<?php

declare(strict_types=1);

namespace App\Models;

use App\Trait\Filament\AppAuthenticationRecoveryCodes;
use App\Trait\Filament\AppAuthenticationSecret;
use App\Trait\UuidTrait;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    use AppAuthenticationRecoveryCodes;
    use AppAuthenticationSecret;
    use HasFactory;
    use Notifiable;
    use UuidTrait;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'email_verified_at',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'created_at' => 'datetime:d/m/Y H:i',
            'updated_at' => 'datetime:d/m/Y H:i',
            'email_verified_at' => 'datetime:d/m/Y H:i',
            'suspended_at' => 'datetime:d/m/Y H:i',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'uuid';  // Substitua por 'uuid' ou o nome do campo que contém seu UUID
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($this->isSuspended()) {
            return false;
        }

        return true;
    }
}
