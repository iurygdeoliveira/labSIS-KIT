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

    #[\Override]
    public function getHeading(): string|Htmlable
    {
        return 'Esqueceu sua senha?';
    }

    #[\Override]
    public function getSubheading(): string|Htmlable|null
    {
        return 'Digite seu email e enviaremos um link para redefinir sua senha.';
    }

    #[\Override]
    protected function getSentNotification(string $status): ?Notification
    {
        return $this->buildNotification(
            type: 'primary',
            title: 'Email enviado',
            body: 'Verifique sua caixa de entrada para redefinir sua senha.',
            seconds: 8
        );
    }

    #[\Override]
    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return;
        }

        $data = $this->form->getState();

        $status = \Illuminate\Support\Facades\Password::broker(\Filament\Facades\Filament::getAuthPasswordBroker())->sendResetLink(
            $this->getCredentialsFromFormData($data),
            function (\Illuminate\Contracts\Auth\CanResetPassword $user, string $token): void {
                if (
                    ($user instanceof \Filament\Models\Contracts\FilamentUser) &&
                    (! $user->canAccessPanel(\Filament\Facades\Filament::getCurrentOrDefaultPanel()))
                ) {
                    return;
                }

                if (! $user instanceof \App\Models\User) {
                    $userClass = $user::class;
                    throw new \LogicException("User [{$userClass}] is not an instance of App\Models\User.");
                }

                $notification = new \App\Notifications\Auth\ResetPasswordNotification($token);
                $notification->url = \Filament\Facades\Filament::getResetPasswordUrl($token, $user);

                $user->notify($notification);

                if (class_exists(\Illuminate\Auth\Events\PasswordResetLinkSent::class)) {
                    event(new \Illuminate\Auth\Events\PasswordResetLinkSent($user));
                }
            },
        );

        if ($status !== \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            $this->getFailureNotification($status)?->send();

            return;
        }

        $this->getSentNotification($status)?->send();

        $this->form->fill();
    }
}
