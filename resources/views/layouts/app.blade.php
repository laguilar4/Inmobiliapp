<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Bootstrap 5 --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        @stack('styles')
    </head>
    <body class="bg-light min-vh-100 d-flex flex-column">
        <header class="border-bottom bg-white">
            <nav class="navbar navbar-expand-lg navbar-light container">
                <a class="navbar-brand fw-semibold" href="#">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
                    @auth
                        <span class="navbar-text me-3 small text-muted">
                            {{ auth()->user()->name }} ({{ auth()->user()->role }})
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                Cerrar sesión
                            </button>
                        </form>
                    @endauth
                </div>
            </nav>
        </header>

        <main class="flex-grow-1 py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>

     
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
    </html>

