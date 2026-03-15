@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Constructoras</h1>
            <small class="text-muted">Listado de constructoras registradas</small>
        </div>
        <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.constructoras.create' : 'admin.constructoras.create') }}"
           class="btn btn-primary btn-sm">
            Nueva constructora
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
                            <th>NIT</th>
                            <th>Ciudad</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($constructoras as $constructora)
                            <tr>
                                <td>{{ $constructora->id }}</td>
                                <td>{{ $constructora->nombre }}</td>
                                <td>{{ $constructora->nit }}</td>
                                <td>{{ $constructora->ciudad }}</td>
                                <td>{{ $constructora->telefono }}</td>
                                <td>{{ $constructora->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $constructora->estado === 'activo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($constructora->estado) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.constructoras.edit' : 'admin.constructoras.edit', $constructora) }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay constructoras registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($constructoras->hasPages())
                <div class="p-3">
                    {{ $constructoras->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

