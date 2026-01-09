<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Traits\Filament\NotificationsTrait;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as AuthLogin;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;

class Login extends AuthLogin
{
    use NotificationsTrait;

    #[\Override]
    public function authenticate(): ?LoginResponse
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

        // Bloqueia login de usuários suspensos
        if ($user instanceof User && $user->isSuspended()) {
            // Fazer login do usuário para que o middleware possa redirecionar
            $authGuard->login($user);

            // Redirecionar para página de conta suspensa
            $this->redirect(route('filament.auth.account-suspended'));

            return null;
        }

        // Verifica se o usuário foi aprovado pelo admin
        if ($user instanceof User && ! $user->isApproved()) {
            // Fazer login do usuário para que o middleware possa redirecionar
            $authGuard->login($user);

            // Redirecionar para página de verificação pendente
            $this->redirect(route('filament.auth.verification-pending'));

            return null;
        }

        // Continua com a autenticação padrão do Filament
        return parent::authenticate();
    }
}
