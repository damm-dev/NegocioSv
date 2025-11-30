<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Interes;

use Illuminate\Support\Facades\Auth;


class PerfilController extends Controller
{
   
    public function verPerfil(Request $request)
    {
        $usuario = Auth::user(); // Usuario autenticado

        $usuario = Usuario::with([
            'perfil.municipio.departamento',
            'intereses.categoria'
        ])->find($usuario->id_usuario);

        if (!$usuario) {
            return response()->json([
                'message' => 'Perfil no encontrado'
            ], 404);
        }

        return response()->json([
            'usuario' => [
                'id' => $usuario->id_usuario,
                'email' => $usuario->email,
                'estado' => $usuario->estado->nombre ?? null,
            ],
            'perfil' => $usuario->perfil,
            'municipio' => $usuario->perfil->municipio->nombre ?? null,
            'departamento' => $usuario->perfil->municipio->departamento->nombre ?? null,
            'intereses' => $usuario->intereses->pluck('categoria.nombre'),
        ], 200);
    }

    //  2. ACTUALIZAR PERFIL
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();; // Usuario autenticado

        $perfil = Perfil::where('id_usuario', $usuario->id_usuario)->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $request->validate([
            'nombres' => 'nullable|string|min:2',
            'apellidos' => 'nullable|string|min:2',
            'telefono' => 'nullable|string',
            'foto' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'id_municipio' => 'nullable|integer|exists:municipios,id_municipio',
            'intereses' => 'nullable|array',
            'intereses.*' => 'integer|exists:categorias,id_categoria',
        ]);

        $perfil->update($request->only([
            'nombres',
            'apellidos',
            'telefono',
            'descripcion',
            'foto',
            'id_municipio'
        ]));

        // Actualizar intereses si vienen
        if ($request->has('intereses')) {
            Interes::where('id_usuario', $usuario->id_usuario)->delete();

            foreach ($request->intereses as $cat) {
                Interes::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_categoria' => $cat
                ]);
            }
        }

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'perfil' => $perfil
        ], 200);
    }

    //  3. ELIMINAR PERFIL
    public function eliminarPerfil(Request $request)
    {
        $usuario = Auth::user();; // Usuario autenticado

        $perfil = Perfil::where('id_usuario', $usuario->id_usuario)->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $perfil->delete();
        $usuario->delete();

        return response()->json(['message' => 'Usuario y perfil eliminados correctamente']);
    }
}
