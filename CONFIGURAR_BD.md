# Configuración de Base de Datos

## Problema Detectado

El proyecto necesita una base de datos configurada para funcionar. Actualmente hay un error porque no está configurada correctamente.

## Opciones de Configuración

### Opción 1: MySQL (Recomendado para Laragon)

1. **Crear la base de datos en MySQL:**
   - Abre phpMyAdmin en Laragon (http://localhost/phpmyadmin)
   - Crea una nueva base de datos llamada `negociosv`

2. **Configurar el archivo `.env`:**
   
   Abre el archivo `negociosv/.env` y modifica estas líneas:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=negociosv
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Ejecutar las migraciones:**
   
   ```bash
   cd negociosv
   php artisan migrate
   ```

### Opción 2: SQLite (Más Simple)

1. **Crear el archivo de base de datos:**
   
   ```bash
   cd negociosv
   New-Item -Path database/database.sqlite -ItemType File
   ```

2. **Configurar el archivo `.env`:**
   
   Abre el archivo `negociosv/.env` y modifica estas líneas:

   ```env
   DB_CONNECTION=sqlite
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=negociosv
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

3. **Ejecutar las migraciones:**
   
   ```bash
   cd negociosv
   php artisan migrate
   ```

## Después de Configurar

Una vez configurada la base de datos, reinicia el servidor de Laravel:

```bash
# Detener el servidor actual (Ctrl+C)
# Luego iniciar de nuevo:
cd negociosv
php artisan serve
```

## Verificar que Funciona

Prueba el endpoint de ping:

```bash
curl http://127.0.0.1:8000/api/ping
```

Deberías ver: `{"message":"API LARAVEL FUNCIONANDO 🚀"}`
