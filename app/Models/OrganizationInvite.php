<?php

namespace App\Models;

use BackedEnum;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['token', 'organization_id', 'user_id', 'email', 'role', 'expires_at', 'accepted_at'])]
class OrganizationInvite extends Model
{
    protected function casts(): array
    {
        return [
            'role' => config('filament-tenant-members.role_enum'),
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')->where('expires_at', '>', now());
    }

    public function scopeByToken(Builder $query, string $token): Builder
    {
        return $query->where('token', $token);
    }

    public function accept(Authenticatable $user): void
    {
        $this->update(['accepted_at' => now()]);

        $this->organization->users()->syncWithoutDetaching([
            $user->getAuthIdentifier() => [
                'role' => $this->role instanceof BackedEnum ? $this->role->value : $this->role,
            ],
        ]);
    }

    public static function mintForUser(
        Authenticatable $user,
        Model $organization,
        BackedEnum|string $role,
        ?\DateInterval $expiresIn = null,
    ): static {
        $expiresIn ??= CarbonInterval::days(config('filament-tenant-members.invite_expires_days', 7));

        return static::create([
            'token' => (string) Str::uuid(),
            'organization_id' => $organization->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'email' => $user->getAttribute('email'),
            'role' => $role instanceof BackedEnum ? $role->value : $role,
            'expires_at' => now()->add($expiresIn),
            'accepted_at' => null,
        ]);
    }

    public function completePasswordSet(string $plainPassword): Authenticatable
    {
        $userModel = config('filament-tenant-members.models.user', User::class);

        /** @var Authenticatable&Model $user */
        $user = $userModel::query()->where('email', $this->email)->firstOrFail();

        $user->setAttribute('password', $plainPassword);
        $user->save();

        $this->accept($user);

        return $user;
    }

    public function matchesUser(Authenticatable $user): bool
    {
        return strcasecmp((string) $user->email, $this->email) === 0;
    }

    public function isResendable(): bool
    {
        $cooldownMinutes = config('filament-tenant-members.resend_cooldown_minutes', 5);

        return $this->updated_at->addMinutes($cooldownMinutes)->isPast();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(config('filament-tenant-members.models.organization', Organization::class));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('filament-tenant-members.models.user', User::class));
    }
}
