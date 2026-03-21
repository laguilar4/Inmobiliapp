<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\VisitaController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────
// Públicas (no requieren token)
// ──────────────────────────────────────────
Route::post('login', [AuthController::class, 'login']);

// ──────────────────────────────────────────
// Protegidas con Sanctum
// ──────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Rol: usuario
    Route::middleware('api.role:usuario')->prefix('usuario')->group(function () {
        Route::get('visitas', [VisitaController::class, 'index']);
        Route::post('visitas', [VisitaController::class, 'store']);
    });

    // Rol: seguridad
    Route::middleware('api.role:seguridad')->prefix('seguridad')->group(function () {
        Route::get('reportes', [ReporteController::class, 'index']);
        Route::get('reportes/{visitante}', [ReporteController::class, 'show']);
        Route::patch('reportes/{visitante}/estado', [ReporteController::class, 'updateEstado']);
    });
});
