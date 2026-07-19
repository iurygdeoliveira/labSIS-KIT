<?php

namespace App\Events;

use App\Models\OrganizationInvite;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganizationInviteCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public OrganizationInvite $invite,
    ) {}
}
