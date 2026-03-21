<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * POST /api/login
     *
     * Body: { "email": "...", "password": "..." }
     *
     * Response 200:
     * {
     *   "token": "...",
     *   "user": { "id", "name", "nombres", "apellidos", "email", "role", "proyecto_id", "cedula" }
     * }
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Las credenciales no son válidas.',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasRole('superadmin') && ! $user->hasConfirmedEmail()) {
            Auth::logout();
            return response()->json([
                'message' => 'Debes confirmar tu correo electrónico antes de iniciar sesión.',
            ], 403);
        }

        // Revoca tokens anteriores del mismo dispositivo si se envía device_name
        $deviceName = $request->input('device_name', 'mobile');
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'          => $user->id,
                'name'        => $user->name,
                'nombres'     => $user->nombres,
                'apellidos'   => $user->apellidos,
                'email'       => $user->email,
                'role'        => $user->role,
                'proyecto_id' => $user->proyecto_id,
                'cedula'      => $user->cedula,
            ],
        ]);
    }

    /**
     * POST /api/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /**
     * GET /api/me
     * Header: Authorization: Bearer {token}
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'                  => $user->id,
            'name'                => $user->name,
            'nombres'             => $user->nombres,
            'apellidos'           => $user->apellidos,
            'email'               => $user->email,
            'role'                => $user->role,
            'proyecto_id'         => $user->proyecto_id,
            'proyecto'            => $user->proyecto?->nombre,
            'cedula'              => $user->cedula,
            'telefono'            => $user->telefono,
            'numero_torre'        => $user->numero_torre,
            'numero_apartamento'  => $user->numero_apartamento,
        ]);
    }
}
