<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AccountConfirmationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class VerificationNoticeController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('superadmin') || $user->email_verified_at) {
            return redirect()->route(
                match ($user->role) {
                    'superadmin' => 'superadmin.dashboard',
                    'admin' => 'admin.dashboard',
                    'seguridad' => 'seguridad.dashboard',
                    default => 'usuario.dashboard',
                }
            );
        }

        return view('auth.verify-email');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->hasRole('superadmin') || $user->email_verified_at) {
            return redirect()->route('login');
        }

        $request->validate([
            'email' => ['required', 'email', Rule::in([$user->email])],
        ]);

        $url = URL::temporarySignedRoute(
            'account.confirm',
            now()->addDays(7),
            ['user' => $user->id]
        );

        Mail::to($user->email)->send(new AccountConfirmationMail($user, $url));

        return back()->with('status', 'Te hemos enviado un nuevo enlace de confirmación.');
    }
}
