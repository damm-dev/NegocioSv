# 📊 Cómo Ver tu Base de Datos en HeidiSQL

## ✅ PROBLEMAS RESUELTOS:

1. **Error MissingAppKeyException** - ✓ SOLUCIONADO
   - Se generó la clave de aplicación con `php artisan key:generate`
   - El servidor se reinició automáticamente

2. **Error: Tabla 'sessions' no existe** - ✓ SOLUCIONADO
   - Se creó la migración de sesiones con `php artisan session:table`
   - Se ejecutó la migración con `php artisan migrate`
   - Tabla `sessions` creada exitosamente

3. **Servidor funcionando correctamente** - ✓ VERIFICADO
   - URL: http://127.0.0.1:8000
   - Estado: Activo y respondiendo (HTTP 200)

## Pasos para conectarte a HeidiSQL:

### 1. En la ventana de HeidiSQL que tienes abierta:
   - ✅ Ya tienes la sesión "Laragon.MySQL" seleccionada
   - ✅ Los datos están correctos:
     - Host: 127.0.0.1
     - Usuario: root
     - Puerto: 3306

### 2. Haz click en el botón **"Abrir"** (abajo a la izquierda)
   - NO hagas click en "Guardar" ni "Cancelar"
   - Haz click en **"Abrir"** para conectarte

### 3. Una vez conectado:
   - En el panel izquierdo verás una lista de bases de datos
   - Busca y haz click en **"negociosv"**
   - Se expandirá mostrando las tablas

### 4. Para ver los datos de una tabla:
   - Haz click en el símbolo "+" junto a "negociosv"
   - Verás las tablas: usuarios, categorias, departamentos, etc.
   - Haz doble click en cualquier tabla para ver sus datos

---

## Si no ves la base de datos después de conectarte:

### Opción A: Refrescar la lista
1. Click derecho en el panel izquierdo
2. Selecciona "Refrescar"

### Opción B: Cerrar y volver a abrir HeidiSQL
1. Cierra HeidiSQL completamente
2. Abre Laragon
3. Click en "Database" o "HeidiSQL"
4. Haz click en "Abrir"

---

## Alternativa: Ver la BD desde el navegador

Si HeidiSQL te da problemas, puedes usar el navegador:

1. Abre tu navegador
2. Ve a: **http://localhost/phpmyadmin** (si tienes phpMyAdmin instalado)
3. O instala Adminer (más ligero):
   - Descarga: https://www.adminer.org/
   - Coloca el archivo en: `C:\laragon\www\adminer.php`
   - Abre: http://localhost/adminer.php
   - Conecta con:
     - Sistema: MySQL
     - Servidor: 127.0.0.1
     - Usuario: root
     - Contraseña: (dejar vacío)
     - Base de datos: negociosv

---

## Verificar que la BD existe (desde terminal):

```bash
# Ver todas las bases de datos
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "SHOW DATABASES;"

# Ver tablas de negociosv
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE negociosv; SHOW TABLES;"

# Ver datos de usuarios
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe" -u root -e "USE negociosv; SELECT * FROM usuarios;"
```

---

## ✅ Confirmación: Tu base de datos SÍ existe

Bases de datos disponibles:
- ✅ **negociosv** ← Esta es tu base de datos
- information_schema
- mysql
- performance_schema
- sys

Tablas en negociosv:
- ✅ categorias (6 registros)
- ✅ departamentos (6 registros)
- ✅ estados_usuario
- ✅ intereses
- ✅ metodos_pago
- ✅ migrations
- ✅ municipios
- ✅ negocio_categoria
- ✅ negocio_metodo_pago
- ✅ negocios
- ✅ perfiles
- ✅ personal_access_tokens
- ✅ terminos
- ✅ usuarios (1 registro)

---

## 🎯 Resumen:

**El problema NO es que la BD no exista**, sino que necesitas:
1. Hacer click en **"Abrir"** en HeidiSQL para conectarte
2. Luego buscar "negociosv" en el panel izquierdo
3. Hacer click en el "+" para expandir y ver las tablas

¡La base de datos está ahí y funcionando correctamente! 🎉
