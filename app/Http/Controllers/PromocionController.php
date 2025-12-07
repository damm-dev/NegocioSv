<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Promocion;
use App\Models\Negocio;
use Carbon\Carbon;

class PromocionController extends Controller
{
    /**
     * Obtener todas las promociones de un negocio
     */
    public function index($idNegocio)
    {
        $promociones = Promocion::where('id_negocio', $idNegocio)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        
        return response()->json([
            'promociones' => $promociones
        ]);
    }
    
    /**
     * Obtener solo las promociones vigentes de un negocio
     */
    public function vigentes($idNegocio)
    {
        $promociones = Promocion::where('id_negocio', $idNegocio)
            ->vigentes()
            ->get();
        
        return response()->json([
            'promociones' => $promociones,
            'total' => $promociones->count()
        ]);
    }
    
    /**
     * Obtener promociones del negocio del usuario autenticado
     */
    public function misPromociones()
    {
        $usuario = Auth::user();
        
        // Buscar el negocio del usuario
        $negocio = Negocio::where('id_usuario', $usuario->id_usuario)->first();
        
        if (!$negocio) {
            return response()->json([
                'message' => 'No tienes un negocio registrado'
            ], 404);
        }
        
        $promociones = Promocion::where('id_negocio', $negocio->id_negocio)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'promociones' => $promociones,
            'total' => $promociones->count()
        ]);
    }
    
    /**
     * Crear una nueva promoción
     */
    public function store(Request $request)
    {
        $usuario = Auth::user();
        
        // Verificar que el usuario tenga un negocio
        $negocio = Negocio::where('id_usuario', $usuario->id_usuario)->first();
        
        if (!$negocio) {
            return response()->json([
                'message' => 'No tienes un negocio registrado'
            ], 404);
        }
        
        $request->validate([
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'descuento_porcentaje' => 'nullable|integer|min:1|max:100',
            'codigo_promocional' => 'nullable|string|max:50',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activa' => 'boolean'
        ]);
        
        $promocion = Promocion::create([
            'id_negocio' => $negocio->id_negocio,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'descuento_porcentaje' => $request->descuento_porcentaje,
            'codigo_promocional' => $request->codigo_promocional,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'activa' => $request->activa ?? true
        ]);
        
        return response()->json([
            'message' => 'Promoción creada exitosamente',
            'promocion' => $promocion
        ], 201);
    }
    
    /**
     * Actualizar una promoción
     */
    public function update(Request $request, $id)
    {
        $usuario = Auth::user();
        
        $promocion = Promocion::find($id);
        
        if (!$promocion) {
            return response()->json([
                'message' => 'Promoción no encontrada'
            ], 404);
        }
        
        // Verificar que la promoción pertenezca al negocio del usuario
        $negocio = Negocio::where('id_usuario', $usuario->id_usuario)->first();
        
        if (!$negocio || $promocion->id_negocio !== $negocio->id_negocio) {
            return response()->json([
                'message' => 'No tienes permiso para editar esta promoción'
            ], 403);
        }
        
        $request->validate([
            'titulo' => 'string|max:100',
            'descripcion' => 'string',
            'descuento_porcentaje' => 'nullable|integer|min:1|max:100',
            'codigo_promocional' => 'nullable|string|max:50',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date|after_or_equal:fecha_inicio',
            'activa' => 'boolean'
        ]);
        
        $promocion->update($request->only([
            'titulo',
            'descripcion',
            'descuento_porcentaje',
            'codigo_promocional',
            'fecha_inicio',
            'fecha_fin',
            'activa'
        ]));
        
        return response()->json([
            'message' => 'Promoción actualizada exitosamente',
            'promocion' => $promocion
        ]);
    }
    
    /**
     * Eliminar una promoción
     */
    public function destroy($id)
    {
        $usuario = Auth::user();
        
        $promocion = Promocion::find($id);
        
        if (!$promocion) {
            return response()->json([
                'message' => 'Promoción no encontrada'
            ], 404);
        }
        
        // Verificar que la promoción pertenezca al negocio del usuario
        $negocio = Negocio::where('id_usuario', $usuario->id_usuario)->first();
        
        if (!$negocio || $promocion->id_negocio !== $negocio->id_negocio) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta promoción'
            ], 403);
        }
        
        $promocion->delete();
        
        return response()->json([
            'message' => 'Promoción eliminada exitosamente'
        ]);
    }
    
    /**
     * Activar/Desactivar una promoción
     */
    public function toggleActiva($id)
    {
        $usuario = Auth::user();
        
        $promocion = Promocion::find($id);
        
        if (!$promocion) {
            return response()->json([
                'message' => 'Promoción no encontrada'
            ], 404);
        }
        
        // Verificar que la promoción pertenezca al negocio del usuario
        $negocio = Negocio::where('id_usuario', $usuario->id_usuario)->first();
        
        if (!$negocio || $promocion->id_negocio !== $negocio->id_negocio) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta promoción'
            ], 403);
        }
        
        $promocion->activa = !$promocion->activa;
        $promocion->save();
        
        return response()->json([
            'message' => $promocion->activa ? 'Promoción activada' : 'Promoción desactivada',
            'promocion' => $promocion
        ]);
    }
}
