@extends('layouts.admin')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 fw-semibold mb-0">Nueva publicación</h1>
            <small class="text-muted">Crea una nueva publicación con archivo multimedia</small>
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

            <form method="POST"
                  action="{{ route(auth()->user()->role === 'superadmin' ? 'superadmin.publicaciones.store' : 'admin.publicaciones.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Título *</label>
                        <input type="text" name="titulo" value="{{ old('titulo') }}"
                               class="form-control form-control-sm" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Descripción *</label>
                        <textarea name="descripcion" rows="4"
                                  class="form-control form-control-sm" required>{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Proyecto *</label>
                        <select name="proyecto_id" class="form-select form-select-sm" required>
                            <option value="">Selecciona un proyecto</option>
                            @foreach($proyectos as $proyecto)
                                <option value="{{ $proyecto->id }}"
                                    {{ old('proyecto_id') == $proyecto->id ? 'selected' : '' }}>
                                    {{ $proyecto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Archivo principal</label>
                        <input type="file" name="archivo" id="archivo"
                               class="form-control form-control-sm"
                               accept="image/*,video/*">
                        <div class="form-text">Imagen o video. Máx. 100 MB.</div>
                        <div id="archivo-preview" class="mt-2 d-none">
                            <img id="preview-img" src="" class="img-thumbnail d-none" style="max-height:160px;">
                            <video id="preview-video" controls class="d-none w-100" style="max-height:160px;"></video>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Imágenes de soporte</label>
                        <input type="file" name="supp_images[]" id="supp_images"
                               class="form-control form-control-sm"
                               accept="image/*" multiple>
                        <div class="form-text">Máximo 3 imágenes. Solo formatos de imagen. Máx. 10 MB c/u.</div>
                        <div id="supp-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Preview archivo principal
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

    // Preview imágenes de soporte
    document.getElementById('supp_images').addEventListener('change', function () {
        const container = document.getElementById('supp-preview');
        container.innerHTML = '';

        if (this.files.length > 3) {
            alert('Solo puedes seleccionar hasta 3 imágenes de soporte.');
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
</script>
@endpush
