@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Detalle de publicación</h1>
            <small class="text-muted">#{{ $publicacion->id }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.edit' : 'admin.publicaciones.edit', $publicacion) }}"
               class="btn btn-outline-primary btn-sm">
                Editar
            </a>
            <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.index' : 'admin.publicaciones.index') }}"
               class="btn btn-outline-secondary btn-sm">
                Volver al listado
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- Información principal --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold mb-1">{{ $publicacion->titulo }}</h5>
                    <div class="text-muted small mb-3">
                        Proyecto: <strong>{{ $publicacion->proyecto->nombre ?? '—' }}</strong>
                        &nbsp;·&nbsp;
                        Publicado por: <strong>{{ $publicacion->user->name ?? '—' }}</strong>
                    </div>

                    <p class="mb-4" style="white-space: pre-wrap;">{{ $publicacion->descripcion }}</p>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <span class="fw-semibold">Creado:</span>
                            {{ $publicacion->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-6">
                            <span class="fw-semibold">Actualizado:</span>
                            {{ $publicacion->updated_at->format('d/m/Y H:i') }}
                        </div>
                        @if($publicacion->archivo_directorio)
                            <div class="col-12 mt-1">
                                <span class="fw-semibold">Directorio en S3:</span>
                                <code>{{ $publicacion->archivo_directorio }}</code>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Información del registro</h6>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">ID</dt>
                        <dd class="col-7">{{ $publicacion->id }}</dd>

                        <dt class="col-5 text-muted">Proyecto ID</dt>
                        <dd class="col-7">{{ $publicacion->proyecto_id }}</dd>

                        <dt class="col-5 text-muted">Usuario ID</dt>
                        <dd class="col-7">{{ $publicacion->user_id }}</dd>

                        <dt class="col-5 text-muted">Archivo</dt>
                        <dd class="col-7">
                            @if($publicacion->archivo_path)
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="text-muted">Sin archivo</span>
                            @endif
                        </dd>

                        <dt class="col-5 text-muted">Imgs. soporte</dt>
                        <dd class="col-7">{{ $publicacion->suppImages->count() }} / 3</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Archivo principal --}}
        @if($publicacion->archivo_path)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Archivo principal</h6>
                        @php
                            $ext = strtolower(pathinfo($publicacion->archivo_path, PATHINFO_EXTENSION));
                            $imageExts = ['jpg','jpeg','png','gif','webp'];
                            $videoExts = ['mp4','mov','avi','mkv','webm'];
                            $fileUrl = \App\Helpers\S3Helper::temporaryUrl(
                                $publicacion->archivo_directorio,
                                basename($publicacion->archivo_path)
                            );
                        @endphp

                        @if(in_array($ext, $imageExts))
                            <img src="{{ $fileUrl }}" class="img-fluid rounded" style="max-height:400px;">
                        @elseif(in_array($ext, $videoExts))
                            <video controls class="w-100 rounded" style="max-height:400px;">
                                <source src="{{ $fileUrl }}">
                                Tu navegador no soporta la reproducción de video.
                            </video>
                        @else
                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                Descargar archivo
                            </a>
                        @endif

                        <div class="mt-2 text-muted small">
                            Nombre: <code>{{ basename($publicacion->archivo_path) }}</code>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Imágenes de soporte --}}
        @if($publicacion->suppImages->count())
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Imágenes de soporte</h6>
                        <div class="row g-3">
                            @foreach($publicacion->suppImages as $supp)
                                @php
                                    $suppUrl = \App\Helpers\S3Helper::temporaryUrl(
                                        dirname($supp->directorio),
                                        basename($supp->directorio)
                                    );
                                @endphp
                                <div class="col-md-4">
                                    <div class="card border shadow-none">
                                        <a href="{{ $suppUrl }}" target="_blank">
                                            <img src="{{ $suppUrl }}" class="card-img-top"
                                                 style="object-fit:cover; height:180px;">
                                        </a>
                                        <div class="card-body p-2 small text-muted">
                                            <div>ID: {{ $supp->id }}</div>
                                            <div>Directorio: <code style="font-size:0.7rem;">{{ $supp->directorio }}</code></div>
                                            <div>{{ $supp->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
