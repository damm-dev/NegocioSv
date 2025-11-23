<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UsuarioController extends Controller
{
    //obtener información del usuario autenticado
    public function userInformation($id) //por el momento recibe el id como parámetro
    {

        $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    return response()->json($user);
    }
}
