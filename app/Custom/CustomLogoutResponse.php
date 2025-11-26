<?php

namespace App\Custom;

use Filament\Forms\Forms;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLogoutResponse implements LogoutResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to('/');
    }
}
