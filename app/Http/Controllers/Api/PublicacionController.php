<?php

namespace App\Http\Controllers\Api;

use App\Helpers\S3Helper;
use App\Http\Controllers\Controller;
use App\Models\Publicacion;
use App\Models\SuppImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicacionController extends Controller
{
    /**
     * GET /api/publicaciones
     * Roles: superadmin, admin, seguridad, usuario
     *
     * Devuelve las publicaciones del proyecto_id del usuario autenticado.
     *
     * Query params:
     *   ?page=1
     *
     * Response 200:
     * {
     *   "data": [
     *     {
     *       "id", "titulo", "descripcion", "tiempo_transcurrido",
     *       "created_at", "updated_at",
     *       "archivo": { "url", "directorio" } | null,
     *       "supp_images": [ { "id", "url", "directorio", "created_at" } ],
     *       "proyecto": { "id", "nombre" },
     *       "publicado_por": { "id", "name" }
     *     }
     *   ],
     *   "total", "page", "per_page", "last_page"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->proyecto_id) {
            return response()->json([
                'data'      => [],
                'total'     => 0,
                'page'      => 1,
                'per_page'  => 15,
                'last_page' => 1,
            ]);
        }

        $paginator = Publicacion::with(['proyecto', 'user', 'suppImages'])
            ->where('proyecto_id', $user->proyecto_id)
            ->orderByDesc('created_at')
            ->paginate(15);

        $data = $paginator->getCollection()->map(fn (Publicacion $pub) => $this->format($pub));

        return response()->json([
            'data'      => $data,
            'total'     => $paginator->total(),
            'page'      => $paginator->currentPage(),
            'per_page'  => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * GET /api/publicaciones/{id}
     * Roles: superadmin, admin, seguridad, usuario
     *
     * Solo permite ver publicaciones del mismo proyecto_id del usuario.
     *
     * Response 200: publicación con detalle completo
     * Response 403: no pertenece al proyecto del usuario
     */
    public function show(Request $request, Publicacion $publicacion): JsonResponse
    {
        $user = $request->user();

        if ($publicacion->proyecto_id !== $user->proyecto_id) {
            return response()->json(['message' => 'No tienes acceso a esta publicación.'], 403);
        }

        $publicacion->load(['proyecto', 'user', 'suppImages']);

        return response()->json(['data' => $this->format($publicacion)]);
    }

    /**
     * POST /api/publicaciones
     * Roles: admin, superadmin
     *
     * Body (multipart/form-data):
     *   titulo        string required
     *   descripcion   string required
     *   proyecto_id   integer required
     *   archivo       file optional (imagen o video, máx 100 MB)
     *   supp_images[] file[] optional (máx 3 imágenes, máx 10 MB c/u)
     *
     * Response 201: { "message", "data": publicación }
     * Response 403: rol sin permiso
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'No tienes permisos para crear publicaciones.'], 403);
        }

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
            'user_id'     => $user->id,
        ]);

        if ($request->hasFile('archivo')) {
            $file      = $request->file('archivo');
            $directory = 'posts/' . $publicacion->id;
            $path      = S3Helper::upload($directory, $file, $file->getClientOriginalName(), 'private');

            if ($path === false) {
                $publicacion->delete();
                return response()->json(['message' => 'No se pudo subir el archivo a S3.'], 500);
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
                    return response()->json(['message' => 'No se pudo subir una imagen de soporte a S3.'], 500);
                }

                $supp->update(['directorio' => $suppPath]);
            }
        }

        $publicacion->load(['proyecto', 'user', 'suppImages']);

        return response()->json([
            'message' => 'Publicación creada correctamente.',
            'data'    => $this->format($publicacion),
        ], 201);
    }

    /**
     * PUT /api/publicaciones/{id}
     * Roles: admin, superadmin
     *
     * Body (multipart/form-data):
     *   titulo        string required
     *   descripcion   string required
     *   proyecto_id   integer required
     *   archivo       file optional (reemplaza el archivo actual)
     *   supp_images[] file[] optional (agrega imágenes; total no puede superar 3)
     *
     * Response 200: { "message", "data": publicación }
     * Response 403: rol sin permiso
     */
    public function update(Request $request, Publicacion $publicacion): JsonResponse
    {
        $user = $request->user();

        if (! in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'No tienes permisos para editar publicaciones.'], 403);
        }

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
            $path      = S3Helper::upload($directory, $file, $file->getClientOriginalName(), 'private');

            if ($path === false) {
                return response()->json(['message' => 'No se pudo subir el archivo a S3.'], 500);
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
                return response()->json([
                    'message' => 'No puedes tener más de 3 imágenes de soporte en total.',
                ], 422);
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
                    return response()->json(['message' => 'No se pudo subir una imagen de soporte a S3.'], 500);
                }

                $supp->update(['directorio' => $suppPath]);
            }
        }

        $publicacion->load(['proyecto', 'user', 'suppImages']);

        return response()->json([
            'message' => 'Publicación actualizada correctamente.',
            'data'    => $this->format($publicacion),
        ]);
    }

    private function format(Publicacion $pub): array
    {
        return [
            'id'                 => $pub->id,
            'titulo'             => $pub->titulo,
            'descripcion'        => $pub->descripcion,
            'tiempo_transcurrido' => $pub->created_at->locale('es')->diffForHumans(),
            'created_at'         => $pub->created_at->toIso8601String(),
            'updated_at'         => $pub->updated_at->toIso8601String(),
            'archivo'            => $pub->archivo_path
                ? [
                    'url'        => S3Helper::temporaryUrl(
                        $pub->archivo_directorio,
                        basename($pub->archivo_path)
                    ),
                    'directorio' => $pub->archivo_directorio,
                ]
                : null,
            'supp_images'        => $pub->suppImages->map(fn (SuppImage $s) => [
                'id'         => $s->id,
                'url'        => S3Helper::temporaryUrl(
                    dirname($s->directorio),
                    basename($s->directorio)
                ),
                'directorio' => $s->directorio,
                'created_at' => $s->created_at->toIso8601String(),
            ])->values(),
            'proyecto'           => [
                'id'     => $pub->proyecto?->id,
                'nombre' => $pub->proyecto?->nombre,
            ],
            'publicado_por'      => [
                'id'   => $pub->user?->id,
                'name' => $pub->user?->name,
            ],
        ];
    }
}
