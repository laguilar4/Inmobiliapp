@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h5 fw-semibold mb-3">Confirma tu correo</h1>

                    <p class="text-muted small mb-3">
                        Hemos enviado un enlace de confirmación a <strong>{{ auth()->user()->email }}</strong>.
                        Revisa tu bandeja de entrada (y spam) y haz clic en el enlace para activar tu cuenta.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success small mb-3">{{ session('status') }}</div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning small mb-3">{{ session('warning') }}</div>
                    @endif

                    <form method="POST" action="{{ route('verification.resend') }}" class="mb-3">
                        @csrf
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            Reenviar enlace de confirmación
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm p-0 text-muted">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
