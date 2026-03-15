<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Constructora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConstructoraController extends Controller
{
    public function index(): View
    {
        $constructoras = Constructora::orderByDesc('created_at')->paginate(10);

        return view('admin.constructoras.index', compact('constructoras'));
    }

    public function create(): View
    {
        return view('admin.constructoras.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:100', 'unique:constructoras,nit'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'representante_legal' => ['nullable', 'string', 'max:255'],
            'fecha_creacion' => ['nullable', 'date'],
            'estado' => ['required', 'string', 'max:50'],
        ]);

        Constructora::create($data);

        return redirect()
            ->route(auth()->user()->role === 'superadmin' ? 'superadmin.constructoras.index' : 'admin.constructoras.index')
            ->with('success', 'Constructora creada correctamente.');
    }

    public function edit(Constructora $constructora): View
    {
        return view('admin.constructoras.edit', compact('constructora'));
    }

    public function update(Request $request, Constructora $constructora): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:100', 'unique:constructoras,nit,' . $constructora->id],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'representante_legal' => ['nullable', 'string', 'max:255'],
            'fecha_creacion' => ['nullable', 'date'],
            'estado' => ['required', 'string', 'max:50'],
        ]);

        $constructora->update($data);

        return redirect()
            ->route(auth()->user()->role === 'superadmin' ? 'superadmin.constructoras.index' : 'admin.constructoras.index')
            ->with('success', 'Constructora actualizada correctamente.');
    }
}

