<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar cuenta</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>

    <p>Para activar tu cuenta en Inmobiliapp, confirma tu correo haciendo clic en el siguiente enlace:</p>

    <p>
        <a href="{{ $confirmationUrl }}">Confirmar mi cuenta</a>
    </p>

    <p>Este enlace caduca en 7 días.</p>

    <p>Si no creaste esta cuenta, puedes ignorar este mensaje.</p>

    <p>Saludos,<br>Inmobiliapp</p>
</body>
</html>
