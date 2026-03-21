@extends('layouts.usuario')

@section('usuario-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Mis Visitas</h1>
            <small class="text-muted">Solicitudes de visita registradas</small>
        </div>
        <a href="{{ route('usuario.visitas.create') }}" class="btn btn-primary btn-sm">
            Nueva Solicitud
        </a>
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
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Proyecto</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Visitantes</th>
                            <th>Registrada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitas as $visita)
                            <tr>
                                <td>{{ $visita->id }}</td>
                                <td>{{ $visita->proyecto->nombre ?? '—' }}</td>
                                <td>{{ $visita->fecha_inicio->format('d/m/Y H:i') }}</td>
                                <td>{{ $visita->fecha_fin->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge
                                        @if($visita->estado === 'pendiente') bg-warning text-dark
                                        @else bg-success
                                        @endif">
                                        {{ ucfirst($visita->estado) }}
                                    </span>
                                </td>
                                <td>{{ $visita->cuerpos->count() }}</td>
                                <td>{{ $visita->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No tienes solicitudes de visita registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($visitas->hasPages())
                <div class="p-3">
                    {{ $visitas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
