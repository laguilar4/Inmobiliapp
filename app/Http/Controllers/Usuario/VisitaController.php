<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Mail\VisitaQrMail;
use App\Models\VisitaCabecera;
use App\Models\VisitaCuerpo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VisitaController extends Controller
{
    public function index(): View
    {
        $visitas = VisitaCabecera::with(['proyecto', 'cuerpos'])
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(15);

        return view('usuario.visitas.index', compact('visitas'));
    }

    public function create(): View
    {
        return view('usuario.visitas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fecha_inicio'            => ['required', 'date', 'after_or_equal:now'],
            'fecha_fin'               => ['required', 'date', 'after:fecha_inicio'],
            'visitantes'              => ['required', 'array', 'min:1'],
            'visitantes.*.nombre'     => ['required', 'string', 'max:255'],
            'visitantes.*.cedula'     => ['required', 'string', 'max:100'],
            'visitantes.*.correo'     => ['required', 'email', 'max:255'],
        ]);

        $user = auth()->user();

        $cabecera = VisitaCabecera::create([
            'proyecto_id'  => $user->proyecto_id,
            'user_id'      => $user->id,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'],
            'estado'       => 'pendiente',
        ]);

        foreach ($data['visitantes'] as $v) {
            $cuerpo = VisitaCuerpo::create([
                'visita_cabecera_id' => $cabecera->id,
                'nombre'             => $v['nombre'],
                'cedula'             => $v['cedula'],
                'correo'             => $v['correo'],
                'estado'             => 'pendiente',
            ]);

            Mail::to($v['correo'])->send(new VisitaQrMail($cuerpo, $cabecera));
        }

        return redirect()
            ->route('usuario.visitas.index')
            ->with('success', 'Solicitud de visita registrada. Se enviaron los códigos QR por correo.');
    }
}
