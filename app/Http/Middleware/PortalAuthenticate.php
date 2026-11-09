<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as Middleware;

class PortalAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        return route('portal.login');
    }
}
