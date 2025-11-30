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
        // 1. Validaciones mejoradas
        $validator = Validator::make($request->all(), [
            // Datos Usuario
            'email'             => 'required|email|unique:usuarios,email',
            'password'          => [
                'required',
                'min:8',
                'regex:/[a-zA-Z]/',      // Al menos una letra
                'regex:/[0-9]/',         // Al menos un número
            ],
            
            // Datos Negocio
            'nombre_negocio'    => 'required|string|min:3|max:100',
            'id_categoria'      => 'required|array|min:1|max:3', // Entre 1 y 3 categorías
            'id_categoria.*'    => 'exists:categorias,id_categoria',
            'descripcion'       => 'required|string|min:20|max:500',
            'direccion'         => 'required|string|min:10',
            'id_municipio'      => 'required|exists:municipios,id_municipio',
            'logo'              => 'nullable|image|max:2048',
            'email_contacto'    => 'required|email',
            'telefono'          => [
                'required',
                'regex:/^\d{4}-\d{4}$/', // Formato ####-####
                'regex:/^[267]/',        // Debe empezar con 2, 6 o 7
            ],
            'metodos_pago'      => 'nullable|array',
            'metodos_pago.*'    => 'exists:metodos_pago,id_metodo_pago',
        ], [
            // Mensajes personalizados en español
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener al menos una letra y un número',
            'nombre_negocio.required' => 'El nombre del negocio es obligatorio',
            'nombre_negocio.min' => 'El nombre del negocio debe tener al menos 3 caracteres',
            'nombre_negocio.max' => 'El nombre del negocio no puede exceder 100 caracteres',
            'id_categoria.required' => 'Debes seleccionar al menos una categoría',
            'id_categoria.min' => 'Debes seleccionar al menos una categoría',
            'id_categoria.max' => 'Puedes seleccionar máximo 3 categorías',
            'id_categoria.*.exists' => 'Una o más categorías seleccionadas no son válidas',
            'descripcion.required' => 'La descripción del negocio es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 20 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
            'direccion.required' => 'La dirección del negocio es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 10 caracteres',
            'id_municipio.required' => 'El municipio es obligatorio',
            'id_municipio.exists' => 'El municipio seleccionado no es válido',
            'logo.image' => 'El archivo debe ser una imagen',
            'logo.max' => 'La imagen no puede ser mayor a 2MB',
            'email_contacto.required' => 'El correo de contacto es obligatorio',
            'email_contacto.email' => 'El correo de contacto debe ser válido',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.regex' => 'El teléfono debe tener el formato ####-#### y comenzar con 2, 6 o 7',
            'metodos_pago.*.exists' => 'Uno o más métodos de pago seleccionados no son válidos',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Transacción (Todo o Nada)
        DB::beginTransaction();

        try {
            // A. Crear el USUARIO
            $usuario = Usuario::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_estado_usuario' => 1, // Estado activo por defecto
            ]);

            // B. Subir el LOGO si existe
            $rutaLogo = null;
            if ($request->hasFile('logo')) {
                $rutaLogo = $request->file('logo')->store('logos', 'public');
            }

            // C. Crear el NEGOCIO
            $negocio = Negocio::create([
                'id_usuario'          => $usuario->id_usuario,
                'id_municipio'        => $request->id_municipio,
                'nombre'              => trim($request->nombre_negocio),
                'descripcion'         => trim($request->descripcion),
                'direccion'           => trim($request->direccion),
                'telefono'            => $request->telefono,
                'email_contacto'      => $request->email_contacto,
                'logo'                => $rutaLogo,
                'estado_verificacion' => false, // Por defecto no verificado
            ]);

            // D. Guardar Categorías (tabla intermedia negocio_categoria)
            $negocio->categorias()->attach($request->id_categoria);

            // E. Guardar Métodos de Pago (si se proporcionaron)
            if ($request->has('metodos_pago') && !empty($request->metodos_pago)) {
                $negocio->metodosPago()->attach($request->metodos_pago);
            }

            // F. Confirmar transacción
            DB::commit();

            // G. Generar token de autenticación
            $token = $usuario->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Negocio registrado exitosamente',
                'token' => $token,
                'usuario' => $usuario,
                'negocio' => $negocio
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el negocio',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
