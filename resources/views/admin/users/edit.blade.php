@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Editar usuario</h1>
            <small class="text-muted">Actualiza datos y contraseña (opcional)</small>
        </div>
        <a href="{{ route(auth()->user()->hasRole('superadmin') ? 'superadmin.users.index' : 'admin.users.index') }}"
           class="btn btn-outline-secondary btn-sm">
            Volver al listado
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route(auth()->user()->hasRole('superadmin') ? 'superadmin.users.update' : 'admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nombres *</label>
                        <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Número de torre</label>
                        <input type="text" name="numero_torre" value="{{ old('numero_torre', $user->numero_torre) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Número de apartamento</label>
                        <input type="text" name="numero_apartamento" value="{{ old('numero_apartamento', $user->numero_apartamento) }}" class="form-control form-control-sm">
                    </div>

                    @if(auth()->user()->hasRole('superadmin'))
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Proyecto
                                <span class="text-muted small">(obligatorio si el rol no es superadmin)</span>
                            </label>
                            <select name="proyecto_id" class="form-select form-select-sm">
                                <option value="">Sin proyecto (solo superadmin)</option>
                                @foreach($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id }}" {{ (string) old('proyecto_id', $user->proyecto_id) === (string) $proyecto->id ? 'selected' : '' }}>
                                        {{ $proyecto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Proyecto</label>
                            <input type="text" class="form-control form-control-sm" value="{{ optional($user->proyecto)->nombre ?? optional(auth()->user()->proyecto)->nombre }}" disabled>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Cédula *</label>
                        <input type="text" name="cedula" value="{{ old('cedula', $user->cedula) }}" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Rol *</label>
                        <select name="role" class="form-select form-select-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <p class="small text-muted mb-2">Cambiar contraseña (opcional)</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control form-control-sm" autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm" autocomplete="new-password">
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
