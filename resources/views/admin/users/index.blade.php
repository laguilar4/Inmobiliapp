@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Usuarios</h1>
            <small class="text-muted">Usuarios del sistema</small>
        </div>
        <a href="{{ route(auth()->user()->hasRole('superadmin') ? 'superadmin.users.create' : 'admin.users.create') }}"
           class="btn btn-primary btn-sm">
            Nuevo usuario
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
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Proyecto</th>
                            <th>Confirmado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td>{{ $u->nombres }}</td>
                                <td>{{ $u->apellidos }}</td>
                                <td>{{ $u->email }}</td>
                                <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
                                <td>{{ $u->proyecto?->nombre ?? '—' }}</td>
                                <td>
                                    @if($u->hasRole('superadmin') || $u->email_verified_at)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route(auth()->user()->hasRole('superadmin') ? 'superadmin.users.edit' : 'admin.users.edit', $u) }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
