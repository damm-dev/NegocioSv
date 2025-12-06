<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Interes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RegistroController extends Controller
{
    public function registrar(Request $request)
    {
        // Validaciones personalizadas
        $validator = Validator::make($request->all(), [
            'email'             => 'required|email|unique:usuarios,email',
            'password'          => [
                'required',
                'min:8',
                'regex:/[a-zA-Z]/',      // Al menos una letra
                'regex:/[0-9]/',         // Al menos un número
            ],
            'nombres'           => [
                'required',
                'string',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', // Solo letras y espacios
            ],
            'apellidos'         => [
                'required',
                'string',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', // Solo letras y espacios
            ],
            'fecha_nacimiento'  => [
                'required',
                'date',
                'before:today',
                'after:' . Carbon::now()->subYears(120)->format('Y-m-d'), // Máximo 120 años
            ],
            'genero'            => 'required|in:M,F,O',
            'telefono'          => [
                'required',
                'regex:/^\d{4}-\d{4}$/', // Formato ####-####
                'regex:/^[267]/',        // Debe empezar con 2, 6 o 7
            ],
            'id_municipio'      => 'required|integer|exists:municipios,id_municipio',
            'descripcion'       => 'nullable|string|max:500',
            'foto'              => 'nullable|string', // Puede ser base64 o URL
            'foto_file'         => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // Máximo 2MB
            'intereses'         => 'required|array|min:1|max:5',
            'intereses.*'       => 'integer|exists:categorias,id_categoria',
            'ubicacion_activa'  => 'nullable|boolean',
        ], [
            // Mensajes personalizados en español
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener al menos una letra y un número',
            'nombres.required' => 'El nombre es obligatorio',
            'nombres.min' => 'El nombre debe tener al menos 2 caracteres',
            'nombres.regex' => 'El nombre solo debe contener letras',
            'apellidos.required' => 'Los apellidos son obligatorios',
            'apellidos.min' => 'Los apellidos deben tener al menos 2 caracteres',
            'apellidos.regex' => 'Los apellidos solo deben contener letras',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser futura',
            'fecha_nacimiento.after' => 'La fecha de nacimiento no es válida',
            'genero.required' => 'El género es obligatorio',
            'genero.in' => 'El género debe ser M, F u O',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.regex' => 'El teléfono debe tener el formato ####-#### y comenzar con 2, 6 o 7',
            'id_municipio.required' => 'El municipio es obligatorio',
            'id_municipio.exists' => 'El municipio seleccionado no es válido',
            'intereses.required' => 'Debes seleccionar al menos un interés',
            'intereses.min' => 'Debes seleccionar al menos un interés',
            'intereses.max' => 'Puedes seleccionar máximo 5 intereses',
            'intereses.*.exists' => 'Uno o más intereses seleccionados no son válidos',
        ]);

        try {
            // Crear usuario
            $usuario = Usuario::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_estado_usuario' => 1, // Estado activo por defecto
            ]);

            // Procesar foto si existe
            $fotoPath = '';
            
            // Opción 1: Si viene como archivo (multipart/form-data)
            if ($request->hasFile('foto_file')) {
                $file = $request->file('foto_file');
                $filename = 'perfil_' . $usuario->id_usuario . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('perfiles', $filename, 'public');
            }
            // Opción 2: Si viene como base64
            elseif ($request->foto && strpos($request->foto, 'data:image') === 0) {
                try {
                    // Extraer el tipo de imagen y los datos base64
                    preg_match('/data:image\/(\w+);base64,(.*)/', $request->foto, $matches);
                    if (count($matches) === 3) {
                        $imageType = $matches[1]; // jpeg, png, etc.
                        $imageData = base64_decode($matches[2]);
                        
                        // Generar nombre único para el archivo
                        $filename = 'perfil_' . $usuario->id_usuario . '_' . time() . '.' . $imageType;
                        
                        // Guardar en storage/app/public/perfiles
                        Storage::disk('public')->put('perfiles/' . $filename, $imageData);
                        $fotoPath = 'perfiles/' . $filename;
                    }
                } catch (\Exception $e) {
                    // Si falla la conversión, continuar sin foto
                    \Log::warning('Error al procesar foto base64: ' . $e->getMessage());
                }
            }
            // Opción 3: Si viene como URL o string simple
            elseif ($request->foto) {
                $fotoPath = $request->foto;
            }

            // Crear perfil
            $perfil = Perfil::create([
                'id_usuario' => $usuario->id_usuario,
                'nombres' => trim($request->nombres),
                'apellidos' => trim($request->apellidos),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
                'foto' => $fotoPath,
                'id_municipio' => $request->id_municipio,
                'descripcion' => $request->descripcion ?? '',
                'ubicacion_activa' => $request->ubicacion_activa ?? false,
            ]);

            // Guardar intereses
            foreach ($request->intereses as $categoriaId) {
                Interes::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_categoria' => $categoriaId
                ]);
            }

            return response()->json([
                'message' => 'Usuario registrado exitosamente',
                'usuario' => $usuario,
                'perfil' => $perfil,
                'foto_url' => $fotoPath ? asset('storage/' . $fotoPath) : null
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
