<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitaCuerpo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /**
     * GET /api/seguridad/reportes
     * Header: Authorization: Bearer {token}
     *
     * Query params opcionales:
     *   ?estado=pendiente|entro|salio
     *   ?page=1
     *
     * Response 200:
     * {
     *   "data": [
     *     {
     *       "id", "nombre", "cedula", "correo", "estado", "updated_at",
     *       "visita": { "id", "proyecto", "fecha_inicio", "fecha_fin", "solicitado_por" }
     *     }
     *   ],
     *   "total", "page", "per_page", "last_page"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = VisitaCuerpo::with(['cabecera.proyecto', 'cabecera.usuario'])
            ->orderByDesc('id');

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $paginator = $query->paginate(20);

        $data = $paginator->getCollection()->map(fn (VisitaCuerpo $c) => $this->formatVisitante($c));

        return response()->json([
            'data'      => $data,
            'total'     => $paginator->total(),
            'page'      => $paginator->currentPage(),
            'per_page'  => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * GET /api/seguridad/reportes/{id}
     * Header: Authorization: Bearer {token}
     *
     * Response 200:
     * {
     *   "visitante": { id, nombre, cedula, correo, estado, updated_at },
     *   "cabecera":  { id, proyecto_id, proyecto, fecha_inicio, fecha_fin, estado, solicitado_por, created_at },
     *   "todos_los_visitantes": [ { id, nombre, cedula, correo, estado, updated_at } ]
     * }
     */
    public function show(VisitaCuerpo $visitante): JsonResponse
    {
        $visitante->load(['cabecera.proyecto', 'cabecera.usuario', 'cabecera.cuerpos']);

        $cabecera = $visitante->cabecera;

        return response()->json([
            'visitante' => [
                'id'         => $visitante->id,
                'nombre'     => $visitante->nombre,
                'cedula'     => $visitante->cedula,
                'correo'     => $visitante->correo,
                'estado'     => $visitante->estado,
                'updated_at' => $visitante->updated_at?->toIso8601String(),
            ],
            'cabecera' => [
                'id'            => $cabecera->id,
                'proyecto_id'   => $cabecera->proyecto_id,
                'proyecto'      => $cabecera->proyecto?->nombre,
                'fecha_inicio'  => $cabecera->fecha_inicio->toIso8601String(),
                'fecha_fin'     => $cabecera->fecha_fin->toIso8601String(),
                'estado'        => $cabecera->estado,
                'solicitado_por' => $cabecera->usuario?->name,
                'created_at'    => $cabecera->created_at->toIso8601String(),
            ],
            'todos_los_visitantes' => $cabecera->cuerpos->map(fn (VisitaCuerpo $c) => [
                'id'         => $c->id,
                'nombre'     => $c->nombre,
                'cedula'     => $c->cedula,
                'correo'     => $c->correo,
                'estado'     => $c->estado,
                'updated_at' => $c->updated_at?->toIso8601String(),
            ])->values(),
        ]);
    }

    /**
     * PATCH /api/seguridad/reportes/{id}/estado
     * Header: Authorization: Bearer {token}
     *
     * Body: { "estado": "entro" | "salio" | "pendiente" }
     *
     * Response 200:
     * {
     *   "message": "Estado actualizado correctamente.",
     *   "visitante": { id, nombre, cedula, correo, estado, updated_at }
     * }
     */
    public function updateEstado(Request $request, VisitaCuerpo $visitante): JsonResponse
    {
        $data = $request->validate([
            'estado' => ['required', 'in:pendiente,entro,salio'],
        ]);

        $visitante->estado     = $data['estado'];
        $visitante->updated_at = now();
        $visitante->save();

        return response()->json([
            'message'   => 'Estado actualizado correctamente.',
            'visitante' => [
                'id'         => $visitante->id,
                'nombre'     => $visitante->nombre,
                'cedula'     => $visitante->cedula,
                'correo'     => $visitante->correo,
                'estado'     => $visitante->estado,
                'updated_at' => $visitante->updated_at->toIso8601String(),
            ],
        ]);
    }

    private function formatVisitante(VisitaCuerpo $c): array
    {
        return [
            'id'         => $c->id,
            'nombre'     => $c->nombre,
            'cedula'     => $c->cedula,
            'correo'     => $c->correo,
            'estado'     => $c->estado,
            'updated_at' => $c->updated_at?->toIso8601String(),
            'visita'     => [
                'id'            => $c->cabecera->id,
                'proyecto'      => $c->cabecera->proyecto?->nombre,
                'fecha_inicio'  => $c->cabecera->fecha_inicio->toIso8601String(),
                'fecha_fin'     => $c->cabecera->fecha_fin->toIso8601String(),
                'solicitado_por' => $c->cabecera->usuario?->name,
            ],
        ];
    }
}
