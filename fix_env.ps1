$content = Get-Content .env
$newContent = @()

foreach($line in $content) {
    # Saltar todas las líneas relacionadas con PostgreSQL
    if($line -match '^DB_CONNECTION=pgsql' -or 
       $line -match '^#?DB_PORT=5432' -or 
       $line -match '^#?DB_USERNAME=postgres' -or
       ($line -match '^DB_HOST=127\.0\.0\.1' -and $newContent[-1] -match 'pgsql') -or
       ($line -match '^#?DB_DATABASE=negociosv' -and $newContent[-1] -match '5432') -or
       ($line -match '^#?DB_PASSWORD=\s*$' -and $newContent[-1] -match 'postgres')) {
        continue
    }
    
    # Descomentar líneas de MySQL
    if($line -match '^# DB_CONNECTION=mysql') {
        $newContent += 'DB_CONNECTION=mysql'
    }
    elseif($line -match '^# DB_HOST=127\.0\.0\.1') {
        $newContent += 'DB_HOST=127.0.0.1'
    }
    elseif($line -match '^# DB_PORT=3306') {
        $newContent += 'DB_PORT=3306'
    }
    elseif($line -match '^# DB_DATABASE=negociosv') {
        $newContent += 'DB_DATABASE=negociosv'
    }
    elseif($line -match '^# DB_USERNAME=root') {
        $newContent += 'DB_USERNAME=root'
    }
    elseif($line -match '^# DB_PASSWORD=\s*$') {
        $newContent += 'DB_PASSWORD='
    }
    else {
        $newContent += $line
    }
}

$newContent | Set-Content .env
Write-Host "Archivo .env actualizado correctamente para MySQL"
