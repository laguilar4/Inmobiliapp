@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Editar constructora</h1>
            <small class="text-muted">Actualiza la información de la empresa</small>
        </div>
        <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.constructoras.index' : 'admin.constructoras.index') }}"
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
                  action="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.constructoras.update' : 'admin.constructoras.update', $constructora) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $constructora->nombre) }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">NIT *</label>
                        <input type="text" name="nit" value="{{ old('nit', $constructora->nit) }}" class="form-control form-control-sm" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $constructora->direccion) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad', $constructora->ciudad) }}" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $constructora->telefono) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" value="{{ old('email', $constructora->email) }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Representante legal</label>
                        <input type="text" name="representante_legal" value="{{ old('representante_legal', $constructora->representante_legal) }}" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Fecha de creación</label>
                        <input
                            type="date"
                            name="fecha_creacion"
                            value="{{ old('fecha_creacion', optional($constructora->fecha_creacion)->format('Y-m-d')) }}"
                            class="form-control form-control-sm"
                        >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Estado *</label>
                        <select name="estado" class="form-select form-select-sm" required>
                            <option value="activo" {{ old('estado', $constructora->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado', $constructora->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
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

