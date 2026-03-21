@extends('layouts.usuario')

@section('usuario-content')
    <div class="mb-3">
        <a href="{{ route('usuario.visitas.index') }}" class="text-decoration-none text-muted small">
            ← Volver a Mis Visitas
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h1 class="h4 fw-semibold mb-4">Nueva Solicitud de Visita</h1>

            @if($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('usuario.visitas.store') }}" id="form-visita">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha y Hora de Inicio <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_inicio"
                               class="form-control @error('fecha_inicio') is-invalid @enderror"
                               value="{{ old('fecha_inicio') }}">
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha y Hora de Fin <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_fin"
                               class="form-control @error('fecha_fin') is-invalid @enderror"
                               value="{{ old('fecha_fin') }}">
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Visitantes</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-agregar">
                        + Agregar Visitante
                    </button>
                </div>

                <div id="visitantes-container">
                    {{-- Fila inicial --}}
                    <div class="visitante-row card border mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold small text-muted">Visitante #1</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" name="visitantes[0][nombre]"
                                           class="form-control form-control-sm"
                                           value="{{ old('visitantes.0.nombre') }}"
                                           placeholder="Nombre y apellido">
                                    @error('visitantes.0.nombre')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Cédula <span class="text-danger">*</span></label>
                                    <input type="text" name="visitantes[0][cedula]"
                                           class="form-control form-control-sm"
                                           value="{{ old('visitantes.0.cedula') }}"
                                           placeholder="Número de cédula">
                                    @error('visitantes.0.cedula')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Correo electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="visitantes[0][correo]"
                                           class="form-control form-control-sm"
                                           value="{{ old('visitantes.0.correo') }}"
                                           placeholder="correo@ejemplo.com">
                                    @error('visitantes.0.correo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('usuario.visitas.index') }}" class="btn btn-outline-secondary btn-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let counter = 1;

    document.getElementById('btn-agregar').addEventListener('click', function () {
        const container = document.getElementById('visitantes-container');
        const idx = counter++;

        const row = document.createElement('div');
        row.className = 'visitante-row card border mb-3';
        row.innerHTML = `
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold small text-muted">Visitante #${idx + 1}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar">Eliminar</button>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" name="visitantes[${idx}][nombre]" class="form-control form-control-sm" placeholder="Nombre y apellido">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Cédula <span class="text-danger">*</span></label>
                        <input type="text" name="visitantes[${idx}][cedula]" class="form-control form-control-sm" placeholder="Número de cédula">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="visitantes[${idx}][correo]" class="form-control form-control-sm" placeholder="correo@ejemplo.com">
                    </div>
                </div>
            </div>`;

        row.querySelector('.btn-eliminar').addEventListener('click', function () {
            row.remove();
        });

        container.appendChild(row);
    });
</script>
@endpush
