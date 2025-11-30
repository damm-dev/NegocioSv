<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $usuario = Usuario::where('email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    // Limpiar tokens anteriores (opcional)
    $usuario->tokens()->delete();

    // Crear nuevo token
    $token = $usuario->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login exitoso',
        'token' => $token,
        'usuario' => [
            'id_usuario' => $usuario->id_usuario,
            'email' => $usuario->email
        ]
    ]);
}
}
