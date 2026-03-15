@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Nuevo proyecto</h1>
            <small class="text-muted">Crea un nuevo proyecto asociado a una constructora</small>
        </div>
        <a href="{{ route('superadmin.proyectos.index') }}" class="btn btn-outline-secondary btn-sm">
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

            <form method="POST" action="{{ route('superadmin.proyectos.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nombre del proyecto *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Número de torres *</label>
                        <input type="number" name="numero_torres" value="{{ old('numero_torres', 1) }}" min="1" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Constructora *</label>
                        <select name="constructora_id" class="form-select form-select-sm" required>
                            <option value="">Seleccione...</option>
                            @foreach($constructoras as $constructora)
                                <option value="{{ $constructora->id }}" {{ old('constructora_id') == $constructora->id ? 'selected' : '' }}>
                                    {{ $constructora->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

