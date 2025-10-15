<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Traits\Filament\NotificationsTrait;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    use NotificationsTrait;

    public function getHeading(): string|Htmlable
    {
        return 'Esqueceu sua senha?';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Digite seu email e enviaremos um link para redefinir sua senha.';
    }

    protected function getSentNotification(string $status): ?Notification
    {
        return $this->buildNotification(
            type: 'primary',
            title: 'Email enviado',
            body: 'Verifique sua caixa de entrada para redefinir sua senha.',
            seconds: 8
        );
    }
}
