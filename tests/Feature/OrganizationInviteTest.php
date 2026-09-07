<?php

declare(strict_types=1);

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Support\Str;

it('finds a pending invite by token and ignores expired or accepted ones', function (): void {
    $organization = Organization::query()->create([
        'name' => 'LabSIS',
        'slug' => 'labsis-invite-scopes',
    ]);
    $inviter = User::factory()->create();

    $pending = OrganizationInvite::query()->create([
        'token' => (string) Str::uuid(),
        'organization_id' => $organization->id,
        'user_id' => $inviter->id,
        'email' => 'pending@example.com',
        'role' => OrganizationRole::User,
        'expires_at' => now()->addDay(),
        'accepted_at' => null,
    ]);
    $expired = OrganizationInvite::query()->create([
        'token' => (string) Str::uuid(),
        'organization_id' => $organization->id,
        'user_id' => $inviter->id,
        'email' => 'expired@example.com',
        'role' => OrganizationRole::User,
        'expires_at' => now()->subMinute(),
        'accepted_at' => null,
    ]);
    $accepted = OrganizationInvite::query()->create([
        'token' => (string) Str::uuid(),
        'organization_id' => $organization->id,
        'user_id' => $inviter->id,
        'email' => 'accepted@example.com',
        'role' => OrganizationRole::User,
        'expires_at' => now()->addDay(),
        'accepted_at' => now(),
    ]);

    expect(OrganizationInvite::query()->pending()->pluck('id')->all())
        ->toBe([$pending->id])
        ->and(OrganizationInvite::query()->byToken($pending->token)->pending()->first()?->is($pending))
        ->toBeTrue()
        ->and(OrganizationInvite::query()->byToken($expired->token)->pending()->first())
        ->toBeNull()
        ->and(OrganizationInvite::query()->byToken($accepted->token)->pending()->first())
        ->toBeNull()
        ->and(OrganizationInvite::query()->byToken((string) Str::uuid())->first())
        ->toBeNull();
});

it('accepts an invite for a matching email regardless of case and stores the enum role on the pivot', function (): void {
    $organization = Organization::query()->create([
        'name' => 'LabSIS',
        'slug' => 'labsis-invite-accept',
    ]);
    $inviter = User::factory()->create();
    $invitee = User::factory()->create([
        'email' => 'Invitee@Example.com',
    ]);

    $invite = OrganizationInvite::query()->create([
        'token' => (string) Str::uuid(),
        'organization_id' => $organization->id,
        'user_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role' => OrganizationRole::Owner,
        'expires_at' => now()->addDay(),
        'accepted_at' => null,
    ]);

    expect($invite->matchesUser($invitee))->toBeTrue()
        ->and($invite->matchesUser($inviter))->toBeFalse();

    $invite->accept($invitee);
    $invite->refresh();

    expect($invite->accepted_at)->not->toBeNull();

    $this->assertDatabaseHas('organization_user', [
        'organization_id' => $organization->id,
        'user_id' => $invitee->id,
        'role' => OrganizationRole::Owner->value,
    ]);
});
