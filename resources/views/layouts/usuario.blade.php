@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="text-uppercase text-muted small mb-3">Menú</h6>
                    <nav class="nav nav-pills flex-column gap-1">
                        <a href="{{ route('usuario.dashboard') }}"
                           class="nav-link {{ request()->routeIs('usuario.dashboard') ? 'active' : 'text-body' }}">
                            Panel Usuario
                        </a>
                        <a href="{{ route('usuario.visitas.index') }}"
                           class="nav-link {{ request()->routeIs('usuario.visitas.*') ? 'active' : 'text-body' }}">
                            Mis Visitas
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10">
            @yield('usuario-content')
        </div>
    </div>
@endsection
