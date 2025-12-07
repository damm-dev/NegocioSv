<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Logro;
use App\Models\UsuarioLogro;
use App\Models\Usuario;

class LogroController extends Controller
{
    /**
     * Obtener todos los logros con el progreso del usuario autenticado
     */
    public function index()
    {
        $usuario = Auth::user();
        
        // Obtener todos los logros
        $logros = Logro::all();
        
        // Obtener el progreso del usuario para cada logro
        $logrosConProgreso = $logros->map(function ($logro) use ($usuario) {
            $progreso = UsuarioLogro::where('id_usuario', $usuario->id_usuario)
                ->where('id_logro', $logro->id_logro)
                ->first();
            
            return [
                'id' => $logro->id_logro,
                'nombre' => $logro->nombre,
                'descripcion' => $logro->descripcion,
                'icono' => $logro->icono,
                'tipo' => $logro->tipo,
                'meta' => $logro->meta,
                'progreso' => $progreso ? $progreso->progreso : 0,
                'completado' => $progreso ? $progreso->completado : false,
                'fecha_completado' => $progreso ? $progreso->fecha_completado : null,
            ];
        });
        
        // Calcular estadísticas
        $completados = $logrosConProgreso->where('completado', true)->count();
        $total = $logrosConProgreso->count();
        $porcentaje = $total > 0 ? round(($completados / $total) * 100) : 0;
        
        return response()->json([
            'logros' => $logrosConProgreso,
            'estadisticas' => [
                'completados' => $completados,
                'total' => $total,
                'porcentaje' => $porcentaje
            ]
        ]);
    }
    
    /**
     * Actualizar el progreso de un logro específico
     * Este método se llama internamente cuando el usuario realiza acciones
     */
    public function actualizarProgreso($idUsuario, $tipoLogro, $incremento = 1)
    {
        // Buscar el logro por tipo
        $logro = Logro::where('tipo', $tipoLogro)->first();
        
        if (!$logro) {
            return false;
        }
        
        // Buscar o crear el registro de progreso
        $usuarioLogro = UsuarioLogro::firstOrCreate(
            [
                'id_usuario' => $idUsuario,
                'id_logro' => $logro->id_logro
            ],
            [
                'progreso' => 0,
                'completado' => false
            ]
        );
        
        // Si ya está completado, no hacer nada
        if ($usuarioLogro->completado) {
            return true;
        }
        
        // Incrementar el progreso
        $usuarioLogro->progreso += $incremento;
        
        // Verificar si se completó el logro
        if ($usuarioLogro->progreso >= $logro->meta) {
            $usuarioLogro->progreso = $logro->meta;
            $usuarioLogro->completado = true;
            $usuarioLogro->fecha_completado = now();
        }
        
        $usuarioLogro->save();
        
        return true;
    }
    
    /**
     * Verificar y actualizar logros basados en las acciones del usuario
     */
    public function verificarLogros(Request $request)
    {
        $usuario = Auth::user();
        
        // Contar favoritos
        $totalFavoritos = $usuario->favoritos()->count();
        $this->actualizarProgresoDirecto($usuario->id_usuario, 'fiel', $totalFavoritos);
        
        // Contar seguimientos
        $totalSeguimientos = $usuario->seguimientos()->count();
        $this->actualizarProgresoDirecto($usuario->id_usuario, 'social', $totalSeguimientos);
        
        // Contar reseñas
        $totalResenas = $usuario->resenas()->count();
        $this->actualizarProgresoDirecto($usuario->id_usuario, 'critico', $totalResenas);
        
        // Contar recomendaciones
        $totalRecomendaciones = $usuario->recomendaciones()->count();
        $this->actualizarProgresoDirecto($usuario->id_usuario, 'apoyo_local', $totalRecomendaciones);
        
        return response()->json([
            'message' => 'Logros verificados correctamente'
        ]);
    }
    
    /**
     * Actualizar progreso con un valor absoluto (no incremental)
     */
    private function actualizarProgresoDirecto($idUsuario, $tipoLogro, $valorAbsoluto)
    {
        $logro = Logro::where('tipo', $tipoLogro)->first();
        
        if (!$logro) {
            return false;
        }
        
        $usuarioLogro = UsuarioLogro::firstOrCreate(
            [
                'id_usuario' => $idUsuario,
                'id_logro' => $logro->id_logro
            ],
            [
                'progreso' => 0,
                'completado' => false
            ]
        );
        
        // Actualizar con el valor absoluto
        $usuarioLogro->progreso = $valorAbsoluto;
        
        // Verificar si se completó
        if ($usuarioLogro->progreso >= $logro->meta && !$usuarioLogro->completado) {
            $usuarioLogro->progreso = $logro->meta;
            $usuarioLogro->completado = true;
            $usuarioLogro->fecha_completado = now();
        }
        
        $usuarioLogro->save();
        
        return true;
    }
}
