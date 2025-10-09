<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public User $newUser
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo usuário cadastrado - '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-user',
            with: [
                'admin' => $this->admin,
                'newUser' => $this->newUser,
                'userUrl' => $this->newUser->id ? route('filament.admin.resources.users.edit', $this->newUser) : '#',
            ]
        );
    }
}
