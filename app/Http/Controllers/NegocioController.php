<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negocio;
use App\Models\Usuario;
use App\Models\Perfil;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'id_categoria'      => 'required|array|min:1',
            'id_categoria.*'    => 'integer|exists:categorias,id_categoria',
            'descripcion'       => 'required|string|min:20|max:500',
            'direccion'         => 'required|string|min:10',
            'id_municipio'      => 'required|integer|exists:municipios,id_municipio',
            'logoFile'          => 'nullable|image|max:2048',
            'logo'              => 'nullable|string', // Para base64
            'email_contacto'    => 'required|email',
            'telefono'          => 'required|string|regex:/^\d{4}-\d{4}$/',
            'metodos_pago'      => 'nullable|array',
            'metodos_pago.*'    => 'integer|exists:metodos_pago,id_metodo_pago',
        ], [
            // Mensajes personalizados en español
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'nombre_negocio.required' => 'El nombre del negocio es obligatorio',
            'nombre_negocio.min' => 'El nombre del negocio debe tener al menos 3 caracteres',
            'nombre_negocio.max' => 'El nombre del negocio no puede exceder 100 caracteres',
            'id_categoria.required' => 'Debes seleccionar al menos una categoría',
            'id_categoria.min' => 'Debes seleccionar al menos una categoría',
            'id_categoria.*.exists' => 'Una o más categorías seleccionadas no son válidas',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 20 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
            'direccion.required' => 'La dirección es obligatoria',
            'direccion.min' => 'La dirección debe tener al menos 10 caracteres',
            'id_municipio.required' => 'El municipio es obligatorio',
            'id_municipio.exists' => 'El municipio seleccionado no es válido',
            'email_contacto.required' => 'El correo de contacto es obligatorio',
            'email_contacto.email' => 'El correo de contacto debe ser válido',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.regex' => 'El teléfono debe tener el formato ####-####',
            'metodos_pago.*.exists' => 'Uno o más métodos de pago seleccionados no son válidos',
        ]);

        if($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Falló la validación al registrar negocio: ', $validator->errors()->all());
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
            Perfil::create([
                'id_usuario' => $usuario->id_usuario,
                'nombres'    => 'Usuario', // Valor por defecto
                'apellidos'  => 'Negocio', // Valor por defecto
            ]);

            // B. Procesar logo si existe
            $rutalogoFile = null;
            
            // Opción 1: Si viene como archivo (multipart/form-data)
            if ($request->hasFile('logoFile')) {
                $file = $request->file('logoFile');
                $filename = 'negocio_' . $usuario->id_usuario . '_' . time() . '.' . $file->getClientOriginalExtension();
                $rutalogoFile = $file->storeAs('negocios', $filename, 'public');
            }
            // Opción 2: Si viene como base64
            elseif ($request->logo && strpos($request->logo, 'data:image') === 0) {
                try {
                    // Extraer el tipo de imagen y los datos base64
                    preg_match('/data:image\/(\w+);base64,(.*)/', $request->logo, $matches);
                    if (count($matches) === 3) {
                        $imageType = $matches[1]; // jpeg, png, etc.
                        $imageData = base64_decode($matches[2]);
                        
                        // Generar nombre único para el archivo
                        $filename = 'negocio_' . $usuario->id_usuario . '_' . time() . '.' . $imageType;
                        
                        // Guardar en storage/app/public/negocios
                        Storage::disk('public')->put('negocios/' . $filename, $imageData);
                        $rutalogoFile = 'negocios/' . $filename;
                    }
                } catch (\Exception $e) {
                    // Si falla la conversión, continuar sin logo
                    \Log::warning('Error al procesar logo base64: ' . $e->getMessage());
                }
            }
            // Opción 3: Si viene como URL o string simple
            elseif ($request->logo) {
                $rutalogoFile = $request->logo;
            }

            // C. Crear el NEGOCIO
            $negocio = Negocio::create([
                'id_usuario'      => $usuario->id_usuario, // Usamos el ID del usuario recién creado
                'id_municipio'    => $request->id_municipio,
                'nombre'          => $request->nombre_negocio,
                'descripcion'     => $request->descripcion,
                'direccion'       => $request->direccion,
                'telefono'        => $request->telefono,
                'email_contacto'  => $request->email_contacto,
                'logo'            => $rutalogoFile,
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

    function listarNegocios(Request $request)
    {
        try {
            // Parámetros de búsqueda
            $query = $request->input('query'); // Texto de búsqueda
            $municipioId = $request->input('municipio'); // ID del municipio
            $lat = $request->input('lat'); // Latitud del usuario
            $lng = $request->input('lng'); // Longitud del usuario
            $radius = $request->input('radius', 50); // Radio en km (por defecto 50km)

            // Query base
            $negociosQuery = Negocio::with(['municipio', 'categorias', 'metodosPago'])
                ->withAvg('resenas', 'calificacion')
                ->withCount('resenas');

            // Filtro por texto (nombre o descripción)
            if ($query) {
                $negociosQuery->where(function($q) use ($query) {
                    $q->where('nombre', 'ILIKE', "%{$query}%")
                      ->orWhere('descripcion', 'ILIKE', "%{$query}%");
                });
            }

            // Filtro por municipio
            if ($municipioId) {
                $negociosQuery->where('id_municipio', $municipioId);
            }

            // Búsqueda por proximidad geográfica
            if ($lat && $lng) {
                // Fórmula de Haversine para calcular distancia
                // Solo incluye negocios que tengan coordenadas
                $negociosQuery->selectRaw("
                    negocios.*,
                    (6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(latitud)) * 
                        cos(radians(longitud) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(latitud))
                    )) AS distancia
                ", [$lat, $lng, $lat])
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->having('distancia', '<=', $radius)
                ->orderBy('distancia', 'asc');
            } else {
                // Si no hay geolocalización, ordenar por fecha
                $negociosQuery->orderBy('created_at', 'desc');
            }

            // Paginación
            $negocios = $negociosQuery->paginate(12);

            // Añadir datos estructurados JSON-LD para SEO
            $structuredData = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'numberOfItems' => $negocios->total(),
                'itemListElement' => []
            ];

            foreach ($negocios->items() as $index => $negocio) {
                $structuredData['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => [
                        '@type' => 'LocalBusiness',
                        '@id' => url("/negocio/{$negocio->id_negocio}"),
                        'name' => $negocio->nombre,
                        'description' => $negocio->descripcion,
                        'image' => $negocio->logo ? asset('storage/' . $negocio->logo) : null,
                        'address' => [
                            '@type' => 'PostalAddress',
                            'streetAddress' => $negocio->direccion,
                            'addressLocality' => $negocio->municipio->nombre ?? '',
                            'addressCountry' => 'SV'
                        ],
                        'telephone' => $negocio->telefono,
                        'email' => $negocio->email_contacto,
                        'aggregateRating' => $negocio->resenas_avg_calificacion ? [
                            '@type' => 'AggregateRating',
                            'ratingValue' => round($negocio->resenas_avg_calificacion, 1),
                            'reviewCount' => $negocio->resenas_count,
                            'bestRating' => 5,
                            'worstRating' => 1
                        ] : null,
                        'geo' => ($negocio->latitud && $negocio->longitud) ? [
                            '@type' => 'GeoCoordinates',
                            'latitude' => $negocio->latitud,
                            'longitude' => $negocio->longitud
                        ] : null
                    ]
                ];
            }

            return response()->json([
                'status' => true,
                'data' => $negocios,
                'structuredData' => $structuredData,
                'filters' => [
                    'query' => $query,
                    'municipio' => $municipioId,
                    'hasLocation' => ($lat && $lng) ? true : false
                ]
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
            // Agregamos 'resenas.usuario' para traer las reseñas y QUIÉN las escribió (para mostrar nombre/foto en el front)
            $negocio = Negocio::with([
                'municipio', 
                'categorias', 
                'metodosPago', 
                'usuario', 
                'resenas.usuario' // Trae las reseñas y los datos del usuario que reseñó
            ])->find($id);

            if (!$negocio) {
                return response()->json([
                    'status' => false,
                    'message' => 'Negocio no encontrado'
                ], 404);
            }

            // Lógica para saber si el usuario logueado ya hizo una reseña
            $usuarioYaReseno = false;
            
            // Verificamos si hay alguien logueado con Sanctum
            if (Auth::guard('sanctum')->check()) {
                $usuarioId = Auth::guard('sanctum')->id();
                
                // Buscamos en la colección de reseñas que ya trajimos si alguna pertenece a este usuario
                $usuarioYaReseno = $negocio->resenas->contains('id_usuario', $usuarioId);
            }

            // Calculamos el promedio de calificación manualmente
            $promedio = $negocio->resenas->avg('calificacion');

            return response()->json([
                'status' => true,
                'data' => $negocio,
                'meta' => [
                    'promedio_calificacion' => round($promedio, 1), 
                    'total_resenas' => $negocio->resenas->count(),
                    'usuario_actual_ya_reseno' => $usuarioYaReseno // <--- Esto le sirve al Front para ocultar/mostrar el formulario
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actualizarNegocio(Request $request, $id)
    {
        try {
            $negocio = Negocio::findOrFail($id);
            
            // Verificar que el usuario autenticado sea el dueño
            if ($negocio->id_usuario !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para editar este negocio'
                ], 403);
            }

            $negocio->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Negocio actualizado correctamente',
                'data' => $negocio
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar negocio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminarNegocio($id)
    {
        try {
            $negocio = Negocio::findOrFail($id);
            
            // Verificar que el usuario autenticado sea el dueño
            if ($negocio->id_usuario !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este negocio'
                ], 403);
            }

            $negocio->delete();

            return response()->json([
                'success' => true,
                'message' => 'Negocio eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar negocio: ' . $e->getMessage()
            ], 500);
        }
    }
}
