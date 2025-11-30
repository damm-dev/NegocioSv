<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negocio;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class NegocioController extends Controller
{
    public function registrarNegocio(Request $request)
    {
    // 1. Validaciones
        $validator = Validator::make($request->all(), [
            // Datos Usuario
            'email'             => 'required|email|unique:usuarios,email',
            'password'          => 'required|min:8',
            
            // Datos Negocio
            'nombre_negocio'    => 'required|string|max:100',
            'id_categoria'      => 'required|array', // Array de IDs (Select múltiple)
            'id_categoria.*'    => 'exists:categorias,id_categoria',
            'descripcion'       => 'required|string',
            'direccion'         => 'required|string',
            'id_municipio'      => 'required|exists:municipios,id_municipio', // Select municipio
            'logo'              => 'nullable|image|max:2048',
            'email_contacto'    => 'required|email',
            'telefono'          => 'required|string',
            'metodos_pago'      => 'nullable|array', // Array de IDs
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Transacción (Todo o Nada)
        DB::beginTransaction();

        try {
            // Crear el USUARIO
            $usuario = Usuario::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // B. Subir el LOGO si existe
            $rutaLogo = null;
            if ($request->hasFile('logo')) {
                $rutaLogo = $request->file('logo')->store('logos', 'public');
            }

            // Crear el NEGOCIO
            $negocio = Negocio::create([
                'id_usuario'      => $usuario->id_usuario, // Usamos el ID del usuario recién creado
                'id_municipio'    => $request->id_municipio,
                'nombre'          => $request->nombre_negocio,
                'descripcion'     => $request->descripcion,
                'direccion'       => $request->direccion,
                'telefono'        => $request->telefono,
                'email_contacto'  => $request->email_contacto,
                'logo'            => $rutaLogo,
                'estado_verificacion' => false, // Por defecto no verificado
            ]);

            // Guardar Categorías
            // attach() guarda en la tabla intermedia negocio_categoria
            $negocio->categorias()->attach($request->id_categoria);

            // Guardar Métodos de Pago
            if ($request->has('metodos_pago')) {
                $negocio->metodosPago()->attach($request->metodos_pago);
            }

            // Confirmar todo
            DB::commit();

            // Asegúrate que tu modelo Usuario use el trait 'HasApiTokens'
            $token = $usuario->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Negocio registrado correctamente',
                'token' => $token,
                'negocio' => $negocio
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
