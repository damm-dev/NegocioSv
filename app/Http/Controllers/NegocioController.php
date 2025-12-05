<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negocio;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NegocioController extends Controller
{
    public function registrarNegocio(Request $request)
    {
        // 1. Validaciones mejoradas
        $validator = Validator::make($request->all(), [
            // Datos Usuario
            'email'             => 'required|email|unique:usuarios,email',
            'password'          => 'required|min:8',            
            // Datos Negocio
            'nombre_negocio'    => 'required|string|min:3|max:100',
            'id_categoria'      => 'required|array',
            'id_categoria.*'    => 'exists:categorias,id_categoria',
            'descripcion'       => 'required|string',
            'direccion'         => 'required|string',
            'id_municipio'      => 'required|exists:municipios,id_municipio',
            'logo'              => 'nullable|image|max:2048',
            'email_contacto'    => 'required|email',
            'telefono'          => 'required|string',
            'metodos_pago'      => 'nullable|array',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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

            // B. Subir el logoFile si existe
            $rutalogoFile = null;
            if ($request->hasFile('logoFile')) {
                $rutalogoFile = $request->file('logoFile')->store('logoFiles', 'public');
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

    function listarNegocios()
    {
        try {
            // Traemos los negocios con su Municipio y sus Categorías
            // paginate(10) para no mostrar muchos negocios a la vez
            $negocios = Negocio::with(['municipio', 'categorias'])
                ->orderBy('created_at', 'desc') // Los más nuevos primero
                ->paginate(10);

            return response()->json([
                'status' => true,
                'data' => $negocios
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los negocios: ' . $e->getMessage()
            ], 500);
        }
    }

    function detalleNegocio($id)
    {
        try {
            // Usamos 'with' para traer los datos relacionados
            $negocio = Negocio::with(['municipio', 'categorias', 'metodosPago', 'usuario'])
                ->find($id);

            // Si no existe, devolvemos error 404
            if (!$negocio) {
                return response()->json([
                    'status' => false,
                    'message' => 'Negocio no encontrado'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $negocio
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    function actualizarNegocio(Request $request, $id)
    {
        // Lógica para actualizar un negocio existente
        try {
            $negocio = Negocio::find($id);

            if (!$negocio) {
                return response()->json(['status' => false, 'message' => 'Negocio no encontrado'], 404);
            }

            // SEGURIDAD: Verificar que el usuario sea el dueño
            if ($negocio->id_usuario !== Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No tienes permiso para editar este negocio',
                    'debug_info' => [
                        'id_del_dueño_real' => $negocio->id_usuario,
                        'id_del_usuario_logueado' => Auth::id()
                    ]
                ], 403);
            }

            // Validamos (todo opcional 'nullable' porque quizás solo quiere cambiar un campo)
            $validator = Validator::make($request->all(), [
                'nombre'            => 'nullable|string|max:100',
                'descripcion'       => 'nullable|string',
                'direccion'         => 'nullable|string',
                'telefono'          => 'nullable|string',
                'email_contacto'    => 'nullable|email',
                'id_municipio'      => 'nullable|exists:municipios,id_municipio',
                'id_categoria'      => 'nullable|array', // Para actualizar categorías
                'metodos_pago'      => 'nullable|array', // Para actualizar métodos de pago
                'logoFile'              => 'nullable|image|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Manejo del logoFile (Si suben uno nuevo)
            if ($request->hasFile('logoFile')) {
                // 1. Borrar logoFile viejo si existe
                if ($negocio->logoFile) {
                    Storage::disk('public')->delete($negocio->logoFile);
                }
                // 2. Guardar nuevo
                $rutalogoFile = $request->file('logoFile')->store('logoFiles', 'public');
                $negocio->logoFile = $rutalogoFile;
            }

            // Actualizar campos de texto
            $negocio->update($request->except(['logoFile', 'id_categoria', 'metodos_pago']));

            // sync() hace la magia: si tenías [1,2] y mandas [2,3], borra el 1 y agrega el 3.
            if ($request->has('id_categoria')) {
                $negocio->categorias()->sync($request->id_categoria);
            }

            if ($request->has('metodos_pago')) {
                $negocio->metodosPago()->sync($request->metodos_pago);
            }

            return response()->json([
                'status' => true,
                'message' => 'Negocio actualizado correctamente',
                'data' => $negocio->load('categorias', 'municipio') // Recargar datos para mostrar
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    //función para eliminar un negocio y verificar que el usuario sea el dueño.
    public function eliminarNegocio($id)
    {
        try {
            $negocio = Negocio::find($id);

            if (!$negocio) {
                return response()->json(['status' => false, 'message' => 'Negocio no encontrado'], 404);
            }

            // SEGURIDAD: Verificar dueño
            if ($negocio->id_usuario !== Auth::id()) {
                return response()->json(['status' => false, 'message' => 'No tienes permiso para eliminar este negocio'], 403);
            }

            //Borrar la imagen del servidor para ahorrar espacio
            if ($negocio->logoFile) {
                Storage::disk('public')->delete($negocio->logoFile);
            }

            // 2. Eliminar registro de la BD
            $negocio->delete();

            return response()->json([
                'status' => true,
                'message' => 'Negocio eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

}
