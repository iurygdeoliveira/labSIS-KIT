<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse extends Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to('/');
    }
}
