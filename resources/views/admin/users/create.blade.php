@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Crear usuario</h1>
            <small class="text-muted">Registra un nuevo usuario del sistema</small>
        </div>
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

            <form method="POST" action="{{ route(auth()->user()->hasRole('superadmin') ? 'superadmin.users.store' : 'admin.users.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nombres *</label>
                        <input type="text" name="nombres" value="{{ old('nombres') }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Número de torre</label>
                        <input type="text" name="numero_torre" value="{{ old('numero_torre') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Número de apartamento</label>
                        <input type="text" name="numero_apartamento" value="{{ old('numero_apartamento') }}" class="form-control form-control-sm">
                    </div>

                    @if(auth()->user()->hasRole('superadmin'))
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Proyecto
                                <span class="text-muted small">(obligatorio si el rol no es superadmin)</span>
                            </label>
                            <select name="proyecto_id" class="form-select form-select-sm">
                                <option value="">Sin proyecto (solo superadmin)</option>
                                @foreach($proyectos as $proyecto)
                                    <option value="{{ $proyecto->id }}" {{ old('proyecto_id') == $proyecto->id ? 'selected' : '' }}>
                                        {{ $proyecto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Proyecto</label>
                            <input type="text" class="form-control form-control-sm" value="{{ optional(auth()->user()->proyecto)->nombre }}" disabled>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Cédula *</label>
                        <input type="text" name="cedula" value="{{ old('cedula') }}" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Rol *</label>
                        <select name="role" class="form-select form-select-sm" required>
                            @foreach($roles as $role)
                                @if(auth()->user()->hasRole('admin') && $role === 'superadmin')
                                    @continue
                                @endif
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Crear usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

