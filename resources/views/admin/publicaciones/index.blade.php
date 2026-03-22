@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Publicaciones</h1>
            <small class="text-muted">Listado de publicaciones registradas</small>
        </div>
        <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.create' : 'admin.publicaciones.create') }}"
           class="btn btn-primary btn-sm">
            Nueva publicación
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
                            <th>Título</th>
                            <th>Proyecto</th>
                            <th>Publicado por</th>
                            <th>Archivo</th>
                            <th>Imágenes soporte</th>
                            <th>Fecha creación</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($publicaciones as $pub)
                            <tr style="cursor:pointer;"
                                onclick="window.open('{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.show' : 'admin.publicaciones.show', $pub) }}', '_blank')">
                                <td>{{ $pub->id }}</td>
                                <td class="fw-semibold">{{ $pub->titulo }}</td>
                                <td>{{ $pub->proyecto->nombre ?? '—' }}</td>
                                <td>{{ $pub->user->name ?? '—' }}</td>
                                <td>
                                    @if($pub->archivo_path)
                                        <span class="badge bg-info text-dark">Sí</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $pub->suppImages->count() }}</span>
                                </td>
                                <td>{{ $pub->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end" onclick="event.stopPropagation()">
                                    <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.edit' : 'admin.publicaciones.edit', $pub) }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay publicaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($publicaciones->hasPages())
                <div class="p-3">
                    {{ $publicaciones->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
