<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use App\Models\Departamento;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    /**
     * Obtener todos los municipios
     */
    public function index()
    {
        try {
            $municipios = Municipio::with('departamento')
                ->orderBy('nombre')
                ->get()
                ->map(function ($municipio) {
                    return [
                        'id' => $municipio->id_municipio,
                        'nombre' => $municipio->nombre,
                        'departamento' => $municipio->departamento->nombre ?? 'N/A',
                        'id_departamento' => $municipio->id_departamento
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $municipios
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener municipios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener municipios por departamento
     */
    public function porDepartamento($idDepartamento)
    {
        try {
            $municipios = Municipio::where('id_departamento', $idDepartamento)
                ->orderBy('nombre')
                ->get()
                ->map(function ($municipio) {
                    return [
                        'id' => $municipio->id_municipio,
                        'nombre' => $municipio->nombre
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $municipios
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener municipios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los departamentos
     */
    public function departamentos()
    {
        try {
            $departamentos = Departamento::orderBy('nombre')
                ->get()
                ->map(function ($departamento) {
                    return [
                        'id' => $departamento->id_departamento,
                        'nombre' => $departamento->nombre
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $departamentos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener departamentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
