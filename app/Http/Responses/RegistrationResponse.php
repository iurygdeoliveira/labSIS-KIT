<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Enums\RoleType;
use App\Filament\Pages\Auth\VerificationPending;
use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as FilamentRegistrationResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class RegistrationResponse implements FilamentRegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        // Se usuário não está aprovado (exceto administradores), redirecionar para verificação pendente
        if (method_exists($user, 'hasRole') && ! $user->hasRole(RoleType::ADMIN->value)) {
            if (method_exists($user, 'isApproved') && ! $user->isApproved()) {
                return redirect()->to(VerificationPending::getUrl());
            }
        }

        // Se usuário está aprovado, redirecionar para painel apropriado
        if ($user->canAccessPanel(Filament::getPanel('admin'))) {
            return redirect()->to('/admin');
        }

        if ($user->canAccessPanel(Filament::getPanel('user'))) {
            $firstTenant = $user->tenants()->first();
            if ($firstTenant) {
                return redirect()->to('/user/'.$firstTenant->uuid.'/dashboard');
            }

            return redirect()->to('/user');
        }

        // Fallback para a rota home se nenhum role for encontrado
        return redirect()->route('home');
    }
}
