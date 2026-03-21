<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código QR de Visita</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .info-table td:first-child { font-weight: bold; color: #555; width: 40%; }
        .qr-wrapper { text-align: center; margin: 24px 0; }
        .footer { margin-top: 20px; font-size: 13px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Solicitud de Visita - Inmobiliapp</h2>
        <p>Hola <strong>{{ $visitante->nombre }}</strong>, se ha registrado una solicitud de visita a tu nombre.</p>
        <p>Presenta este código QR al personal de seguridad al momento de ingresar.</p>

        <table class="info-table">
            <tr>
                <td>Nombre:</td>
                <td>{{ $visitante->nombre }}</td>
            </tr>
            <tr>
                <td>Cédula:</td>
                <td>{{ $visitante->cedula }}</td>
            </tr>
            <tr>
                <td>Correo:</td>
                <td>{{ $visitante->correo }}</td>
            </tr>
            <tr>
                <td>Proyecto:</td>
                <td>{{ $cabecera->proyecto->nombre ?? '—' }}</td>
            </tr>
            <tr>
                <td>Fecha de inicio:</td>
                <td>{{ $cabecera->fecha_inicio->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Fecha de fin:</td>
                <td>{{ $cabecera->fecha_fin->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <div class="qr-wrapper">
            <img src="data:image/svg+xml;base64,{{ $qrBase64 }}"
                 width="250" height="250"
                 alt="Código QR de visita"
                 style="display:block; margin:0 auto;">
        </div>

        <div class="footer">
            <p>Este código QR es personal e intransferible. No lo compartas con otras personas.</p>
            <p>Saludos,<br>Inmobiliapp</p>
        </div>
    </div>
</body>
</html>
