<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backend Laravel - NegocioSv</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 800px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.2em;
        }
        .status {
            background: #10b981;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .info-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        .info-card p {
            color: #666;
            line-height: 1.6;
        }
        .endpoints {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .endpoints h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .endpoint {
            background: white;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 3px solid #10b981;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .method {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            margin-right: 10px;
            font-size: 0.8em;
        }
        .get { background: #3b82f6; color: white; }
        .post { background: #10b981; color: white; }
        .put { background: #f59e0b; color: white; }
        .delete { background: #ef4444; color: white; }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Backend Laravel Funcionando</h1>
        <p class="subtitle">API REST para NegocioSv</p>
        
        <div class="status">
            ✓ Servidor Activo
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h3>📊 Base de Datos</h3>
                <p><strong>MySQL</strong><br>
                Base de datos: negociosv<br>
                Estado: Conectada ✓</p>
            </div>
            
            <div class="info-card">
                <h3>📦 Datos Disponibles</h3>
                <p>
                    • 1 Usuario<br>
                    • 6 Categorías<br>
                    • 6 Departamentos<br>
                    • Estados y términos
                </p>
            </div>
            
            <div class="info-card">
                <h3>🔧 Framework</h3>
                <p><strong>Laravel {{ app()->version() }}</strong><br>
                PHP {{ PHP_VERSION }}<br>
                Entorno: {{ config('app.env') }}</p>
            </div>
            
            <div class="info-card">
                <h3>🌐 URL Base API</h3>
                <p><strong>{{ url('/api') }}</strong><br>
                Listo para conectar tu frontend</p>
            </div>
        </div>

        <div class="endpoints">
            <h3>🔌 Endpoints Principales</h3>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <span>/api/registrar</span>
            </div>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <span>/api/login</span>
            </div>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <span>/api/logout</span>
            </div>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span>/api/perfil</span>
            </div>
            
            <div class="endpoint">
                <span class="method put">PUT</span>
                <span>/api/perfil</span>
            </div>
            
            <div class="endpoint">
                <span class="method post">POST</span>
                <span>/api/registrar_negocio</span>
            </div>
            
            <div class="endpoint">
                <span class="method get">GET</span>
                <span>/api/ping</span>
            </div>
        </div>

        <div class="footer">
            <p>✨ Todo está configurado y listo para usar ✨</p>
            <p style="margin-top: 10px;">Revisa <strong>GUIA_MYSQL_LARAGON.md</strong> para más información</p>
        </div>
    </div>
</body>
</html>
