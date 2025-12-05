<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\MunicipioController;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API LARAVEL FUNCIONANDO 🚀'
    ]);
});

Route::post('/registrar', [RegistroController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registrar_negocio', [NegocioController::class, 'registrarNegocio']);

Route::get('/negocios', [NegocioController::class, 'listarNegocios']);
Route::get('/negocio/{id}', [NegocioController::class, 'detalleNegocio']);

// Rutas para municipios y departamentos
Route::get('/municipios', [MunicipioController::class, 'index']);
Route::get('/departamentos', [MunicipioController::class, 'departamentos']);
Route::get('/municipios/departamento/{id}', [MunicipioController::class, 'porDepartamento']);

// Rutas para obtener categorías y métodos de pago
Route::get('/categorias', function() {
    try {
        $categorias = \App\Models\Categoria::select('id_categoria as id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener categorías: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/metodos-pago', function() {
    try {
        $metodos = \App\Models\MetodoPago::select('id_metodo_pago as id', 'nombre')
            ->orderBy('nombre', 'asc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $metodos
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener métodos de pago: ' . $e->getMessage()
        ], 500);
    }
});

Route::middleware('auth:sanctum')->group(function () {
    // Ver perfil
    Route::get('/perfil', [PerfilController::class, 'verPerfil']);
    // Actualizar perfil
    Route::put('/perfil', [PerfilController::class, 'actualizarPerfil']);
    // Cerrar sesión / Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Editar negocio
    Route::put('/negocio/{id}', [NegocioController::class, 'actualizarNegocio']);
    // Eliminar negocio
    Route::delete('/negocio/{id}', [NegocioController::class, 'eliminarNegocio']);
});
