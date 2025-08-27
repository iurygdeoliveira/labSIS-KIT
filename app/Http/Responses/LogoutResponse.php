<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as FilamentLogoutResponse;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements FilamentLogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        // Redireciona para a rota raiz (página home)
        return redirect()->route('home');
    }
}
