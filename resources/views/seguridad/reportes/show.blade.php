@extends('layouts.seguridad')

@section('seguridad-content')
    <div class="mb-3">
        <a href="{{ route('seguridad.reportes.index') }}" class="text-decoration-none text-muted small">
            ← Volver al listado
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Cabecera de la visita --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-semibold mb-0">Detalle de la Solicitud de Visita #{{ $visitante->cabecera->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted small">Proyecto</div>
                    <div class="fw-semibold">{{ $visitante->cabecera->proyecto->nombre ?? '—' }}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted small">Solicitado por</div>
                    <div class="fw-semibold">{{ $visitante->cabecera->usuario->name ?? '—' }}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted small">Fecha de Inicio</div>
                    <div class="fw-semibold">{{ $visitante->cabecera->fecha_inicio->format('d/m/Y H:i') }}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted small">Fecha de Fin</div>
                    <div class="fw-semibold">{{ $visitante->cabecera->fecha_fin->format('d/m/Y H:i') }}</div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted small">Estado Solicitud</div>
                    <span class="badge bg-warning text-dark">{{ ucfirst($visitante->cabecera->estado) }}</span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="text-muted small">Registrada</div>
                    <div>{{ $visitante->cabecera->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Todos los visitantes de esa cabecera --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-semibold mb-0">Todos los Visitantes de esta Solicitud</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>Última Act.</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visitante->cabecera->cuerpos as $cuerpo)
                            <tr @if($cuerpo->id === $visitante->id) class="table-primary" @endif>
                                <td>{{ $cuerpo->id }}</td>
                                <td>{{ $cuerpo->nombre }}</td>
                                <td>{{ $cuerpo->cedula }}</td>
                                <td>{{ $cuerpo->correo }}</td>
                                <td>
                                    @php
                                        $bc = match($cuerpo->estado) {
                                            'entro'  => 'bg-success',
                                            'salio'  => 'bg-secondary',
                                            default  => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $bc }}">{{ ucfirst($cuerpo->estado) }}</span>
                                </td>
                                <td>{{ $cuerpo->updated_at ? $cuerpo->updated_at->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    @if($cuerpo->id !== $visitante->id)
                                        <a href="{{ route('seguridad.reportes.show', $cuerpo) }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            Ver / Editar
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Actualizar estado del visitante seleccionado --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h6 class="fw-semibold mb-0">
                Actualizar Estado de: <span class="text-primary">{{ $visitante->nombre }}</span>
                (Cédula: {{ $visitante->cedula }})
            </h6>
        </div>
        <div class="card-body">
            <form method="POST"
                  action="{{ route('seguridad.reportes.updateEstado', $visitante) }}">
                @csrf
                @method('PATCH')

                <div class="row align-items-end g-3">
                    <div class="col-sm-6 col-md-4">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado"
                                class="form-select @error('estado') is-invalid @enderror">
                            <option value="pendiente" @selected($visitante->estado === 'pendiente')>Pendiente</option>
                            <option value="entro"     @selected($visitante->estado === 'entro')>Entró</option>
                            <option value="salio"     @selected($visitante->estado === 'salio')>Salió</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Guardar Cambio
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
