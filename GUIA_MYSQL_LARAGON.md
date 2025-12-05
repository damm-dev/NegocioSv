# 🎉 Tu Backend Laravel está LISTO para el Frontend

## ✅ Estado Actual

### Servidor Backend
- **URL del servidor:** http://127.0.0.1:8000
- **Estado:** ✅ Corriendo (mantén la terminal abierta)
- **Base de datos:** MySQL (negociosv)

### Datos Disponibles
- ✅ 1 usuario de prueba
- ✅ 6 categorías
- ✅ 6 departamentos
- ✅ Estados de usuario
- ✅ Términos y condiciones
- ✅ Intereses

---

## 📊 Cómo Ver tu Base de Datos en MySQL

### Opción 1: HeidiSQL (Recomendado - Ya viene con Laragon)
1. Abre **Laragon**
2. Click en el botón **"Database"** o **"HeidiSQL"**
3. Se abrirá HeidiSQL automáticamente conectado
4. En el panel izquierdo verás la base de datos **"negociosv"**
5. Haz click en las tablas para ver los datos

### Opción 2: phpMyAdmin (Si lo tienes instalado)
1. Abre tu navegador
2. Ve a: http://localhost/phpmyadmin
3. Usuario: `root`
4. Contraseña: (dejar en blanco)
5. Selecciona la base de datos **"negociosv"**

### Opción 3: Desde la Terminal
```bash
# Ver todas las tablas
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE negociosv; SHOW TABLES;"

# Ver usuarios
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE negociosv; SELECT * FROM usuarios;"

# Ver categorías
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE negociosv; SELECT * FROM categorias;"
```

---

## 🔌 Endpoints API Disponibles

### Autenticación
- `POST /api/register` - Registrar nuevo usuario
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión (requiere token)

### Usuarios
- `GET /api/usuarios` - Listar usuarios
- `GET /api/usuarios/{id}` - Ver usuario específico
- `PUT /api/usuarios/{id}` - Actualizar usuario
- `DELETE /api/usuarios/{id}` - Eliminar usuario

### Negocios
- `GET /api/negocios` - Listar negocios
- `POST /api/negocios` - Crear negocio
- `GET /api/negocios/{id}` - Ver negocio específico
- `PUT /api/negocios/{id}` - Actualizar negocio
- `DELETE /api/negocios/{id}` - Eliminar negocio

### Perfiles
- `GET /api/perfiles` - Listar perfiles
- `POST /api/perfiles` - Crear perfil
- `GET /api/perfiles/{id}` - Ver perfil específico
- `PUT /api/perfiles/{id}` - Actualizar perfil
- `DELETE /api/perfiles/{id}` - Eliminar perfil

---

## 🚀 Conectar tu Frontend

### Configuración en tu Frontend (React/Vue/Angular)

```javascript
// Configuración base de la API
const API_BASE_URL = 'http://127.0.0.1:8000/api';

// Ejemplo de petición con fetch
fetch(`${API_BASE_URL}/usuarios`)
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

// Ejemplo con axios
import axios from 'axios';

axios.defaults.baseURL = 'http://127.0.0.1:8000/api';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Para peticiones autenticadas (después del login)
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
```

### Ejemplo de Login
```javascript
const login = async (email, password) => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email, password })
    });
    
    const data = await response.json();
    
    if (data.token) {
      // Guardar el token para futuras peticiones
      localStorage.setItem('token', data.token);
      return data;
    }
  } catch (error) {
    console.error('Error en login:', error);
  }
};
```

---

## 🔧 Comandos Útiles

### Gestión del Servidor
```bash
# Iniciar servidor (ya está corriendo)
php artisan serve

# Iniciar en otro puerto
php artisan serve --port=8080

# Detener servidor
# Presiona Ctrl+C en la terminal donde está corriendo
```

### Gestión de Base de Datos
```bash
# Ver estado de migraciones
php artisan migrate:status

# Revertir última migración
php artisan migrate:rollback

# Revertir todas y volver a ejecutar
php artisan migrate:fresh

# Revertir y ejecutar seeders
php artisan migrate:fresh --seed

# Solo ejecutar seeders
php artisan db:seed
```

### Limpiar Caché
```bash
# Limpiar toda la caché
php artisan optimize:clear

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear
```

---

## 📝 Credenciales de Prueba

### Usuario de Prueba (creado por el seeder)
Revisa el archivo `database/seeders/UsuarioSeeder.php` para ver las credenciales del usuario de prueba.

### Base de Datos MySQL
- **Host:** 127.0.0.1
- **Puerto:** 3306
- **Base de datos:** negociosv
- **Usuario:** root
- **Contraseña:** (vacía)

---

## ⚠️ Solución de Problemas Comunes

### Error de CORS
Si tu frontend no puede conectarse por CORS, edita `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000'], // Tu URL del frontend
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

### El servidor se detuvo
Simplemente ejecuta de nuevo:
```bash
php artisan serve
```

### Error de conexión a la base de datos
Verifica que Laragon esté corriendo y MySQL esté activo:
1. Abre Laragon
2. Verifica que el botón diga "Stop All" (significa que está corriendo)
3. Si dice "Start All", haz click para iniciar los servicios

---

## 🎯 Próximos Pasos

1. ✅ **Backend listo** - El servidor está corriendo
2. ✅ **Base de datos poblada** - Tienes datos de prueba
3. 🔄 **Conecta tu frontend** - Usa los endpoints API
4. 🧪 **Prueba las peticiones** - Usa Postman o tu frontend
5. 🚀 **Desarrolla tu aplicación** - ¡Todo está listo!

---

## 📞 Comandos Rápidos de Referencia

```bash
# Ver tablas de la BD
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE negociosv; SHOW TABLES;"

# Ver rutas disponibles
php artisan route:list

# Ver información del sistema
php artisan about

# Crear un nuevo controlador
php artisan make:controller NombreController

# Crear un nuevo modelo
php artisan make:model NombreModelo -m
```

---

¡Tu backend está completamente configurado y listo para trabajar con tu frontend! 🎉
