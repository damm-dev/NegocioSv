<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Negocio;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{

    function registerBusiness(Request $request)
    {
        // Obtener datos del request - usar json() para requests JSON
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        
        $validator = Validator::make($data, [
            'nombreNegocio' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:negocios,email',
            'password' => 'required|string|min:8',
            'productos'=> 'nullable|string',
            'direccion' => 'required|string|max:500',
            'metodosPago' => 'required|array',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono' => 'nullable|string|max:20',
            'categoria' => 'nullable|exists:categorias,id',
            'descripcion' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $business = DB::table('negocios')->insert([
            'nombreNegocio' => $data['nombreNegocio'],
            'email' => $data['email'],
            'direccion' => $data['direccion'],
            'productos' => $data['productos'] ?? null,
            'metodosPago' => json_encode($data['metodosPago']),
            'telefono' => $data['telefono'] ?? null,
            'categoria_id' => $data['categoria'] ?? null,
            'foto' => $data['foto'] ?? null,
            'descripcion' => $data['descripcion'],
            'password' => Hash::make($data['password']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Negocio registrado exitosamente'], 201);
    }
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ciudad' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'notificaciones' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'foto_perfil_path' => $request->photo,
            'ciudad' => $request->ciudad,
            'municipio' => $request->municipio,
            'departamento' => $request->departamento,
            'notificaciones' => $request->notifications ?? false,
        ]);

        return response()->json(['message' => 'Usuario registrado exitosamente', 'user' => $user], 201);
    }

    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        //respuesta para el usuario
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Inicio de sesión existoso',
            'type' => 'user',
            'data' => $user], 200);
        }

        //respuesta para el negocio
        $negocio = Negocio::where('email', $request->email)->first();
        if ($negocio && Hash::check($request->password, $negocio->password)) {
            return response()->json(['message' => 'Inicio de sesión exitoso',
            'type' => 'negocio',
            'data' => $negocio], 200);
        }
        return response()->json(['message' => 'Credenciales inválidas'], 401);

    }
}