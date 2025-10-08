<?php

namespace App\Notifications;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sua conta foi aprovada! - '.config('app.name'))
            ->greeting('Olá '.$this->user->name.'!')
            ->line($this->user->approvedByUser->name.' aprovou sua conta no sistema.')
            ->line('Agora você pode acessar todas as funcionalidades da plataforma.')
            ->action('Acessar Sistema', Filament::getLoginUrl())
            ->line('Obrigado por usar nossa aplicação!')
            ->salutation('Atenciosamente, '.config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'approved_by' => $this->user->approvedByUser->name,
        ];
    }
}
