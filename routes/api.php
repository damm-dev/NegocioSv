<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ResenasController;

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

Route::get('/negocio/{id}/resenas', [ResenasController::class, 'index']);

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
    // Ver perfil (persona)
    Route::get('/perfil', [PerfilController::class, 'verPerfil']);
    // Ver perfil (negocio)
    Route::get('/perfil/negocio', [PerfilController::class, 'verPerfilNegocio']);
    // Actualizar perfil
    Route::put('/perfil', [PerfilController::class, 'actualizarPerfil']);
    // Cerrar sesión / Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    // Editar negocio
    Route::put('/negocio/{id}', [NegocioController::class, 'actualizarNegocio']);
    // Eliminar negocio
    Route::delete('/negocio/{id}', [NegocioController::class, 'eliminarNegocio']);
    //crear reseña
    Route::post('/negocio/{id}/resena', [ResenasController::class, 'store']);
    //editar reseña
    Route::put('/resena/{id}', [ResenasController::class, 'editar']);
    //eliminar reseña
    Route::delete('/resena/{id}', [ResenasController::class, 'eliminar']);
});

// ==================== RUTAS DE ADMINISTRADOR ====================
use App\Http\Controllers\AdminController;

// Login de administrador (sin autenticación)
Route::post('/admin/login', [AdminController::class, 'login']);

// Rutas protegidas de administrador
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->group(function () {
    // Logout
    Route::post('/logout', [AdminController::class, 'logout']);
    
    // Usuarios
    Route::get('/usuarios', [AdminController::class, 'listarUsuarios']);
    Route::get('/usuarios/{id}', [AdminController::class, 'obtenerUsuario']);
    Route::put('/usuarios/{id}', [AdminController::class, 'actualizarUsuario']);
    Route::delete('/usuarios/{id}', [AdminController::class, 'eliminarUsuario']);
    
    // Perfiles
    Route::get('/perfiles', [AdminController::class, 'listarPerfiles']);
    Route::get('/perfiles/{id}', [AdminController::class, 'obtenerPerfil']);
    Route::put('/perfiles/{id}', [AdminController::class, 'actualizarPerfil']);
    Route::delete('/perfiles/{id}', [AdminController::class, 'eliminarPerfil']);
    
    // Negocios
    Route::get('/negocios', [AdminController::class, 'listarNegocios']);
    Route::get('/negocios/{id}', [AdminController::class, 'obtenerNegocio']);
    Route::post('/negocios', [AdminController::class, 'crearNegocio']);
    Route::put('/negocios/{id}', [AdminController::class, 'actualizarNegocio']);
    Route::delete('/negocios/{id}', [AdminController::class, 'eliminarNegocio']);
    
    // Categorías
    Route::get('/categorias', [AdminController::class, 'listarCategorias']);
    Route::get('/categorias/{id}', [AdminController::class, 'obtenerCategoria']);
    Route::post('/categorias', [AdminController::class, 'crearCategoria']);
    Route::put('/categorias/{id}', [AdminController::class, 'actualizarCategoria']);
    Route::delete('/categorias/{id}', [AdminController::class, 'eliminarCategoria']);
    
    // Métodos de Pago
    Route::get('/metodos-pago', [AdminController::class, 'listarMetodosPago']);
    Route::get('/metodos-pago/{id}', [AdminController::class, 'obtenerMetodoPago']);
    Route::post('/metodos-pago', [AdminController::class, 'crearMetodoPago']);
    Route::put('/metodos-pago/{id}', [AdminController::class, 'actualizarMetodoPago']);
    Route::delete('/metodos-pago/{id}', [AdminController::class, 'eliminarMetodoPago']);
    
    // Departamentos
    Route::get('/departamentos', [AdminController::class, 'listarDepartamentos']);
    Route::get('/departamentos/{id}', [AdminController::class, 'obtenerDepartamento']);
    Route::post('/departamentos', [AdminController::class, 'crearDepartamento']);
    Route::put('/departamentos/{id}', [AdminController::class, 'actualizarDepartamento']);
    Route::delete('/departamentos/{id}', [AdminController::class, 'eliminarDepartamento']);
    
    // Municipios
    Route::get('/municipios', [AdminController::class, 'listarMunicipios']);
    Route::get('/municipios/{id}', [AdminController::class, 'obtenerMunicipio']);
    Route::post('/municipios', [AdminController::class, 'crearMunicipio']);
    Route::put('/municipios/{id}', [AdminController::class, 'actualizarMunicipio']);
    Route::delete('/municipios/{id}', [AdminController::class, 'eliminarMunicipio']);
    
    // Estados de Usuario
    Route::get('/estados-usuario', [AdminController::class, 'listarEstadosUsuario']);
    Route::get('/estados-usuario/{id}', [AdminController::class, 'obtenerEstadoUsuario']);
    Route::post('/estados-usuario', [AdminController::class, 'crearEstadoUsuario']);
    Route::put('/estados-usuario/{id}', [AdminController::class, 'actualizarEstadoUsuario']);
    Route::delete('/estados-usuario/{id}', [AdminController::class, 'eliminarEstadoUsuario']);
    
    // Intereses
    Route::get('/intereses', [AdminController::class, 'listarIntereses']);
    Route::delete('/intereses/{id}', [AdminController::class, 'eliminarInteres']);
    
    // Términos
    Route::get('/terminos', [AdminController::class, 'listarTerminos']);
    Route::get('/terminos/{id}', [AdminController::class, 'obtenerTermino']);
    Route::post('/terminos', [AdminController::class, 'crearTermino']);
    Route::put('/terminos/{id}', [AdminController::class, 'actualizarTermino']);
    Route::delete('/terminos/{id}', [AdminController::class, 'eliminarTermino']);
});
