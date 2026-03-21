<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailConfirmationController extends Controller
{
    public function confirm(Request $request, User $user): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'El enlace de confirmación no es válido o ha expirado.');
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return redirect()
            ->route('login')
            ->with('status', 'Tu cuenta ha sido confirmada. Ya puedes iniciar sesión.');
    }
}
