<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VisitaQrMail;
use App\Models\VisitaCabecera;
use App\Models\VisitaCuerpo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VisitaController extends Controller
{
    /**
     * GET /api/usuario/visitas
     * Header: Authorization: Bearer {token}
     *
     * Response 200:
     * {
     *   "data": [
     *     {
     *       "id", "proyecto_id", "proyecto", "fecha_inicio", "fecha_fin",
     *       "estado", "created_at",
     *       "visitantes": [ { "id", "nombre", "cedula", "correo", "estado", "updated_at" } ]
     *     }
     *   ],
     *   "total": 10,
     *   "page": 1,
     *   "per_page": 15,
     *   "last_page": 1
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = VisitaCabecera::with(['proyecto', 'cuerpos'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(15);

        $data = $paginator->getCollection()->map(fn (VisitaCabecera $v) => [
            'id'           => $v->id,
            'proyecto_id'  => $v->proyecto_id,
            'proyecto'     => $v->proyecto?->nombre,
            'fecha_inicio' => $v->fecha_inicio->toIso8601String(),
            'fecha_fin'    => $v->fecha_fin->toIso8601String(),
            'estado'       => $v->estado,
            'created_at'   => $v->created_at->toIso8601String(),
            'visitantes'   => $v->cuerpos->map(fn (VisitaCuerpo $c) => [
                'id'         => $c->id,
                'nombre'     => $c->nombre,
                'cedula'     => $c->cedula,
                'correo'     => $c->correo,
                'estado'     => $c->estado,
                'updated_at' => $c->updated_at?->toIso8601String(),
            ])->values(),
        ]);

        return response()->json([
            'data'      => $data,
            'total'     => $paginator->total(),
            'page'      => $paginator->currentPage(),
            'per_page'  => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * POST /api/usuario/visitas
     * Header: Authorization: Bearer {token}
     *
     * Body:
     * {
     *   "fecha_inicio": "2026-03-25T09:00:00",
     *   "fecha_fin":    "2026-03-25T12:00:00",
     *   "visitantes": [
     *     { "nombre": "Juan Pérez", "cedula": "12345678", "correo": "juan@email.com" }
     *   ]
     * }
     *
     * Response 201:
     * {
     *   "message": "Solicitud registrada. Se enviaron los códigos QR.",
     *   "visita": { id, proyecto_id, proyecto, fecha_inicio, fecha_fin, estado, visitantes[] }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fecha_inicio'            => ['required', 'date', 'after_or_equal:now'],
            'fecha_fin'               => ['required', 'date', 'after:fecha_inicio'],
            'visitantes'              => ['required', 'array', 'min:1'],
            'visitantes.*.nombre'     => ['required', 'string', 'max:255'],
            'visitantes.*.cedula'     => ['required', 'string', 'max:100'],
            'visitantes.*.correo'     => ['required', 'email', 'max:255'],
        ]);

        $user = $request->user();

        $cabecera = VisitaCabecera::create([
            'proyecto_id'  => $user->proyecto_id,
            'user_id'      => $user->id,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'],
            'estado'       => 'pendiente',
        ]);

        $cuerpos = [];
        foreach ($data['visitantes'] as $v) {
            $cuerpo = VisitaCuerpo::create([
                'visita_cabecera_id' => $cabecera->id,
                'nombre'             => $v['nombre'],
                'cedula'             => $v['cedula'],
                'correo'             => $v['correo'],
                'estado'             => 'pendiente',
            ]);

            Mail::to($v['correo'])->send(new VisitaQrMail($cuerpo, $cabecera));

            $cuerpos[] = [
                'id'         => $cuerpo->id,
                'nombre'     => $cuerpo->nombre,
                'cedula'     => $cuerpo->cedula,
                'correo'     => $cuerpo->correo,
                'estado'     => $cuerpo->estado,
                'updated_at' => $cuerpo->updated_at?->toIso8601String(),
            ];
        }

        return response()->json([
            'message' => 'Solicitud registrada. Se enviaron los códigos QR.',
            'visita'  => [
                'id'           => $cabecera->id,
                'proyecto_id'  => $cabecera->proyecto_id,
                'proyecto'     => $user->proyecto?->nombre,
                'fecha_inicio' => $cabecera->fecha_inicio->toIso8601String(),
                'fecha_fin'    => $cabecera->fecha_fin->toIso8601String(),
                'estado'       => $cabecera->estado,
                'created_at'   => $cabecera->created_at->toIso8601String(),
                'visitantes'   => $cuerpos,
            ],
        ], 201);
    }
}
