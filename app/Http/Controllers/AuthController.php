<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{

    function registerBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreNegocio' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'productos'=> 'nullable|string',
            'direccion' => 'required|string|max:500',
            'metodosPago' => 'required|array',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'telefono' => 'nullable|string|max:20',
            'categoria' => 'nullable|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $business = DB::table('negocios')->insert([
            'nombreNegocio' => $request->nombreNegocio,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'productos' => $request->productos,
            'metodosPago' => json_encode($request->metodosPago),
            'telefono' => $request->telefono,
            'categoria_id' => $request->categoria,
            'foto' => $request->foto,
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
            'email' => 'required|string|email|max:255|unique:negocios,email',
            'password' => 'required|string|min:6',
            'photo' => 'nullable|image|mimes:jpg,jpeg,pgn|max:2048',
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
            'ciudad' => $request->city,
            'municipio' => $request->municipality,
            'departamento' => $request->department,
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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        return response()->json(['message' => 'Inicio de sesión exitoso', 'user' => $user], 200);
    }
}