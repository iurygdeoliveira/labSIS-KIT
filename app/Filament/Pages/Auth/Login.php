<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Trait\Filament\NotificationsTrait;
use Filament\Auth\Pages\Login as AuthLogin;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;

class Login extends AuthLogin
{
    use NotificationsTrait;

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $data = $this->form->getState();

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $credentials = $this->getCredentialsFromFormData($data);

        // Verifica se o usuário existe antes de tentar autenticar
        $user = $authGuard->getProvider()->retrieveByCredentials($credentials);

        if (! $user) {
            $this->throwFailureValidationException();
        }

        // Bloqueia login de usuários suspensos com notificação amigável
        if ($user instanceof User && $user->isSuspended()) {
            $this->notifyDanger('Conta suspensa', 'Sua conta está suspensa. Entre em contato com o suporte para mais informações.');

            return null;
        }

        // Continua com a autenticação padrão do Filament
        return parent::authenticate();
    }
}
