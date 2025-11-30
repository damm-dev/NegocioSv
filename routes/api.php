<?php

use App\Http\Controllers\RegistroController;


use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API LARAVEL FUNCIONANDO 🚀'
    ]);
});


Route::post('/registrar', [RegistroController::class, 'registrar']); 