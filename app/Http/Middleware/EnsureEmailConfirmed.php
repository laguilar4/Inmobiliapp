<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailConfirmed
{
    /**
     * Exige que el correo esté confirmado (email_verified_at).
     * El rol superadmin queda exento.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        if ($user->email_verified_at !== null) {
            return $next($request);
        }

        return redirect()
            ->route('verification.notice')
            ->with('warning', 'Debes confirmar tu correo electrónico para continuar.');
    }
}
