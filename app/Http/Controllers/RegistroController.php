<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Interes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    public function registrar(Request $request)
    {
        $request->validate([
            'email'             => 'required|email|unique:usuarios,email',
            'password'          => 'required|min:6',
            'nombres'           => 'required',
            'apellidos'         => 'required',
            'fecha_nacimiento'  => 'required|date',
            'genero'            => 'required|max:3',
            'telefono'          => 'required',
            'id_municipio'      => 'required|integer',
            'descripcion'       => 'nullable|string',
            'foto'              => 'nullable|string',
            'intereses'         => 'required|array|min:1',
            'intereses.*'       => 'integer'
        ]);

        $usuario = Usuario::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'estado' => true,
            'fecha_creacion' => now()
        ]);

        $perfil = Perfil::create([
            'id_usuario' => $usuario->id_usuario,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'foto' => $request->foto,
            'id_municipio' => $request->id_municipio,
            'descripcion' => $request->descripcion,
        ]);

        foreach ($request->intereses as $cat) {
            Interes::create([
                'id_usuario' => $usuario->id_usuario,
                'id_categoria' => $cat
            ]);
        }

        return response()->json([
            'usuario' => $usuario,
            'perfil' => $perfil
        ], 201);
    }
}
