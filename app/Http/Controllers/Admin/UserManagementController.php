<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewUserPasswordMail;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function create(): View
    {
        $roles = ['superadmin', 'admin', 'seguridad', 'usuario'];

        $proyectos = collect();

        if (auth()->user()->hasRole('superadmin')) {
            $proyectos = Proyecto::orderBy('nombre')->get();
        }

        return view('admin.users.create', [
            'roles' => $roles,
            'proyectos' => $proyectos,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $currentUser = auth()->user();

        $baseRules = [
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'numero_torre' => ['nullable', 'string', 'max:50'],
            'numero_apartamento' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'cedula' => ['required', 'string', 'max:100', 'unique:users,cedula'],
            'role' => ['required', 'in:superadmin,admin,seguridad,usuario'],
        ];

        if ($currentUser->hasRole('superadmin')) {
            $baseRules['proyecto_id'] = ['nullable', 'exists:proyectos,id'];
        }

        $data = $request->validate($baseRules);

        // Regla de proyecto:
        // - superadmin: si role != superadmin => proyecto_id requerido
        // - admin: siempre usa su propio proyecto_id, no puede cambiarlo
        if ($currentUser->hasRole('superadmin')) {
            if ($data['role'] !== 'superadmin' && empty($data['proyecto_id'])) {
                return back()
                    ->withErrors(['proyecto_id' => 'Debe seleccionar un proyecto para este usuario.'])
                    ->withInput();
            }
        } else {
            // admin
            $data['proyecto_id'] = $currentUser->proyecto_id;
        }

        // Generar contraseña aleatoria
        $plainPassword = Str::random(10);

        $user = new User();
        $user->name = $data['nombres'] . ' ' . $data['apellidos'];
        $user->nombres = $data['nombres'];
        $user->apellidos = $data['apellidos'];
        $user->telefono = $data['telefono'] ?? null;
        $user->numero_torre = $data['numero_torre'] ?? null;
        $user->numero_apartamento = $data['numero_apartamento'] ?? null;
        $user->proyecto_id = $data['proyecto_id'] ?? null;
        $user->email = $data['email'];
        $user->cedula = $data['cedula'];
        $user->role = $data['role'];
        $user->password = $plainPassword;
        $user->save();

        // Enviar correo con la contraseña generada
        Mail::to($user->email)->send(new NewUserPasswordMail($user, $plainPassword));

        $redirectRoute = $currentUser->hasRole('superadmin')
            ? 'superadmin.dashboard'
            : 'admin.dashboard';

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Usuario creado correctamente y contraseña enviada por correo.');
    }
}

