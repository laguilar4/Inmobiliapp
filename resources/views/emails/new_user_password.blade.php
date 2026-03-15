<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Credenciales de acceso</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>

    <p>Se ha creado una cuenta para ti en el sistema Inmobiliapp.</p>

    <p>
        <strong>Correo:</strong> {{ $user->email }}<br>
        <strong>Contraseña temporal:</strong> {{ $password }}
    </p>

    <p>Te recomendamos iniciar sesión y cambiar tu contraseña lo antes posible.</p>

    <p>Saludos,<br>
        Inmobiliapp</p>
</body>
</html>

