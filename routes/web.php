<?php

use App\Http\Controllers\Admin\ConstructoraController;
use App\Http\Controllers\Admin\ProyectoController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/superadmin', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::resource('constructoras', ConstructoraController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::resource('proyectos', ProyectoController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::get('users/create', [UserManagementController::class, 'create'])
            ->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])
            ->name('users.store');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('constructoras', ConstructoraController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::get('users/create', [UserManagementController::class, 'create'])
            ->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])
            ->name('users.store');
    });
});

Route::middleware(['auth', 'role:seguridad'])->group(function () {
    Route::get('/seguridad', function () {
        return view('seguridad.dashboard');
    })->name('seguridad.dashboard');
});

Route::middleware(['auth', 'role:usuario'])->group(function () {
    Route::get('/usuario', function () {
        return view('usuario.dashboard');
    })->name('usuario.dashboard');
});
