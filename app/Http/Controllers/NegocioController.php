<?php

namespace App\Http\Controllers;
use App\Models\Negocio;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    //obtener lista de negocios
    public function listaNegocios()
    {
        //traemos los neogocios con su categoria.
        $negocios = Negocio::with('categoria:id,nombre')
            ->select('id', 'nombreNegocio', 'email', 'descripcion', 'telefono', 'foto', 'categoria_id')
            ->get();

        return response()->json($negocios);
    }

    //obtener negocio por id
    public function negocioPorId($id){
        $negocio = Negocio::with('categoria:id,nombre')
            ->select('id', 'nombreNegocio', 'email', 'direccion', 'descripcion', 'telefono', 'foto', 'categoria_id')
            ->where('id', $id)
            ->first();

        if (!$negocio) {
            return response()->json(['message' => 'Negocio no encontrado'], 404);
    }

            return response()->json($negocio);
    }
}
