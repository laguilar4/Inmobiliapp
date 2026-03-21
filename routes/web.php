<?php

use App\Http\Controllers\Admin\ConstructoraController;
use App\Http\Controllers\Admin\ProyectoController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\EmailConfirmationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationNoticeController;
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
    });
});

Route::middleware(['auth', 'confirmed', 'role:seguridad'])->group(function () {
    Route::get('/seguridad', function () {
        return view('seguridad.dashboard');
    })->name('seguridad.dashboard');
});

Route::middleware(['auth', 'confirmed', 'role:usuario'])->group(function () {
    Route::get('/usuario', function () {
        return view('usuario.dashboard');
    })->name('usuario.dashboard');
});
