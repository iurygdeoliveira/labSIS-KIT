<?php

namespace App\Console\Commands;

use App\Models\OrganizationInvite;
use Illuminate\Console\Command;

class CleanupExpiredInvites extends Command
{
    protected $signature = 'organization:cleanup-invites';

    protected $description = 'Delete expired and unaccepted organization invitations';

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
