<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 40px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 30px 20px; }
        .credentials-box { background: #f8f9fa; border: 2px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .credential-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
        .credential-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #666; }
        .value { color: #333; font-family: 'Courier New', monospace; background: #fff; padding: 4px 8px; border-radius: 4px; }
        .button { display: inline-block; background: #667eea; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .button:hover { background: #5568d3; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🎉</div>
            <h1>¡Bienvenido al Sistema!</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            
            <p>Tu cuenta ha sido creada exitosamente. A continuación encontrarás tus credenciales de acceso al sistema:</p>
            
            <div class="credentials-box">
                <h3 style="margin-top: 0; color: #667eea;">📋 Credenciales de Acceso</h3>
                
                <div class="credential-row">
                    <span class="label">Usuario / Email:</span>
                    <span class="value">{{ $user->email }}</span>
                </div>
                
                <div class="credential-row">
                    <span class="label">Contraseña:</span>
                    <span class="value">{{ $password }}</span>
                </div>
                
                <div class="credential-row">
                    <span class="label">URL del Sistema:</span>
                    <span class="value">{{ config('app.url') }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/login" class="button">
                    🔐 Acceder al Sistema
                </a>
            </div>

            <div class="warning">
                <strong>⚠️ Importante:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Guarda esta contraseña en un lugar seguro</li>
                    <li>Te recomendamos cambiar tu contraseña después del primer inicio de sesión</li>
                    <li>No compartas tus credenciales con nadie</li>
                    <li>Si no solicitaste esta cuenta, contacta al administrador</li>
                </ul>
            </div>

            @if($user->roles->isNotEmpty())
            <div style="margin: 20px 0; padding: 15px; background: #e7f3ff; border-radius: 4px;">
                <strong>👤 Rol asignado:</strong> 
                <span style="color: #0066cc;">{{ $user->roles->first()->name }}</span>
            </div>
            @endif

            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar al administrador del sistema.
            </p>

            <p style="color: #666; font-size: 14px;">
                ¡Esperamos que disfrutes usando el sistema!
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            <p>Este correo fue enviado automáticamente, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
