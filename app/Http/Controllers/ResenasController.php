<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resena;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ResenasController extends Controller
{
    public function store(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'id_negocio'   => 'required|exists:negocios,id_negocio',
            'comentario'   => 'required|string|min:10|max:500', // Mínimo 10 letras
            'calificacion' => 'required|integer|min:1|max:5',    // Estrellas 1-5
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //Verificar si el usuario ya comentó este negocio
        $existe = Resena::where('id_negocio', $request->id_negocio)
                        ->where('id_usuario', Auth::id())
                        ->exists();

        if ($existe) {
            return response()->json([
                'status' => false,
                'message' => 'Ya has publicado una reseña para este negocio.'
            ], 409); // 409 Conflict
        }

        //Crear la reseña
        try {
            $resena = Resena::create([
                'id_negocio'   => $request->id_negocio,
                'id_usuario'   => Auth::id(), // Tomamos el ID del token del usuario logueado
                'comentario'   => $request->comentario,
                'calificacion' => $request->calificacion,
            ]);

            return response()->json([
                'status' => true,
                'message' => '¡Reseña publicada con éxito!',
                'data' => $resena
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
