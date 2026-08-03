<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $asunto }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .email-header {
            background-color: #550000;
            color: #ffffff;
            padding: 24px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .email-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .email-body {
            padding: 30px;
            line-height: 1.6;
            font-size: 15px;
        }
        .email-body h2 {
            font-size: 18px;
            color: #550000;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .message-content {
            background-color: #f8fafc;
            border-left: 4px solid #550000;
            padding: 16px 20px;
            border-radius: 4px;
            white-space: pre-wrap;
            margin-bottom: 24px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Sistema Bonos de Vivienda</h1>
            <p>Notificación Oficial</p>
        </div>
        <div class="email-body">
            <h2>{{ $asunto }}</h2>
            <div class="message-content">
                {{ $mensaje }}
            </div>
            <p>Si tiene alguna consulta o duda respecto a esta notificación, por favor comuníquese con nuestras oficinas.</p>
        </div>
        <div class="email-footer">
            <p>Este es un correo automático enviado desde el <strong>Sistema Bonos de Vivienda Andrea Rojas</strong>.</p>
            <p>Por favor no responda directamente a este correo.</p>
        </div>
    </div>
</body>
</html>
