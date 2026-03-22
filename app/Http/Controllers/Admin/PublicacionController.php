<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\S3Helper;
use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Publicacion;
use App\Models\SuppImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicacionController extends Controller
{
    public function index(): View
    {
        $publicaciones = Publicacion::with(['proyecto', 'user'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.publicaciones.index', compact('publicaciones'));
    }

    public function create(): View
    {
        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('admin.publicaciones.create', compact('proyectos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo'        => ['required', 'string', 'max:255'],
            'descripcion'   => ['required', 'string'],
            'proyecto_id'   => ['required', 'exists:proyectos,id'],
            'archivo'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,webm', 'max:102400'],
            'supp_images'   => ['nullable', 'array', 'max:3'],
            'supp_images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ]);

        $publicacion = Publicacion::create([
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
            'proyecto_id' => $request->proyecto_id,
            'user_id'     => auth()->id(),
        ]);

        if ($request->hasFile('archivo')) {
            $file      = $request->file('archivo');
            $directory = 'posts/' . $publicacion->id;
            $filename  = $file->getClientOriginalName();
            $path      = S3Helper::upload($directory, $file, $filename, 'private');

            if ($path === false) {
                $publicacion->delete();
                return back()->withInput()
                    ->withErrors(['archivo' => 'No se pudo subir el archivo a S3. Verifica las credenciales y permisos del bucket.']);
            }

            $publicacion->update([
                'archivo_path'       => $path,
                'archivo_directorio' => $directory,
            ]);
        }

        if ($request->hasFile('supp_images')) {
            foreach ($request->file('supp_images') as $img) {
                $supp = SuppImage::create([
                    'publicacion_id' => $publicacion->id,
                    'directorio'     => '',
                ]);

                $suppDir  = 'posts/supp_images/' . $supp->id;
                $suppPath = S3Helper::upload($suppDir, $img, $img->getClientOriginalName(), 'private');

                if ($suppPath === false) {
                    $supp->delete();
                    return back()->withInput()
                        ->withErrors(['supp_images' => 'No se pudo subir una imagen de soporte a S3. Verifica las credenciales y permisos del bucket.']);
                }

                $supp->update(['directorio' => $suppPath]);
            }
        }

        $route = auth()->user()->role === 'superadmin'
            ? 'superadmin.publicaciones.index'
            : 'admin.publicaciones.index';

        return redirect()->route($route)->with('success', 'Publicación creada correctamente.');
    }

    public function show(Publicacion $publicacion): View
    {
        $publicacion->load(['proyecto', 'user', 'suppImages']);

        return view('admin.publicaciones.show', compact('publicacion'));
    }

    public function edit(Publicacion $publicacion): View
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $publicacion->load('suppImages');

        return view('admin.publicaciones.edit', compact('publicacion', 'proyectos'));
    }

    public function update(Request $request, Publicacion $publicacion): RedirectResponse
    {
        $request->validate([
            'titulo'        => ['required', 'string', 'max:255'],
            'descripcion'   => ['required', 'string'],
            'proyecto_id'   => ['required', 'exists:proyectos,id'],
            'archivo'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,webm', 'max:102400'],
            'supp_images'   => ['nullable', 'array', 'max:3'],
            'supp_images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ]);

        $publicacion->update([
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
            'proyecto_id' => $request->proyecto_id,
        ]);

        if ($request->hasFile('archivo')) {
            if ($publicacion->archivo_directorio) {
                S3Helper::delete($publicacion->archivo_directorio, basename($publicacion->archivo_path));
            }

            $file      = $request->file('archivo');
            $directory = 'posts/' . $publicacion->id;
            $filename  = $file->getClientOriginalName();
            $path      = S3Helper::upload($directory, $file, $filename, 'private');

            if ($path === false) {
                return back()->withInput()
                    ->withErrors(['archivo' => 'No se pudo subir el archivo a S3. Verifica las credenciales y permisos del bucket.']);
            }

            $publicacion->update([
                'archivo_path'       => $path,
                'archivo_directorio' => $directory,
            ]);
        }

        if ($request->hasFile('supp_images')) {
            $currentCount = $publicacion->suppImages()->count();
            $newCount     = count($request->file('supp_images'));

            if (($currentCount + $newCount) > 3) {
                return back()
                    ->withErrors(['supp_images' => 'No puedes tener más de 3 imágenes de soporte en total.'])
                    ->withInput();
            }

            foreach ($request->file('supp_images') as $img) {
                $supp = SuppImage::create([
                    'publicacion_id' => $publicacion->id,
                    'directorio'     => '',
                ]);

                $suppDir  = 'posts/supp_images/' . $supp->id;
                $suppPath = S3Helper::upload($suppDir, $img, $img->getClientOriginalName(), 'private');

                if ($suppPath === false) {
                    $supp->delete();
                    return back()->withInput()
                        ->withErrors(['supp_images' => 'No se pudo subir una imagen de soporte a S3. Verifica las credenciales y permisos del bucket.']);
                }

                $supp->update(['directorio' => $suppPath]);
            }
        }

        $route = auth()->user()->role === 'superadmin'
            ? 'superadmin.publicaciones.index'
            : 'admin.publicaciones.index';

        return redirect()->route($route)->with('success', 'Publicación actualizada correctamente.');
    }

    public function destroySuppImage(SuppImage $suppImage): RedirectResponse
    {
        $publicacion = $suppImage->publicacion;

        $directory = dirname($suppImage->directorio);
        $filename  = basename($suppImage->directorio);
        S3Helper::delete($directory, $filename);

        $suppImage->delete();

        $route = auth()->user()->role === 'superadmin'
            ? 'superadmin.publicaciones.edit'
            : 'admin.publicaciones.edit';

        return redirect()->route($route, $publicacion)->with('success', 'Imagen de soporte eliminada.');
    }
}
