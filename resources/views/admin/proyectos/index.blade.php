@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Proyectos</h1>
            <small class="text-muted">Listado de proyectos por constructora</small>
        </div>
        <a href="{{ route('superadmin.proyectos.create') }}" class="btn btn-primary btn-sm">
            Nuevo proyecto
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
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Número de torres</th>
                            <th>Constructora</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyectos as $proyecto)
                            <tr>
                                <td>{{ $proyecto->id }}</td>
                                <td>{{ $proyecto->nombre }}</td>
                                <td>{{ $proyecto->numero_torres }}</td>
                                <td>{{ $proyecto->constructora?->nombre }}</td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.proyectos.edit', $proyecto) }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No hay proyectos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($proyectos->hasPages())
                <div class="p-3">
                    {{ $proyectos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

