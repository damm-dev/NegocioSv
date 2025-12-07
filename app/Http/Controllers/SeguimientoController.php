<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seguimiento;
use App\Models\Negocio;
use App\Http\Controllers\LogroController;

class SeguimientoController extends Controller
{
    protected $logroController;

    public function __construct(LogroController $logroController)
    {
        $this->logroController = $logroController;
    }

    /**
     * Obtener todos los negocios que sigue el usuario autenticado
     */
    public function index()
    {
        $usuario = Auth::user();
        
        $seguimientos = Seguimiento::where('id_usuario', $usuario->id_usuario)
            ->with(['negocio.municipio.departamento', 'negocio.categorias'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $negociosSeguidos = $seguimientos->map(function ($seguimiento) {
            $negocio = $seguimiento->negocio;
            return [
                'id_seguimiento' => $seguimiento->id_seguimiento,
                'id_negocio' => $negocio->id_negocio,
                'nombre' => $negocio->nombre,
                'descripcion' => $negocio->descripcion,
                'logo_url' => $negocio->logo_url,
                'municipio' => $negocio->municipio->nombre ?? null,
                'departamento' => $negocio->municipio->departamento->nombre ?? null,
                'categorias' => $negocio->categorias->pluck('nombre'),
                'fecha_seguimiento' => $seguimiento->created_at
            ];
        });
        
        return response()->json([
            'seguimientos' => $negociosSeguidos,
            'total' => $negociosSeguidos->count()
        ]);
    }
    
    /**
     * Seguir un negocio
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_negocio' => 'required|exists:negocios,id_negocio'
        ]);
        
        $usuario = Auth::user();
        
        // Verificar si ya sigue este negocio
        $existe = Seguimiento::where('id_usuario', $usuario->id_usuario)
            ->where('id_negocio', $request->id_negocio)
            ->exists();
        
        if ($existe) {
            return response()->json([
                'message' => 'Ya sigues este negocio'
            ], 400);
        }
        
        // Crear el seguimiento
        $seguimiento = Seguimiento::create([
            'id_usuario' => $usuario->id_usuario,
            'id_negocio' => $request->id_negocio
        ]);
        
        // Actualizar progreso del logro "Social"
        $this->logroController->actualizarProgreso($usuario->id_usuario, 'social', 1);
        
        return response()->json([
            'message' => 'Ahora sigues este negocio',
            'seguimiento' => $seguimiento
        ], 201);
    }
    
    /**
     * Dejar de seguir un negocio
     */
    public function destroy($idNegocio)
    {
        $usuario = Auth::user();
        
        $seguimiento = Seguimiento::where('id_usuario', $usuario->id_usuario)
            ->where('id_negocio', $idNegocio)
            ->first();
        
        if (!$seguimiento) {
            return response()->json([
                'message' => 'No sigues este negocio'
            ], 404);
        }
        
        $seguimiento->delete();
        
        // Recalcular progreso del logro
        $this->logroController->actualizarProgreso($usuario->id_usuario, 'social', -1);
        
        return response()->json([
            'message' => 'Has dejado de seguir este negocio'
        ]);
    }
    
    /**
     * Verificar si el usuario sigue un negocio
     */
    public function verificar($idNegocio)
    {
        $usuario = Auth::user();
        
        $siguiendo = Seguimiento::where('id_usuario', $usuario->id_usuario)
            ->where('id_negocio', $idNegocio)
            ->exists();
        
        return response()->json([
            'siguiendo' => $siguiendo
        ]);
    }
    
    /**
     * Obtener estadísticas de seguidores de un negocio
     */
    public function estadisticasNegocio($idNegocio)
    {
        $totalSeguidores = Seguimiento::where('id_negocio', $idNegocio)->count();
        
        return response()->json([
            'total_seguidores' => $totalSeguidores
        ]);
    }
}
