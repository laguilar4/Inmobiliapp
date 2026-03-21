<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountConfirmationMail;
use App\Mail\NewUserPasswordMail;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = $this->usersQuery()
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => $this->rolesForForm(),
            'proyectos' => $this->proyectosForForm(),
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

        if ($currentUser->hasRole('superadmin')) {
            if ($data['role'] !== 'superadmin' && empty($data['proyecto_id'])) {
                return back()
                    ->withErrors(['proyecto_id' => 'Debe seleccionar un proyecto para este usuario.'])
                    ->withInput();
            }
        } else {
            $data['proyecto_id'] = $currentUser->proyecto_id;
        }

        $plainPassword = Str::random(10);

        $user = new User;
        $user->name = $data['nombres'].' '.$data['apellidos'];
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

        if ($data['role'] === 'superadmin') {
            $user->email_verified_at = now();
        } else {
            $user->email_verified_at = null;
        }

        $user->save();

        $confirmationUrl = null;
        if ($data['role'] !== 'superadmin') {
            $confirmationUrl = URL::temporarySignedRoute(
                'account.confirm',
                now()->addDays(7),
                ['user' => $user->id]
            );
        }

        Mail::to($user->email)->send(new NewUserPasswordMail($user, $plainPassword, $confirmationUrl));

        return redirect()
            ->route($this->usersIndexRoute())
            ->with('success', 'Usuario creado. Se envió correo con contraseña y enlace de confirmación.');
    }

    public function edit(User $user): View
    {
        $this->authorizeManagedUser($user);

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->rolesForForm(),
            'proyectos' => $this->proyectosForForm(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManagedUser($user);

        $currentUser = auth()->user();

        $baseRules = [
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'numero_torre' => ['nullable', 'string', 'max:50'],
            'numero_apartamento' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'cedula' => ['required', 'string', 'max:100', 'unique:users,cedula,'.$user->id],
            'role' => ['required', 'in:superadmin,admin,seguridad,usuario'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($currentUser->hasRole('superadmin')) {
            $baseRules['proyecto_id'] = ['nullable', 'exists:proyectos,id'];
        }

        $data = $request->validate($baseRules);

        if ($currentUser->hasRole('superadmin')) {
            if ($data['role'] !== 'superadmin' && empty($data['proyecto_id'])) {
                return back()
                    ->withErrors(['proyecto_id' => 'Debe seleccionar un proyecto para este usuario.'])
                    ->withInput();
            }
        } else {
            $data['proyecto_id'] = $currentUser->proyecto_id;
            if ($data['role'] === 'superadmin') {
                abort(403);
            }
        }

        $emailChanged = $user->email !== $data['email'];
        $roleChangedFromSuperadmin = $user->role === 'superadmin' && $data['role'] !== 'superadmin';

        $user->name = $data['nombres'].' '.$data['apellidos'];
        $user->nombres = $data['nombres'];
        $user->apellidos = $data['apellidos'];
        $user->telefono = $data['telefono'] ?? null;
        $user->numero_torre = $data['numero_torre'] ?? null;
        $user->numero_apartamento = $data['numero_apartamento'] ?? null;
        $user->proyecto_id = $data['proyecto_id'] ?? null;
        $user->email = $data['email'];
        $user->cedula = $data['cedula'];
        $user->role = $data['role'];

        if ($data['role'] === 'superadmin') {
            $user->proyecto_id = null;
            $user->email_verified_at = now();
        } elseif ($roleChangedFromSuperadmin || $emailChanged) {
            $user->email_verified_at = null;
        }

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        if ($emailChanged && $user->role !== 'superadmin') {
            $url = URL::temporarySignedRoute(
                'account.confirm',
                now()->addDays(7),
                ['user' => $user->id]
            );
            Mail::to($user->email)->send(new AccountConfirmationMail($user, $url));
        }

        return redirect()
            ->route($this->usersIndexRoute())
            ->with('success', 'Usuario actualizado correctamente.');
    }

    private function usersQuery(): Builder
    {
        $q = User::query()->with('proyecto');

        if (auth()->user()->hasRole('superadmin')) {
            return $q;
        }

        return $q
            ->where('proyecto_id', auth()->user()->proyecto_id)
            ->where('role', '!=', 'superadmin');
    }

    private function authorizeManagedUser(User $user): void
    {
        if (auth()->user()->hasRole('superadmin')) {
            return;
        }

        if ($user->role === 'superadmin') {
            abort(403);
        }

        if ($user->proyecto_id !== auth()->user()->proyecto_id) {
            abort(403);
        }
    }

    private function proyectosForForm(): Collection
    {
        if (auth()->user()->hasRole('superadmin')) {
            return Proyecto::orderBy('nombre')->get();
        }

        return collect();
    }

    /**
     * @return list<string>
     */
    private function rolesForForm(): array
    {
        $roles = ['superadmin', 'admin', 'seguridad', 'usuario'];

        if (auth()->user()->hasRole('admin')) {
            return array_values(array_filter($roles, fn ($r) => $r !== 'superadmin'));
        }

        return $roles;
    }

    private function usersIndexRoute(): string
    {
        return auth()->user()->hasRole('superadmin')
            ? 'superadmin.users.index'
            : 'admin.users.index';
    }
}
