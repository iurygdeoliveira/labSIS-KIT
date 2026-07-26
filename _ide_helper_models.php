<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
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
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $authenticatable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereAuthenticatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereAuthenticatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereClearedByUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereLoginSuccessful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereLogoutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuthenticationLog whereUserAgent($value)
 */
	class AuthenticationLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property bool $video
 * @property string|null $mime_type
 * @property int|null $size
 * @property int|null $organization_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read string $file_type
 * @property-read string $human_size
 * @property-read string|null $image_url
 * @property-read string|null $collection_name
 * @property-read Video|null $videoRelation
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereVideo($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Organization|null $organization
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereUuid($value)
 */
	class MediaItem extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \App\Support\AppDateTime|null $created_at
 * @property \App\Support\AppDateTime|null $updated_at
 * @property bool $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrganizationInvite> $invites
 * @property-read int|null $invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MediaItem> $mediaItems
 * @property-read int|null $media_items_count
 * @property-read \App\Models\OrganizationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 */
	class Organization extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $token
 * @property int $organization_id
 * @property int $user_id
 * @property string $email
 * @property \App\Enums\OrganizationRole $role
 * @property \App\Support\AppDateTime $expires_at
 * @property \App\Support\AppDateTime|null $accepted_at
 * @property \App\Support\AppDateTime|null $created_at
 * @property \App\Support\AppDateTime|null $updated_at
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite byToken(string $token)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationInvite whereUserId($value)
 */
	class OrganizationInvite extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $organization_id
 * @property int $user_id
 * @property string $role
 * @property \App\Support\AppDateTime|null $created_at
 * @property \App\Support\AppDateTime|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationUser whereUserId($value)
 */
	class OrganizationUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read MorphPivot $pivot
 * @property int $id
 * @property int|null $team_id
 * @property string $name
 * @property string $guard_name
 * @property \App\Support\AppDateTime|null $created_at
 * @property \App\Support\AppDateTime|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Organization|null $team
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
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
 * @method bool isOwnerOfOrganization(Organization $team)
 * @method bool isUserOfTeam(Organization $team)
 * @method \Illuminate\Support\Collection getRolesForTeam(Organization $team)
 * @method bool hasAnyRoleInTeam(Organization $team)
 * @method bool hasOwnerRoleInAnyTeam()
 * @method void assignRoleInTeam(Role $role, Organization $team)
 * @method void removeRoleFromTeam(string $roleName, Organization $team)
 * @method void removeAllUserRolesFromTeam(Organization $team)
 * @method void removeAllOwnerRolesFromTeam(Organization $team)
 * @method \Illuminate\Database\Eloquent\Relations\MorphToMany<\Spatie\Permission\Models\Role, \App\Models\User> rolesWithTeams()
 * @mixin \Eloquent
 * @property string $role
 * @property-read User|null $approvedByUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuthenticationLog> $authentications
 * @property-read int|null $authentications_count
 * @property-read \App\Models\AuthenticationLog|null $latestAuthentication
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\OrganizationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $rolesWithTeams
 * @property-read int|null $roles_with_teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withRolesForTeam(\App\Models\Organization $organization)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser, \Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication, \Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery, \Spatie\MediaLibrary\HasMedia, \Filament\Models\Contracts\HasTenants, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $media_item_id
 * @property string|null $provider
 * @property string|null $provider_video_id
 * @property string $url
 * @property string|null $title
 * @property int|null $duration_seconds
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read MediaItem $mediaItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereMediaItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereProviderVideoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUrl($value)
 * @mixin \Eloquent
 * @property string $uuid
 * @property int|null $organization_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUuid($value)
 */
	class Video extends \Eloquent {}
}

