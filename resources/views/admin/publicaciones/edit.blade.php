@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Editar publicación</h1>
            <small class="text-muted">Actualiza los datos de la publicación</small>
        </div>
        <a href="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.index' : 'admin.publicaciones.index') }}"
           class="btn btn-outline-secondary btn-sm">
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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST"
                  action="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.update' : 'admin.publicaciones.update', $publicacion) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Título *</label>
                        <input type="text" name="titulo"
                               value="{{ old('titulo', $publicacion->titulo) }}"
                               class="form-control form-control-sm" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Descripción *</label>
                        <textarea name="descripcion" rows="4"
                                  class="form-control form-control-sm" required>{{ old('descripcion', $publicacion->descripcion) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Proyecto *</label>
                        <select name="proyecto_id" class="form-select form-select-sm" required>
                            <option value="">Selecciona un proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}"
                                    {{ old('proyecto_id', $publicacion->proyecto_id) == $proyecto->id ? 'selected' : '' }}>
                                    {{ $proyecto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Archivo principal</label>
                        @if($publicacion->archivo_path)
                            <div class="mb-2 small text-muted">
                                Archivo actual:
                                <a href="{{ \App\Helpers\S3Helper::temporaryUrl($publicacion->archivo_directorio, basename($publicacion->archivo_path)) }}"
                                   target="_blank" class="text-decoration-none">
                                    {{ basename($publicacion->archivo_path) }}
                                </a>
                            </div>
                        @endif
                        <input type="file" name="archivo" id="archivo"
                               class="form-control form-control-sm"
                               accept="image/*,video/*">
                        <div class="form-text">Sube un nuevo archivo para reemplazar el actual. Máx. 100 MB.</div>
                        <div id="archivo-preview" class="mt-2 d-none">
                            <img id="preview-img" src="" class="img-thumbnail d-none" style="max-height:160px;">
                            <video id="preview-video" controls class="d-none w-100" style="max-height:160px;"></video>
                        </div>
                    </div>

                    {{-- Imágenes de soporte actuales --}}
                    @if($publicacion->suppImages->count())
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Imágenes de soporte actuales</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($publicacion->suppImages as $supp)
                                    <div class="text-center">
                                        <a href="{{ \App\Helpers\S3Helper::temporaryUrl(dirname($supp->directorio), basename($supp->directorio)) }}"
                                           target="_blank">
                                            <img src="{{ \App\Helpers\S3Helper::temporaryUrl(dirname($supp->directorio), basename($supp->directorio)) }}"
                                                 class="img-thumbnail" style="max-height:100px;">
                                        </a>
                                        <div class="mt-1">
                                            <form method="POST"
                                                  action="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.supp_images.destroy' : 'admin.publicaciones.supp_images.destroy', $supp) }}"
                                                  onsubmit="return confirm('¿Eliminar esta imagen de soporte?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" style="font-size:0.7rem;">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($publicacion->suppImages->count() < 3)
                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                Agregar imágenes de soporte
                                <span class="text-muted">({{ 3 - $publicacion->suppImages->count() }} disponible(s))</span>
                            </label>
                            <input type="file" name="supp_images[]" id="supp_images"
                                   class="form-control form-control-sm"
                                   accept="image/*" multiple>
                            <div class="form-text">Solo imágenes. Máx. 10 MB c/u.</div>
                            <div id="supp-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-warning small mb-0">
                                Ya tienes 3 imágenes de soporte. Elimina alguna para agregar nuevas.
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('archivo').addEventListener('change', function () {
        const file = this.files[0];
        const previewBox = document.getElementById('archivo-preview');
        const img = document.getElementById('preview-img');
        const video = document.getElementById('preview-video');

        img.classList.add('d-none');
        video.classList.add('d-none');

        if (!file) { previewBox.classList.add('d-none'); return; }

        previewBox.classList.remove('d-none');
        const url = URL.createObjectURL(file);

        if (file.type.startsWith('image/')) {
            img.src = url;
            img.classList.remove('d-none');
        } else if (file.type.startsWith('video/')) {
            video.src = url;
            video.classList.remove('d-none');
        }
    });

    const suppInput = document.getElementById('supp_images');
    if (suppInput) {
        const maxNew = {{ 3 - $publicacion->suppImages->count() }};
        suppInput.addEventListener('change', function () {
            const container = document.getElementById('supp-preview');
            container.innerHTML = '';

            if (this.files.length > maxNew) {
                alert('Solo puedes agregar hasta ' + maxNew + ' imagen(es) más.');
                this.value = '';
                return;
            }

            Array.from(this.files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'img-thumbnail';
                img.style.maxHeight = '100px';
                container.appendChild(img);
            });
        });
    }
</script>
@endpush
