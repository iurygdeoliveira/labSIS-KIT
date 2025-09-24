<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as FilamentLoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements FilamentLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        if ($user->canAccessPanel(Filament::getPanel('admin'))) {
            return redirect()->to('/admin');
        }

        if ($user->canAccessPanel(Filament::getPanel('user'))) {
            return redirect()->to('/user');
        }

        // Fallback para a rota home se nenhum role for encontrado
        return redirect()->route('home');
    }
}
