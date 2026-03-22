@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h6 class="text-uppercase text-muted small mb-3">Menú</h6>
                    <nav class="nav nav-pills flex-column gap-1">
                        @if(auth()->user()->role === 'superadmin')
                            <a href="{{ route('superadmin.dashboard') }}"
                               class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : 'text-body' }}">
                                Panel Superadmin
                            </a>

                            <a href="{{ route('superadmin.constructoras.index') }}"
                               class="nav-link {{ request()->routeIs('superadmin.constructoras.*') ? 'active' : 'text-body' }}">
                                Constructoras
                            </a>

                            <a href="{{ route('superadmin.proyectos.index') }}"
                               class="nav-link {{ request()->routeIs('superadmin.proyectos.*') ? 'active' : 'text-body' }}">
                                Proyectos
                            </a>

                            <a href="{{ route('superadmin.users.index') }}"
                               class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : 'text-body' }}">
                                Usuarios
                            </a>

                            <a href="{{ route('superadmin.publicaciones.index') }}"
                               class="nav-link {{ request()->routeIs('superadmin.publicaciones.*') ? 'active' : 'text-body' }}">
                                Publicaciones
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-body' }}">
                                Panel Admin
                            </a>

                            <a href="{{ route('admin.constructoras.index') }}"
                               class="nav-link {{ request()->routeIs('admin.constructoras.*') ? 'active' : 'text-body' }}">
                                Constructoras
                            </a>

                            <a href="{{ route('admin.users.index') }}"
                               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : 'text-body' }}">
                                Usuarios
                            </a>

                            <a href="{{ route('admin.publicaciones.index') }}"
                               class="nav-link {{ request()->routeIs('admin.publicaciones.*') ? 'active' : 'text-body' }}">
                                Publicaciones
                            </a>
                        @endif
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10">
            @yield('admin-content')
        </div>
    </div>
@endsection

