<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleType;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Traits\Filament\AppAuthenticationRecoveryCodes;
use App\Traits\Filament\AppAuthenticationSecret;
use App\Traits\HasOrganizations;
use App\Traits\UuidTrait;
use Carbon\CarbonImmutable;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_suspended
 * @property CarbonImmutable|null $suspended_at
 * @property bool $is_approved
 * @property int|null $approved_by
 * @property string|null $suspension_reason
 * @property string|null $app_authentication_secret
 * @property array<array-key, mixed>|null $app_authentication_recovery_codes
 * @property string|null $theme_color
 * @property string|null $locale
 * @property string|null $custom_fields
 * @property string|null $remember_token
 * @property CarbonImmutable|null $email_verified_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property CarbonImmutable|null $last_login_at
 * @property string|null $last_login_ip
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppAuthenticationRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppAuthenticationSecret($value)
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
 * @method bool isOwnerOfOrganization(Team $team)
 * @method bool isUserOfTeam(Team $team)
 * @method \Illuminate\Support\Collection getRolesForTeam(Team $team)
 * @method bool hasAnyRoleInTeam(Team $team)
 * @method bool hasOwnerRoleInAnyTeam()
 * @method void assignRoleInTeam(Role $role, Team $team)
 * @method void removeRoleFromTeam(string $roleName, Team $team)
 * @method void removeAllUserRolesFromTeam(Team $team)
 * @method void removeAllOwnerRolesFromTeam(Team $team)
 * @method \Illuminate\Database\Eloquent\Relations\MorphToMany<\Spatie\Permission\Models\Role, \App\Models\User> rolesWithTeams()
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'uuid',
    'name',
    'email',
    'password',
    'email_verified_at',
    'is_suspended',
    'suspended_at',
    'suspension_reason',
    'is_approved',
    'approved_by',
    'remember_token',
])]
#[Hidden([
    'password',
    'app_authentication_secret',
    'app_authentication_recovery_codes',
    'remember_token',
])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasMedia, HasTenants, MustVerifyEmail
{
    use AppAuthenticationRecoveryCodes;
    use AppAuthenticationSecret;
    use HasFactory;
    use HasOrganizations;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;
    use UuidTrait;

    private ?Collection $cachedTenants = null;

    /**
     * @return BelongsToMany<Organization, $this>
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->using(OrganizationUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // ==========================================
    // Setup & Configuration
    // ==========================================

    #[\Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'is_approved' => 'boolean',
            'created_at' => 'datetime:d/m/Y H:i',
            'updated_at' => 'datetime:d/m/Y H:i',
            'email_verified_at' => 'datetime:d/m/Y H:i',
            'suspended_at' => 'datetime:d/m/Y H:i',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('s3')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void {}

    // ==========================================
    // Relationships
    // ==========================================

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return MorphToMany<Role, $this, MorphPivot>
     */
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

    public function authentications()
    {
        return $this->morphMany(AuthenticationLog::class, 'authenticatable')->latest('login_at');
    }

    public function latestAuthentication()
    {
        return $this->morphOne(AuthenticationLog::class, 'authenticatable')->latestOfMany('login_at');
    }

    // ==========================================
    // Scopes
    // ==========================================

    #[Scope]
    protected function withRolesForTeam($query, Organization $organization): void
    {
        $query->with([
            'rolesWithTeams' => fn ($q) => $q->where('model_has_roles.team_id', $organization->id),
        ]);
    }

    // ==========================================
    // Filament / Access Control
    // ==========================================

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

            if ($this->hasOwnerRoleInAnyTeam()) {
                return true;
            }

            return $this->organizations()->exists();
        }

        return false;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Organization) {
            return false;
        }

        if (! $tenant->is_active) {
            return false;
        }

        return $this->organizations()->whereKey($tenant->getKey())->exists();
    }

    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->cachedTenants instanceof Collection) {
            return $this->cachedTenants;
        }

        return $this->cachedTenants = $this->organizations()->where('is_active', true)->get();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $media = $this->getFirstMedia('avatar');

        if ($media instanceof Media) {
            try {
                return $media->getUrl();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    // ==========================================
    // State Checks & Notifications
    // ==========================================

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    #[\Override]
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ==========================================
    // Team & Role Logic (Business Logic)
    // ==========================================

    public function isOwnerOfOrganization(Organization $organization): bool
    {
        return $this->getRoleQueryBuilder($organization)
            ->where('roles.name', RoleType::OWNER->value)
            ->exists();
    }

    public function isUserOfTeam(Organization $organization): bool
    {
        return $this->getRoleQueryBuilder($organization)
            ->where('roles.name', RoleType::USER->value)
            ->exists();
    }

    public function hasAnyRoleInTeam(Organization $organization): bool
    {
        return $this->getRoleQueryBuilder($organization)->exists();
    }

    public function hasOwnerRoleInAnyTeam(): bool
    {
        return $this->rolesWithTeams()
            ->where('roles.name', RoleType::OWNER->value)
            ->exists();
    }

    public function getRolesForTeam(Organization $organization): Collection
    {
        return $this->getRoleQueryBuilder($organization)
            ->select('roles.*')
            ->get();
    }

    public function assignRoleInTeam(Role $role, Organization $organization): void
    {
        $this->rolesWithTeams()->syncWithoutDetaching([
            $role->getKey() => ['team_id' => $organization->id],
        ]);
    }

    public function removeRoleFromTeam(string $roleName, Organization $organization): void
    {
        $role = Role::query()
            ->where('name', $roleName)
            ->where('team_id', $organization->id)
            ->first();

        if (! $role) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $organization->id)
            ->detach($role->getKey());
    }

    public function removeAllUserRolesFromTeam(Organization $organization): void
    {
        $roleIds = Role::query()
            ->where('name', RoleType::USER->value)
            ->where('team_id', $organization->id)
            ->pluck('id');

        if ($roleIds->isEmpty()) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $organization->id)
            ->detach($roleIds->toArray());
    }

    public function removeAllOwnerRolesFromTeam(Organization $organization): void
    {
        $roleIds = Role::query()
            ->where('name', RoleType::OWNER->value)
            ->where('team_id', $organization->id)
            ->pluck('id');

        if ($roleIds->isEmpty()) {
            return;
        }

        $this->rolesWithTeams()
            ->wherePivot('team_id', $organization->id)
            ->detach($roleIds->toArray());
    }

    private function getRoleQueryBuilder(Organization $organization): Builder
    {
        return Role::query()
            ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'roles.id')
            ->where('mhr.model_type', self::class)
            ->where('mhr.model_id', $this->id)
            ->where('mhr.team_id', $organization->id);
    }
}
