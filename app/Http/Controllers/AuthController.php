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

    // Determinar el tipo de usuario (persona o negocio)
    $perfil = $usuario->perfil;
    $negocio = \App\Models\Negocio::where('id_usuario', $usuario->id_usuario)->first();
    
    $userType = 'persona'; // Por defecto
    $userData = [
        'id_usuario' => $usuario->id_usuario,
        'email' => $usuario->email
    ];

    if ($negocio) {
        // Es un negocio
        $userType = 'negocio';
        $userData['negocio'] = [
            'id_negocio' => $negocio->id_negocio,
            'nombre' => $negocio->nombre,
            'descripcion' => $negocio->descripcion,
            'direccion' => $negocio->direccion,
            'telefono' => $negocio->telefono,
            'email_contacto' => $negocio->email_contacto,
            'logo' => $negocio->logo,
            'logo_url' => $negocio->logo_url, // URL completa del logo
            'estado_verificacion' => $negocio->estado_verificacion,
        ];
    } elseif ($perfil) {
        // Es una persona
        $userType = 'persona';
        $userData['perfil'] = [
            'nombres' => $perfil->nombres,
            'apellidos' => $perfil->apellidos,
            'fecha_nacimiento' => $perfil->fecha_nacimiento,
            'genero' => $perfil->genero,
            'telefono' => $perfil->telefono,
            'foto' => $perfil->foto,
            'foto_url' => $perfil->foto_url, // URL completa de la foto
            'id_municipio' => $perfil->id_municipio,
            'descripcion' => $perfil->descripcion,
            'ubicacion_activa' => $perfil->ubicacion_activa,
        ];
    }

    return response()->json([
        'message' => 'Login exitoso',
        'token' => $token,
        'type' => $userType,
        'usuario' => $userData
    ]);
}
public function logout(Request $request)
{
    // Eliminar solo el token actual (logout del dispositivo activo)
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Sesión cerrada correctamente'
    ]);
}
}
