<?php

namespace App\Listeners;

use App\Events\OrganizationInviteCreated;
use App\Mail\OrganizationInviteMail;
use Illuminate\Support\Facades\Mail;

class SendOrganizationInviteMail
{
    public function handle(OrganizationInviteCreated $event): void
    {
        $event->invite->loadMissing(['organization', 'user']);

        Mail::to($event->invite->email)->send(new OrganizationInviteMail($event->invite));
    }
}
