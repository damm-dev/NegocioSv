<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Negocio;
use App\Models\Categoria;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\EstadoUsuario;
use App\Models\Interes;
use App\Models\Termino;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // ==================== AUTENTICACIÓN ====================
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Administrador::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (!$admin->activo) {
            return response()->json(['message' => 'Cuenta de administrador inactiva'], 403);
        }

        // Limpiar tokens anteriores
        $admin->tokens()->delete();

        // Crear nuevo token
        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'admin' => [
                'id' => $admin->id_administrador,
                'nombre' => $admin->nombre,
                'email' => $admin->email
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    // ==================== USUARIOS ====================
    
    public function listarUsuarios()
    {
        $usuarios = Usuario::with(['perfil', 'estado'])->get();
        return response()->json(['success' => true, 'data' => $usuarios]);
    }

    public function obtenerUsuario($id)
    {
        $usuario = Usuario::with(['perfil.municipio.departamento', 'estado', 'intereses.categoria'])
            ->findOrFail($id);
        return response()->json(['success' => true, 'data' => $usuario]);
    }

    public function actualizarUsuario(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:usuarios,email,' . $id . ',id_usuario',
            'password' => 'sometimes|min:6',
            'id_estado_usuario' => 'sometimes|exists:estados_usuario,id_estado_usuario'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $usuario = Usuario::findOrFail($id);
        
        if ($request->has('email')) {
            $usuario->email = $request->email;
        }
        
        if ($request->has('password')) {
            $usuario->password = Hash::make($request->password);
        }
        
        if ($request->has('id_estado_usuario')) {
            $usuario->id_estado_usuario = $request->id_estado_usuario;
        }
        
        $usuario->save();

        return response()->json(['success' => true, 'message' => 'Usuario actualizado', 'data' => $usuario]);
    }

    public function eliminarUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();
        return response()->json(['success' => true, 'message' => 'Usuario eliminado']);
    }

    // ==================== PERFILES ====================
    
    public function listarPerfiles()
    {
        $perfiles = Perfil::with(['usuario', 'municipio.departamento'])->get();
        return response()->json(['success' => true, 'data' => $perfiles]);
    }

    public function obtenerPerfil($id)
    {
        $perfil = Perfil::with(['usuario', 'municipio.departamento'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $perfil]);
    }

    public function actualizarPerfil(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:100',
            'telefono' => 'sometimes|string|max:20',
            'id_municipio' => 'sometimes|exists:municipios,id_municipio'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $perfil = Perfil::findOrFail($id);
        $perfil->update($request->all());

        return response()->json(['success' => true, 'message' => 'Perfil actualizado', 'data' => $perfil]);
    }

    public function eliminarPerfil($id)
    {
        $perfil = Perfil::findOrFail($id);
        $perfil->delete();
        return response()->json(['success' => true, 'message' => 'Perfil eliminado']);
    }

    // ==================== NEGOCIOS ====================
    
    public function listarNegocios()
    {
        $negocios = Negocio::with(['usuario.perfil', 'municipio.departamento', 'categorias', 'metodosPago'])->get();
        return response()->json(['success' => true, 'data' => $negocios]);
    }

    public function obtenerNegocio($id)
    {
        $negocio = Negocio::with(['usuario.perfil', 'municipio.departamento', 'categorias', 'metodosPago'])
            ->findOrFail($id);
        return response()->json(['success' => true, 'data' => $negocio]);
    }

    public function crearNegocio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'id_municipio' => 'required|exists:municipios,id_municipio',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'direccion' => 'required|string|max:200',
            'telefono' => 'required|string|max:20',
            'email_contacto' => 'required|email|max:100',
            'categorias' => 'required|array',
            'categorias.*' => 'exists:categorias,id_categoria',
            'metodos_pago' => 'required|array',
            'metodos_pago.*' => 'exists:metodos_pago,id_metodo_pago'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $negocio = Negocio::create($request->except(['categorias', 'metodos_pago']));
        
        // Asociar categorías y métodos de pago
        $negocio->categorias()->attach($request->categorias);
        $negocio->metodosPago()->attach($request->metodos_pago);

        return response()->json(['success' => true, 'message' => 'Negocio creado', 'data' => $negocio], 201);
    }

    public function actualizarNegocio(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_municipio' => 'sometimes|exists:municipios,id_municipio',
            'nombre' => 'sometimes|string|max:100',
            'descripcion' => 'sometimes|string',
            'direccion' => 'sometimes|string|max:200',
            'telefono' => 'sometimes|string|max:20',
            'email_contacto' => 'sometimes|email|max:100',
            'estado_verificacion' => 'sometimes|boolean',
            'categorias' => 'sometimes|array',
            'categorias.*' => 'exists:categorias,id_categoria',
            'metodos_pago' => 'sometimes|array',
            'metodos_pago.*' => 'exists:metodos_pago,id_metodo_pago'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $negocio = Negocio::findOrFail($id);
        $negocio->update($request->except(['categorias', 'metodos_pago']));

        if ($request->has('categorias')) {
            $negocio->categorias()->sync($request->categorias);
        }

        if ($request->has('metodos_pago')) {
            $negocio->metodosPago()->sync($request->metodos_pago);
        }

        return response()->json(['success' => true, 'message' => 'Negocio actualizado', 'data' => $negocio]);
    }

    public function eliminarNegocio($id)
    {
        $negocio = Negocio::findOrFail($id);
        $negocio->delete();
        return response()->json(['success' => true, 'message' => 'Negocio eliminado']);
    }

    // ==================== CATEGORÍAS ====================
    
    public function listarCategorias()
    {
        $categorias = Categoria::all();
        return response()->json(['success' => true, 'data' => $categorias]);
    }

    public function obtenerCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        return response()->json(['success' => true, 'data' => $categoria]);
    }

    public function crearCategoria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:categorias,nombre'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $categoria = Categoria::create($request->all());
        return response()->json(['success' => true, 'message' => 'Categoría creada', 'data' => $categoria], 201);
    }

    public function actualizarCategoria(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $id . ',id_categoria'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $categoria = Categoria::findOrFail($id);
        $categoria->update($request->all());

        return response()->json(['success' => true, 'message' => 'Categoría actualizada', 'data' => $categoria]);
    }

    public function eliminarCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();
        return response()->json(['success' => true, 'message' => 'Categoría eliminada']);
    }

    // ==================== MÉTODOS DE PAGO ====================
    
    public function listarMetodosPago()
    {
        $metodos = MetodoPago::all();
        return response()->json(['success' => true, 'data' => $metodos]);
    }

    public function obtenerMetodoPago($id)
    {
        $metodo = MetodoPago::findOrFail($id);
        return response()->json(['success' => true, 'data' => $metodo]);
    }

    public function crearMetodoPago(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $metodo = MetodoPago::create($request->all());
        return response()->json(['success' => true, 'message' => 'Método de pago creado', 'data' => $metodo], 201);
    }

    public function actualizarMetodoPago(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre,' . $id . ',id_metodo_pago'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $metodo = MetodoPago::findOrFail($id);
        $metodo->update($request->all());

        return response()->json(['success' => true, 'message' => 'Método de pago actualizado', 'data' => $metodo]);
    }

    public function eliminarMetodoPago($id)
    {
        $metodo = MetodoPago::findOrFail($id);
        $metodo->delete();
        return response()->json(['success' => true, 'message' => 'Método de pago eliminado']);
    }

    // ==================== DEPARTAMENTOS ====================
    
    public function listarDepartamentos()
    {
        $departamentos = Departamento::with('municipios')->get();
        return response()->json(['success' => true, 'data' => $departamentos]);
    }

    public function obtenerDepartamento($id)
    {
        $departamento = Departamento::with('municipios')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $departamento]);
    }

    public function crearDepartamento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:departamentos,nombre'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $departamento = Departamento::create($request->all());
        return response()->json(['success' => true, 'message' => 'Departamento creado', 'data' => $departamento], 201);
    }

    public function actualizarDepartamento(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:departamentos,nombre,' . $id . ',id_departamento'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $departamento = Departamento::findOrFail($id);
        $departamento->update($request->all());

        return response()->json(['success' => true, 'message' => 'Departamento actualizado', 'data' => $departamento]);
    }

    public function eliminarDepartamento($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();
        return response()->json(['success' => true, 'message' => 'Departamento eliminado']);
    }

    // ==================== MUNICIPIOS ====================
    
    public function listarMunicipios()
    {
        $municipios = Municipio::with('departamento')->get();
        return response()->json(['success' => true, 'data' => $municipios]);
    }

    public function obtenerMunicipio($id)
    {
        $municipio = Municipio::with('departamento')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $municipio]);
    }

    public function crearMunicipio(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'id_departamento' => 'required|exists:departamentos,id_departamento'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $municipio = Municipio::create($request->all());
        return response()->json(['success' => true, 'message' => 'Municipio creado', 'data' => $municipio], 201);
    }

    public function actualizarMunicipio(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:100',
            'id_departamento' => 'sometimes|exists:departamentos,id_departamento'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $municipio = Municipio::findOrFail($id);
        $municipio->update($request->all());

        return response()->json(['success' => true, 'message' => 'Municipio actualizado', 'data' => $municipio]);
    }

    public function eliminarMunicipio($id)
    {
        $municipio = Municipio::findOrFail($id);
        $municipio->delete();
        return response()->json(['success' => true, 'message' => 'Municipio eliminado']);
    }

    // ==================== ESTADOS DE USUARIO ====================
    
    public function listarEstadosUsuario()
    {
        $estados = EstadoUsuario::all();
        return response()->json(['success' => true, 'data' => $estados]);
    }

    public function obtenerEstadoUsuario($id)
    {
        $estado = EstadoUsuario::findOrFail($id);
        return response()->json(['success' => true, 'data' => $estado]);
    }

    public function crearEstadoUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:estados_usuario,nombre'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $estado = EstadoUsuario::create($request->all());
        return response()->json(['success' => true, 'message' => 'Estado creado', 'data' => $estado], 201);
    }

    public function actualizarEstadoUsuario(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:estados_usuario,nombre,' . $id . ',id_estado_usuario'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $estado = EstadoUsuario::findOrFail($id);
        $estado->update($request->all());

        return response()->json(['success' => true, 'message' => 'Estado actualizado', 'data' => $estado]);
    }

    public function eliminarEstadoUsuario($id)
    {
        $estado = EstadoUsuario::findOrFail($id);
        $estado->delete();
        return response()->json(['success' => true, 'message' => 'Estado eliminado']);
    }

    // ==================== INTERESES ====================
    
    public function listarIntereses()
    {
        $intereses = Interes::with(['usuario', 'categoria'])->get();
        return response()->json(['success' => true, 'data' => $intereses]);
    }

    public function eliminarInteres($id)
    {
        $interes = Interes::findOrFail($id);
        $interes->delete();
        return response()->json(['success' => true, 'message' => 'Interés eliminado']);
    }

    // ==================== TÉRMINOS ====================
    
    public function listarTerminos()
    {
        $terminos = Termino::all();
        return response()->json(['success' => true, 'data' => $terminos]);
    }

    public function obtenerTermino($id)
    {
        $termino = Termino::findOrFail($id);
        return response()->json(['success' => true, 'data' => $termino]);
    }

    public function crearTermino(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'version' => 'required|string|max:20',
            'contenido' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $termino = Termino::create($request->all());
        return response()->json(['success' => true, 'message' => 'Término creado', 'data' => $termino], 201);
    }

    public function actualizarTermino(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'version' => 'sometimes|string|max:20',
            'contenido' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $termino = Termino::findOrFail($id);
        $termino->update($request->all());

        return response()->json(['success' => true, 'message' => 'Término actualizado', 'data' => $termino]);
    }

    public function eliminarTermino($id)
    {
        $termino = Termino::findOrFail($id);
        $termino->delete();
        return response()->json(['success' => true, 'message' => 'Término eliminado']);
    }
}
