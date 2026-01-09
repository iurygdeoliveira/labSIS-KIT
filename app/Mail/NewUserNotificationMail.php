<?php

declare(strict_types=1);

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
            subject: 'Novo Usuário Registrado',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-user-notification',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
