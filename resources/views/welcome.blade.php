<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mozzo React en Laravel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite('resources/css/custom.css') 
    
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
</head>
<body>
    <div id="app">
        {/* Aquí se montará tu componente HeroSection.jsx */}
    </div>
</body>
</html>