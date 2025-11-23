<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NegocioController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API LARAVEL FUNCIONANDO 🚀'
    ]);
});
//Rutas de autenticación
Route::post('/auth/registro_usuario', [AuthController::class, 'register']);
Route::post('/auth/registro_negocios', [AuthController::class, 'registerBusiness']);
Route::post('/auth/login', [AuthController::class, 'login']);

//Rutas de la información de usuario
//Route::get('/user/userInfomation/{id}', [UsuarioController::class, 'userInformation']);

//esta ruta obtiene la lista de negocios
Route::get('/listas_negocios', [NegocioController::class, 'listaNegocios']);
//esta ruta obtiene los datos de un negocio en específico
Route::get('/negocios/{id}', [NegocioController::class, 'negocioPorId']);