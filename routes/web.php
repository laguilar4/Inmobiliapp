<?php

use App\Http\Controllers\Admin\ConstructoraController;
use App\Http\Controllers\Admin\ProyectoController;
use App\Http\Controllers\Admin\PublicacionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\EmailConfirmationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationNoticeController;
use App\Http\Controllers\Seguridad\ReporteController;
use App\Http\Controllers\Usuario\VisitaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/confirmar-cuenta/{user}', [EmailConfirmationController::class, 'confirm'])
    ->middleware('signed')
    ->name('account.confirm');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/verificar-correo', [VerificationNoticeController::class, 'show'])
        ->name('verification.notice');
    Route::post('/verificar-correo/reenviar', [VerificationNoticeController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

Route::middleware(['auth', 'confirmed', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::resource('constructoras', ConstructoraController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('proyectos', ProyectoController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('users', UserManagementController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('publicaciones', PublicacionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
            ->parameters(['publicaciones' => 'publicacion']);

        Route::delete('publicaciones/supp-images/{suppImage}', [PublicacionController::class, 'destroySuppImage'])
            ->name('publicaciones.supp_images.destroy');
    });
});

Route::middleware(['auth', 'confirmed', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('constructoras', ConstructoraController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('users', UserManagementController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('publicaciones', PublicacionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
            ->parameters(['publicaciones' => 'publicacion']);

        Route::delete('publicaciones/supp-images/{suppImage}', [PublicacionController::class, 'destroySuppImage'])
            ->name('publicaciones.supp_images.destroy');
    });
});

Route::middleware(['auth', 'confirmed', 'role:seguridad'])->group(function () {
    Route::get('/seguridad', function () {
        return view('seguridad.dashboard');
    })->name('seguridad.dashboard');

    Route::prefix('seguridad')->name('seguridad.')->group(function () {
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/{visitante}', [ReporteController::class, 'show'])->name('reportes.show');
        Route::patch('reportes/{visitante}/estado', [ReporteController::class, 'updateEstado'])->name('reportes.updateEstado');
    });
});

Route::middleware(['auth', 'confirmed', 'role:usuario'])->group(function () {
    Route::get('/usuario', function () {
        return view('usuario.dashboard');
    })->name('usuario.dashboard');

    Route::prefix('usuario')->name('usuario.')->group(function () {
        Route::get('visitas', [VisitaController::class, 'index'])->name('visitas.index');
        Route::get('visitas/crear', [VisitaController::class, 'create'])->name('visitas.create');
        Route::post('visitas', [VisitaController::class, 'store'])->name('visitas.store');
    });
});
