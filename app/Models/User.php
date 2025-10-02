<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleType;
use App\Services\AvatarService;
use App\Traits\Filament\AppAuthenticationRecoveryCodes;
use App\Traits\Filament\AppAuthenticationSecret;
use App\Traits\UuidTrait;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_suspended
 * @property \Carbon\CarbonImmutable|null $suspended_at
 * @property string|null $suspension_reason
 * @property string|null $app_authentication_secret
 * @property array<array-key, mixed>|null $app_authentication_recovery_codes
 * @property string|null $avatar_url
 * @property string|null $theme_color
 * @property string|null $locale
 * @property string|null $custom_fields
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppAuthenticationRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppAuthenticationSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSuspended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspendedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSuspensionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereThemeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasAvatar, HasMedia, HasTenants
{
    use AppAuthenticationRecoveryCodes;
    use AppAuthenticationSecret;
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;
    use UuidTrait;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'avatar_url',
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

    public function getFilamentAvatarUrl(): ?string
    {
        return app(AvatarService::class)->getAvatarUrl($this);
    }

    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'auth') {
            return true;
        }

        if ($this->isSuspended()) {
            return false;
        }

        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->hasRole(RoleType::ADMIN->value);
        }

        if ($panel->getId() === 'user') {
            if ($this->hasRole(RoleType::USER->value)) {
                return true;
            }

            if ($this->hasOwnerRoleInAnyTenant()) {
                return true;
            }

            return $this->tenants()->exists();
        }

        return false;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('s3')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void {}

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->withTimestamps();
    }

    public function rolesWithTeams(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            config('permission.table_names.model_has_roles', 'model_has_roles'),
            'model_id',
            'role_id'
        )->withPivot('team_id');
    }

    public function getTenants(Panel $panel): array|\Illuminate\Support\Collection
    {
        return $this->tenants()->where('is_active', true)->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Tenant) {
            return false;
        }

        if (! $tenant->is_active) {
            return false;
        }

        return $this->tenants()->whereKey($tenant->getKey())->exists();
    }

    public function isOwnerOfTenant(Tenant $tenant): bool
    {
        return $this->getRoleQueryBuilder($tenant)
            ->where('roles.name', RoleType::OWNER->value)
            ->exists();
    }

    public function isUserOfTenant(Tenant $tenant): bool
    {
        return $this->getRoleQueryBuilder($tenant)
            ->where('roles.name', RoleType::USER->value)
            ->exists();
    }

    public function getRolesForTenant(Tenant $tenant): Collection
    {
        return $this->getRoleQueryBuilder($tenant)
            ->select('roles.*')
            ->get();
    }

    public function hasAnyRoleInTenant(Tenant $tenant): bool
    {
        return $this->getRoleQueryBuilder($tenant)->exists();
    }

    public function hasOwnerRoleInAnyTenant(): bool
    {
        return $this->rolesWithTeams()
            ->where('roles.name', RoleType::OWNER->value)
            ->exists();
    }

    private function getRoleQueryBuilder(Tenant $tenant): Builder
    {
        return Role::query()
            ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'roles.id')
            ->where('mhr.model_type', self::class)
            ->where('mhr.model_id', $this->id)
            ->where('mhr.team_id', $tenant->id);
    }

    public function scopeWithRolesForTenant($query, Tenant $tenant): void
    {
        $query->with([
            'rolesWithTeams' => fn ($q) => $q->where('team_id', $tenant->id),
        ]);
    }

    public function assignRoleInTenant(Role $role, Tenant $tenant): void
    {
        $this->rolesWithTeams()->syncWithoutDetaching([
            $role->getKey() => ['team_id' => $tenant->id],
        ]);
    }

    public function removeRoleFromTenant(string $roleName, Tenant $tenant): void
    {
        $role = Role::query()
            ->where('name', $roleName)
            ->where('team_id', $tenant->id)
            ->first();

        if (! $role) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $tenant->id)
            ->detach($role->getKey());
    }

    public function removeAllUserRolesFromTenant(Tenant $tenant): void
    {
        $roleIds = Role::query()
            ->where('name', RoleType::USER->value)
            ->where('team_id', $tenant->id)
            ->pluck('id');

        if ($roleIds->isEmpty()) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $tenant->id)
            ->detach($roleIds->toArray());
    }

    public function removeAllOwnerRolesFromTenant(Tenant $tenant): void
    {
        $roleIds = Role::query()
            ->where('name', RoleType::OWNER->value)
            ->where('team_id', $tenant->id)
            ->pluck('id');

        if ($roleIds->isEmpty()) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $tenant->id)
            ->detach($roleIds->toArray());
    }

    public function setAvatarUrlAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['avatar_url'] = null;

            return;
        }

        $avatarService = app(\App\Services\AvatarService::class);

        if ($avatarService->processAndSaveAvatar($this, $value)) {
            $this->attributes['avatar_url'] = null;
        } else {
            $this->attributes['avatar_url'] = $value;
        }
    }
}
