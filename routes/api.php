<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API LARAVEL FUNCIONANDO 🚀'
    ]);
});

Route::post('/auth/registro_usuario', [AuthController::class, 'register']);
Route::post('/auth/registro_negocios', [AuthController::class, 'registerBusiness']);
Route::post('/auth/login', [AuthController::class, 'login']);