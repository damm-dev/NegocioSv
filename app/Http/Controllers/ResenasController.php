<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResenasController extends Controller
{
    /**
     * Crear una reseña para un negocio.
     * Ruta: POST /api/negocio/{id}/resena
     * {id} = id_negocio
     */
    public function index($id)
{
    $resenas = Resena::with(['usuario.perfil'])
        ->where('id_negocio', $id)
        ->orderByDesc('created_at')
        ->get();

    return response()->json($resenas);
}
    public function store(Request $request, $id)
    {
        // Validación de los campos que vienen en el body
        $request->validate([
            'comentario'   => 'required|string|min:10',
            'calificacion' => 'required|integer|min:1|max:5',
        ]);

        // Verificar si este usuario ya hizo una reseña para este negocio
        $existe = Resena::where('id_negocio', $id)
                        ->where('id_usuario', Auth::id())
                        ->exists();

        if ($existe) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ya has dejado una reseña para este negocio.',
            ], 422);
        }

        // Crear la reseña
        $resena = Resena::create([
            'id_negocio'   => $id,               // viene de la URL
            'id_usuario'   => Auth::id(),        // usuario autenticado
            'comentario'   => $request->comentario,
            'calificacion' => $request->calificacion,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Reseña creada correctamente.',
            'data'    => $resena,
        ], 201);
    }

    /**
     * Editar una reseña.
     * Ruta: PUT /api/resena/{id}
     * {id} = id_resena
     */
    public function editar(Request $request, $id)
    {
        $resena = Resena::find($id);

        if (!$resena) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Reseña no encontrada.',
            ], 404);
        }

        // Verificar que la reseña sea del usuario autenticado
        if ($resena->id_usuario !== Auth::id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No tienes permiso para editar esta reseña.',
            ], 403);
        }

        // Validar nuevos datos
        $request->validate([
            'comentario'   => 'required|string|min:10',
            'calificacion' => 'required|integer|min:1|max:5',
        ]);

        $resena->update([
            'comentario'   => $request->comentario,
            'calificacion' => $request->calificacion,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Reseña actualizada correctamente.',
            'data'    => $resena,
        ]);
    }

    /**
     * Eliminar una reseña.
     * Ruta: DELETE /api/resena/{id}
     * {id} = id_resena
     */
    public function eliminar($id)
    {
        $resena = Resena::find($id);

        if (!$resena) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Reseña no encontrada.',
            ], 404);
        }

        // Verificar que la reseña sea del usuario autenticado
        if ($resena->id_usuario !== Auth::id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No tienes permiso para eliminar esta reseña.',
            ], 403);
        }

        try {
            $resena->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Reseña eliminada correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error al eliminar la reseña.',
            ], 500);
        }
    }
}
