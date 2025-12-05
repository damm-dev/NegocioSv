# 🔧 Configurar .env para PostgreSQL

## ✅ Base de datos ya creada
La base de datos `negociosv` ya está creada en PostgreSQL 17.

## 📝 Pasos para configurar .env

### 1. Abrir el archivo .env
Abre el archivo `negociosv/.env` en tu editor de código.

### 2. Buscar y modificar estas líneas:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Cambiarlas por:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=negociosv
DB_USERNAME=postgres
DB_PASSWORD=TU_CONTRASEÑA_AQUI
```

**IMPORTANTE:** Reemplaza `TU_CONTRASEÑA_AQUI` con la contraseña que usaste para conectarte a PostgreSQL en pgAdmin.

### 4. Guardar el archivo

---

## 🚀 Siguiente paso: Ejecutar migraciones

Una vez configurado el `.env`, ejecuta estos comandos en orden:

```bash
# 1. Generar la clave de aplicación (si no existe)
cd negociosv
php artisan key:generate

# 2. Ejecutar las migraciones
php artisan migrate

# 3. (Opcional) Ejecutar seeders si los tienes
php artisan db:seed
```

---

## ✅ Verificar la conexión

Para verificar que Laravel se conecta correctamente a PostgreSQL:

```bash
php artisan migrate:status
```

Si ves la lista de migraciones, ¡todo está funcionando! 🎉

---

## 🐛 Solución de problemas

### Error: "could not find driver"
- Verifica que las extensiones de PostgreSQL estén habilitadas en php.ini
- Reinicia Laragon completamente

### Error: "SQLSTATE[08006] connection failed"
- Verifica que PostgreSQL esté corriendo
- Verifica la contraseña en el archivo .env
- Verifica que la base de datos `negociosv` exista en pgAdmin

### Error: "password authentication failed"
- La contraseña en .env no es correcta
- Intenta conectarte a pgAdmin con la misma contraseña para verificar
