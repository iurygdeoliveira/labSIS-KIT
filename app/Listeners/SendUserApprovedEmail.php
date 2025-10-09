<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserApproved;
use App\Mail\UserApprovedMail;
use Illuminate\Support\Facades\Mail;

class SendUserApprovedEmail
{
    public function handle(UserApproved $event): void
    {
        Mail::to($event->user->email)->send(new UserApprovedMail($event->user));
    }
}
