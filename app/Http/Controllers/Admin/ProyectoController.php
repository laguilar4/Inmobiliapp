<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Constructora;
use App\Models\Proyecto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProyectoController extends Controller
{
    public function index(): View
    {
        $proyectos = Proyecto::with('constructora')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.proyectos.index', compact('proyectos'));
    }

    public function create(): View
    {
        $constructoras = Constructora::orderBy('nombre')->get();

        return view('admin.proyectos.create', compact('constructoras'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'numero_torres' => ['required', 'integer', 'min:1'],
            'constructora_id' => ['required', 'exists:constructoras,id'],
        ]);

        Proyecto::create($data);

        return redirect()
            ->route('superadmin.proyectos.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function edit(Proyecto $proyecto): View
    {
        $constructoras = Constructora::orderBy('nombre')->get();

        return view('admin.proyectos.edit', compact('proyecto', 'constructoras'));
    }

    public function update(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'numero_torres' => ['required', 'integer', 'min:1'],
            'constructora_id' => ['required', 'exists:constructoras,id'],
        ]);

        $proyecto->update($data);

        return redirect()
            ->route('superadmin.proyectos.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }
}

