<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Iniciar sesión - {{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg,#f5f7fa,#e4ecf5);
        }

        .login-card{
            border: none;
            border-radius: 12px;
        }

        .login-img{
            max-width:120px;
        }

        .form-control{
            border-radius:8px;
        }

        .btn-login{
            border-radius:8px;
            font-weight:500;
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="row w-100 justify-content-center">

        <div class="col-lg-4 col-md-6 col-sm-10">

            <div class="card shadow-lg login-card p-4">

                <div class="text-center mb-4">

                    <img src="https://cdn-icons-png.flaticon.com/512/5087/5087579.png" class="login-img mb-3" alt="login">

                    <h4 class="fw-bold">
                        Iniciar sesión
                    </h4>

                    <p class="text-muted small">
                        Accede con tu correo y contraseña
                    </p>

                </div>

                @if (session('status'))
                    <div class="alert alert-success small">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-check mb-3">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                        >
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">
                            Entrar
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>