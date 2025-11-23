<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API LARAVEL FUNCIONANDO 🚀'
    ]);
});

Route::post('/test-json', function (Illuminate\Http\Request $request) {
    return response()->json([
        'received_all' => $request->all(),
        'received_input' => $request->input(),
        'received_json' => $request->json()->all(),
        'raw_content' => $request->getContent(),
        'content_type' => $request->header('Content-Type'),
    ]);
});

Route::post('/auth/registro_usuario', [AuthController::class, 'register']);
Route::post('/auth/registro_negocios', [AuthController::class, 'registerBusiness']);
Route::post('/auth/login', [AuthController::class, 'login']);