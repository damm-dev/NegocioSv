<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorito;
use App\Models\Negocio;
use App\Http\Controllers\LogroController;

class FavoritoController extends Controller
{
    protected $logroController;

    public function __construct(LogroController $logroController)
    {
        $this->logroController = $logroController;
    }

    /**
     * Obtener todos los favoritos del usuario autenticado
     */
    public function index()
    {
        $usuario = Auth::user();
        
        $favoritos = Favorito::where('id_usuario', $usuario->id_usuario)
            ->with(['negocio.municipio.departamento', 'negocio.categorias'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $negociosFavoritos = $favoritos->map(function ($favorito) {
            $negocio = $favorito->negocio;
            return [
                'id_favorito' => $favorito->id_favorito,
                'id_negocio' => $negocio->id_negocio,
                'nombre' => $negocio->nombre,
                'descripcion' => $negocio->descripcion,
                'logo_url' => $negocio->logo_url,
                'municipio' => $negocio->municipio->nombre ?? null,
                'departamento' => $negocio->municipio->departamento->nombre ?? null,
                'categorias' => $negocio->categorias->pluck('nombre'),
                'fecha_agregado' => $favorito->created_at
            ];
        });
        
        return response()->json([
            'favoritos' => $negociosFavoritos,
            'total' => $negociosFavoritos->count()
        ]);
    }
    
    /**
     * Agregar un negocio a favoritos
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_negocio' => 'required|exists:negocios,id_negocio'
        ]);
        
        $usuario = Auth::user();
        
        // Verificar si ya existe
        $existe = Favorito::where('id_usuario', $usuario->id_usuario)
            ->where('id_negocio', $request->id_negocio)
            ->exists();
        
        if ($existe) {
            return response()->json([
                'message' => 'Este negocio ya está en tus favoritos'
            ], 400);
        }
        
        // Crear el favorito
        $favorito = Favorito::create([
            'id_usuario' => $usuario->id_usuario,
            'id_negocio' => $request->id_negocio
        ]);
        
        // Actualizar progreso del logro "Cliente Fiel"
        $this->logroController->actualizarProgreso($usuario->id_usuario, 'fiel', 1);
        
        return response()->json([
            'message' => 'Negocio agregado a favoritos',
            'favorito' => $favorito
        ], 201);
    }
    
    /**
     * Eliminar un negocio de favoritos
     */
    public function destroy($idNegocio)
    {
        $usuario = Auth::user();
        
        $favorito = Favorito::where('id_usuario', $usuario->id_usuario)
            ->where('id_negocio', $idNegocio)
            ->first();
        
        if (!$favorito) {
            return response()->json([
                'message' => 'Favorito no encontrado'
            ], 404);
        }
        
        $favorito->delete();
        
        // Recalcular progreso del logro
        $totalFavoritos = Favorito::where('id_usuario', $usuario->id_usuario)->count();
        $this->logroController->actualizarProgreso($usuario->id_usuario, 'fiel', -1);
        
        return response()->json([
            'message' => 'Negocio eliminado de favoritos'
        ]);
    }
    
    /**
     * Verificar si un negocio está en favoritos
     */
    public function verificar($idNegocio)
    {
        $usuario = Auth::user();
        
        $esFavorito = Favorito::where('id_usuario', $usuario->id_usuario)
            ->where('id_negocio', $idNegocio)
            ->exists();
        
        return response()->json([
            'es_favorito' => $esFavorito
        ]);
    }
}
