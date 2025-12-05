<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\NegocioController;

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
Route::get('/municipios', [NegocioController::class, 'listarMunicipios']);//mostrar lista de municipios

Route::middleware('auth:sanctum')->group(function () {

    // Ver perfil
    Route::get('/perfil', [PerfilController::class, 'verPerfil']);

    // Actualizar perfil
    Route::put('/perfil', [PerfilController::class, 'actualizarPerfil']);

    // Cerrar sesión / Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    
    // Editar negocio
    Route::put('/negocio/{id}', [NegocioController::class, 'actualizarNegocio']);

    //eliminar negocio
    Route::delete('/negocio/{id}', [NegocioController::class, 'eliminarNegocio']);
});
