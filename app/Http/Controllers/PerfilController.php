<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Interes;
use App\Models\Negocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PerfilController extends Controller
{
   
    public function verPerfil(Request $request)
    {
        $usuario = Auth::user(); // Usuario autenticado

        $usuario = Usuario::with([
            'perfil.municipio.departamento',
            'intereses.categoria'
        ])->find($usuario->id_usuario);

        if (!$usuario) {
            return response()->json([
                'message' => 'Perfil no encontrado'
            ], 404);
        }

        // Construir URL completa de la foto si existe
        $fotoUrl = null;
        if ($usuario->perfil && $usuario->perfil->foto) {
            $fotoUrl = url('storage/' . $usuario->perfil->foto);
        }

        return response()->json([
            'usuario' => [
                'id' => $usuario->id_usuario,
                'email' => $usuario->email,
                'estado' => $usuario->estado->nombre ?? null,
            ],
            'perfil' => $usuario->perfil,
            'foto_url' => $fotoUrl,
            'municipio' => $usuario->perfil->municipio->nombre ?? null,
            'departamento' => $usuario->perfil->municipio->departamento->nombre ?? null,
            'intereses' => $usuario->intereses->pluck('categoria.nombre'),
            'type' => 'persona'
        ], 200);
    }

    public function verPerfilNegocio(Request $request)
    {
        $usuario = Auth::user(); // Usuario autenticado

        // Buscar el negocio asociado a este usuario con fotos incluidas
        $negocio = Negocio::with([
            'municipio.departamento',
            'categorias',
            'metodosPago',
            'fotos' // Incluir fotos adicionales
        ])->where('id_usuario', $usuario->id_usuario)->first();

        if (!$negocio) {
            return response()->json([
                'message' => 'Negocio no encontrado'
            ], 404);
        }

        // Construir URL completa del logo si existe
        $logoUrl = null;
        if ($negocio->logo) {
            // Si el logo ya es una URL completa, usarla directamente
            if (filter_var($negocio->logo, FILTER_VALIDATE_URL)) {
                $logoUrl = $negocio->logo;
            } else {
                // Si es una ruta relativa, construir la URL completa
                $logoUrl = url('storage/' . $negocio->logo);
            }
        }

        // Procesar fotos para el frontend
        $fotosFormateadas = $negocio->fotos->map(function ($foto) {
            return [
                'id' => $foto->id_foto,
                'url' => $foto->foto_url,
                'orden' => $foto->orden,
                'descripcion' => $foto->descripcion
            ];
        });

        return response()->json([
            'usuario' => [
                'id' => $usuario->id_usuario,
                'email' => $usuario->email,
                'estado' => $usuario->estado->nombre ?? null,
            ],
            'negocio' => [
                'id' => $negocio->id_negocio,
                'nombre' => $negocio->nombre,
                'descripcion' => $negocio->descripcion,
                'direccion' => $negocio->direccion,
                'telefono' => $negocio->telefono,
                'email_contacto' => $negocio->email_contacto,
                'logo' => $negocio->logo,
                'logo_url' => $logoUrl, // URL del logo dentro del objeto negocio
                'estado_verificacion' => $negocio->estado_verificacion,
                'id_municipio' => $negocio->id_municipio,
                'fotos' => $fotosFormateadas, // Fotos adicionales del negocio
            ],
            'logo_url' => $logoUrl,
            'municipio' => $negocio->municipio->nombre ?? null,
            'departamento' => $negocio->municipio->departamento->nombre ?? null,
            'categorias' => $negocio->categorias->pluck('nombre'),
            'metodos_pago' => $negocio->metodosPago->pluck('nombre'),
            'type' => 'negocio'
        ], 200);
    }

    //  2. ACTUALIZAR PERFIL
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user(); // Usuario autenticado

        $perfil = Perfil::where('id_usuario', $usuario->id_usuario)->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $request->validate([
            'nombres' => 'nullable|string|min:2',
            'apellidos' => 'nullable|string|min:2',
            'telefono' => 'nullable|string',
            'foto' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'id_municipio' => 'nullable|integer|exists:municipios,id_municipio',
            'intereses' => 'nullable|array',
            'intereses.*' => 'integer|exists:categorias,id_categoria',
        ]);

        $perfil->update($request->only([
            'nombres',
            'apellidos',
            'telefono',
            'descripcion',
            'foto',
            'id_municipio'
        ]));

        // Actualizar intereses si vienen
        if ($request->has('intereses')) {
            Interes::where('id_usuario', $usuario->id_usuario)->delete();

            foreach ($request->intereses as $cat) {
                Interes::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_categoria' => $cat
                ]);
            }
        }

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'perfil' => $perfil
        ], 200);
    }

    /**
     * Subir foto de perfil (persona)
     */
    public function subirFoto(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $perfil = Perfil::where('id_usuario', $usuario->id_usuario)->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        // Eliminar foto anterior si existe
        if ($perfil->foto && Storage::disk('public')->exists($perfil->foto)) {
            Storage::disk('public')->delete($perfil->foto);
        }

        // Guardar nueva foto
        $file = $request->file('foto');
        $filename = 'perfil_' . $usuario->id_usuario . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('perfiles', $filename, 'public');

        $perfil->foto = $path;
        $perfil->save();

        return response()->json([
            'message' => 'Foto de perfil actualizada correctamente',
            'foto_url' => url('storage/' . $path)
        ], 200);
    }

    /**
     * Subir logo del negocio
     */
    public function subirLogo(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $negocio = Negocio::where('id_usuario', $usuario->id_usuario)->first();

        if (!$negocio) {
            return response()->json(['message' => 'Negocio no encontrado'], 404);
        }

        // Eliminar logo anterior si existe
        if ($negocio->logo && Storage::disk('public')->exists($negocio->logo)) {
            Storage::disk('public')->delete($negocio->logo);
        }

        // Guardar nuevo logo
        $file = $request->file('logo');
        $filename = 'logo_' . $negocio->id_negocio . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('logos', $filename, 'public');

        $negocio->logo = $path;
        $negocio->save();

        return response()->json([
            'message' => 'Logo actualizado correctamente',
            'logo_url' => url('storage/' . $path)
        ], 200);
    }

    //  3. ELIMINAR PERFIL
    public function eliminarPerfil(Request $request)
    {
        $usuario = Auth::user();; // Usuario autenticado

        $perfil = Perfil::where('id_usuario', $usuario->id_usuario)->first();

        if (!$perfil) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $perfil->delete();
        $usuario->delete();

        return response()->json(['message' => 'Usuario y perfil eliminados correctamente']);
    }
}
