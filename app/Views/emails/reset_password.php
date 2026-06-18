<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f5f7fb; margin:0; padding:24px; color:#1f2937;">
    <div style="max-width:620px; margin:0 auto; background:#ffffff; border-radius:16px; padding:28px; border:1px solid #e5e7eb;">
        <h2 style="margin-top:0; color:#0f172a;">Restablecer contraseña</h2>

        <p>Hola <?= esc($nombre) ?>,</p>

        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en Digisalud.</p>

        <p style="margin:28px 0;">
            <a href="<?= esc($link) ?>" style="background:#2563eb; color:#ffffff; padding:12px 20px; border-radius:10px; text-decoration:none; font-weight:bold; display:inline-block;">
                Crear nueva contraseña
            </a>
        </p>

        <p>Este enlace expira en <?= esc($minutos) ?> minutos y solo puede usarse una vez.</p>

        <p>Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña actual seguirá igual.</p>

        <hr style="border:none; border-top:1px solid #e5e7eb; margin:24px 0;">

        <p style="font-size:12px; color:#64748b;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
        <p style="font-size:12px; color:#64748b; word-break:break-all;"><?= esc($link) ?></p>
    </div>
</body>
</html>
