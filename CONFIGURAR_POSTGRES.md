# Configuración de PostgreSQL para NegocioSV

## ✅ No hay problema trabajar con diferentes bases de datos

Tu compañero puede usar PostgreSQL y tú puedes usar MySQL o PostgreSQL en Laragon. Laravel es compatible con ambas y el código funcionará igual.

## Opción 1: Instalar PostgreSQL en Laragon

### Paso 1: Descargar PostgreSQL
1. Descarga PostgreSQL desde: https://www.postgresql.org/download/windows/
2. Instala PostgreSQL (versión 14 o superior recomendada)
3. Durante la instalación, anota la contraseña que configures para el usuario `postgres`

### Paso 2: Configurar PostgreSQL en Laragon

1. **Agregar PostgreSQL a Laragon:**
   - Abre Laragon
   - Click derecho en el ícono de Laragon → Preferencias → Servicios
   - Agrega PostgreSQL si no está listado

2. **Crear la base de datos:**
   
   Opción A - Usando pgAdmin (viene con PostgreSQL):
   ```
   - Abre pgAdmin
   - Conéctate al servidor local
   - Click derecho en "Databases" → Create → Database
   - Nombre: negociosv
   - Owner: postgres
   - Click "Save"
   ```
   
   Opción B - Usando línea de comandos:
   ```bash
   # Abre PowerShell como administrador
   psql -U postgres
   # Ingresa tu contraseña
   CREATE DATABASE negociosv;
   \q
   ```

### Paso 3: Instalar el driver de PostgreSQL para PHP

1. **Verificar si ya está instalado:**
   ```bash
   php -m | findstr pdo_pgsql
   ```

2. **Si no está instalado:**
   - Abre el archivo `php.ini` de Laragon (usualmente en `C:\laragon\bin\php\php-8.x\php.ini`)
   - Busca la línea `;extension=pdo_pgsql`
   - Quita el punto y coma (`;`) para descomentarla: `extension=pdo_pgsql`
   - Busca también `;extension=pgsql` y descoméntala: `extension=pgsql`
   - Guarda el archivo
   - Reinicia Laragon

### Paso 4: Configurar Laravel

Edita el archivo `negociosv/.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=negociosv
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña_aqui
```

### Paso 5: Ejecutar Migraciones

```bash
cd negociosv
php artisan migrate
```

---

## Opción 2: Usar MySQL en Laragon (Más Simple)

Si prefieres usar MySQL localmente y tu compañero usa PostgreSQL, no hay problema. Laravel maneja las diferencias automáticamente.

### Configuración MySQL:

1. **Crear base de datos en phpMyAdmin:**
   - Abre http://localhost/phpmyadmin
   - Crea base de datos `negociosv`

2. **Configurar `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=negociosv
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Ejecutar migraciones:**
   ```bash
   cd negociosv
   php artisan migrate
   ```

---

## 🔄 Trabajar en Equipo con Diferentes Bases de Datos

### ¿Cómo funciona?

- **Laravel es agnóstico a la base de datos:** El mismo código funciona con MySQL, PostgreSQL, SQLite, etc.
- **Las migraciones son compatibles:** Laravel traduce automáticamente las consultas
- **Git ignora `.env`:** Cada desarrollador tiene su propia configuración local

### Buenas Prácticas:

1. **Nunca subir `.env` a Git** (ya está en `.gitignore`)
2. **Compartir `.env.example`** con la estructura pero sin credenciales
3. **Usar las mismas migraciones** para mantener la estructura sincronizada
4. **Probar en ambas bases de datos** antes de hacer merge importante

### Archivo `.env.example` para el equipo:

Crea/actualiza `negociosv/.env.example`:

```env
APP_NAME=NegocioSV
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

# Base de datos - Cada desarrollador configura la suya
DB_CONNECTION=pgsql  # o mysql
DB_HOST=127.0.0.1
DB_PORT=5432  # 5432 para PostgreSQL, 3306 para MySQL
DB_DATABASE=negociosv
DB_USERNAME=postgres  # o root para MySQL
DB_PASSWORD=

# Resto de configuración...
```

---

## 🐛 Solución de Problemas

### Error: "could not find driver"

**Solución:**
1. Verifica que las extensiones estén habilitadas en `php.ini`:
   - `extension=pdo_pgsql`
   - `extension=pgsql`
2. Reinicia Laragon
3. Verifica con: `php -m | findstr pgsql`

### Error: "SQLSTATE[08006] Connection refused"

**Solución:**
1. Verifica que PostgreSQL esté corriendo
2. Verifica el puerto (5432 por defecto)
3. Verifica usuario y contraseña en `.env`

### Error: "database does not exist"

**Solución:**
1. Crea la base de datos manualmente en pgAdmin o con:
   ```bash
   psql -U postgres -c "CREATE DATABASE negociosv;"
   ```

---

## ✅ Verificar que Todo Funciona

Después de configurar, prueba:

```bash
# 1. Verificar conexión a la base de datos
cd negociosv
php artisan migrate:status

# 2. Ejecutar migraciones
php artisan migrate

# 3. Probar la API
php artisan serve
# En otra terminal:
curl http://127.0.0.1:8000/api/ping
```

---

## 📝 Resumen

- ✅ Puedes usar PostgreSQL o MySQL, ambos funcionan
- ✅ Tu compañero puede usar PostgreSQL, tú MySQL (o viceversa)
- ✅ Laravel maneja las diferencias automáticamente
- ✅ Solo necesitas configurar tu `.env` local
- ✅ Las migraciones funcionarán en ambas bases de datos
