<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\VisitaCuerpo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(): View
    {
        $visitantes = VisitaCuerpo::with(['cabecera.proyecto', 'cabecera.usuario'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('seguridad.reportes.index', compact('visitantes'));
    }

    public function show(VisitaCuerpo $visitante): View
    {
        $visitante->load(['cabecera.proyecto', 'cabecera.usuario', 'cabecera.cuerpos']);

        return view('seguridad.reportes.show', compact('visitante'));
    }

    public function updateEstado(Request $request, VisitaCuerpo $visitante): RedirectResponse
    {
        $data = $request->validate([
            'estado' => ['required', 'in:pendiente,entro,salio'],
        ]);

        $visitante->estado     = $data['estado'];
        $visitante->updated_at = now();
        $visitante->save();

        return redirect()
            ->route('seguridad.reportes.show', $visitante)
            ->with('success', 'Estado actualizado correctamente.');
    }
}
