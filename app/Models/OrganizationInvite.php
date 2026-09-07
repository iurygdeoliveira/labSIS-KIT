<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use BackedEnum;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property OrganizationRole $role
 * @property-read Organization $organization
 */
#[Fillable(['token', 'organization_id', 'user_id', 'email', 'role', 'expires_at', 'accepted_at'])]
class OrganizationInvite extends Model
{
    protected function casts(): array
    {
        return [
            'role' => OrganizationRole::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')->where('expires_at', '>', now());
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function byToken(Builder $query, string $token): Builder
    {
        return $query->where('token', $token);
    }

    public function accept(Authenticatable $user): void
    {
        $this->update(['accepted_at' => now()]);

        $this->organization->users()->syncWithoutDetaching([
            $user->getAuthIdentifier() => [
                'role' => $this->role->value,
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

        /** @var static $invite */
        $invite = static::query()->create([
            'token' => (string) Str::uuid(),
            'organization_id' => $organization->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'email' => $user instanceof Model ? $user->getAttribute('email') : null,
            'role' => $role instanceof BackedEnum ? $role->value : $role,
            'expires_at' => now()->add($expiresIn),
            'accepted_at' => null,
        ]);

        return $invite;
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
        $email = $user instanceof Model ? $user->getAttribute('email') : null;

        return strcasecmp((string) $email, $this->email) === 0;
    }

    public function isResendable(): bool
    {
        $cooldownMinutes = config('filament-tenant-members.resend_cooldown_minutes', 5);

        return $this->updated_at->addMinutes($cooldownMinutes)->isPast();
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
