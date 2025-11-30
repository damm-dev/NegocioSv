# Leer el archivo .env.example original
$content = Get-Content .env.example

# Crear nuevo contenido con MySQL configurado
$newContent = @()
$inDBSection = $false

foreach($line in $content) {
    # Detectar inicio de sección de base de datos
    if($line -match '^DB_CONNECTION=') {
        $inDBSection = $true
        # Agregar configuración de MySQL
        $newContent += 'DB_CONNECTION=mysql'
        $newContent += 'DB_HOST=127.0.0.1'
        $newContent += 'DB_PORT=3306'
        $newContent += 'DB_DATABASE=negociosv'
        $newContent += 'DB_USERNAME=root'
        $newContent += 'DB_PASSWORD='
        continue
    }
    
    # Saltar líneas de DB hasta encontrar una línea que no sea DB_
    if($inDBSection -and $line -match '^(DB_|#.*DB_)') {
        continue
    }
    
    # Si encontramos una línea que no es DB_, salimos de la sección
    if($inDBSection -and $line -notmatch '^(DB_|#.*DB_|\s*$)') {
        $inDBSection = $false
    }
    
    # Agregar la línea si no estamos en sección DB
    if(!$inDBSection) {
        $newContent += $line
    }
}

# Guardar el nuevo contenido
$newContent | Set-Content .env
Write-Host "Archivo .env configurado para MySQL correctamente"
