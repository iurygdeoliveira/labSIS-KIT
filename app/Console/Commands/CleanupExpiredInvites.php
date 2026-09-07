<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OrganizationInvite;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Delete expired and unaccepted organization invitations')]
#[Signature('organization:cleanup-invites')]
class CleanupExpiredInvites extends Command
{
    public function handle(): int
    {
        $count = OrganizationInvite::query()
            ->where('expires_at', '<', now())
            ->whereNull('accepted_at')
            ->delete();

        $this->info("Deleted {$count} expired invitation(s).");

        return self::SUCCESS;
    }
}
