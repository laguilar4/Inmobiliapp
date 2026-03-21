@extends('layouts.seguridad')

@section('seguridad-content')
    <div class="mb-3">
        <h1 class="h4 fw-semibold mb-0">Reportes de Visitas</h1>
        <small class="text-muted">Listado de todos los visitantes registrados</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="cursor:pointer;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Correo</th>
                            <th>Proyecto</th>
                            <th>Fecha Inicio Visita</th>
                            <th>Estado</th>
                            <th>Última Actualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitantes as $visitante)
                            <tr onclick="window.location='{{ route('seguridad.reportes.show', $visitante) }}'"
                                title="Ver detalle">
                                <td>{{ $visitante->id }}</td>
                                <td>{{ $visitante->nombre }}</td>
                                <td>{{ $visitante->cedula }}</td>
                                <td>{{ $visitante->correo }}</td>
                                <td>{{ $visitante->cabecera->proyecto->nombre ?? '—' }}</td>
                                <td>{{ $visitante->cabecera->fecha_inicio->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($visitante->estado) {
                                            'entro'  => 'bg-success',
                                            'salio'  => 'bg-secondary',
                                            default  => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($visitante->estado) }}
                                    </span>
                                </td>
                                <td>{{ $visitante->updated_at ? $visitante->updated_at->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay visitantes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($visitantes->hasPages())
                <div class="p-3">
                    {{ $visitantes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
