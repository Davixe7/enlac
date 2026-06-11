<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación a Capacitación ENLAC</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e1e8ed;">

        <tr>
            <td style="background-color: #ffffff; padding: 0; text-align: center;">
                <img src="URL_DE_TU_IMAGEN_AQUI" alt="Instituto ENLAC" width="100%" style="max-width: 600px; height: auto; display: block; border: 0;">
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 40px;">
                <h2 style="color: #002855; margin-top: 0; font-size: 22px; font-weight: 700; border-bottom: 2px solid #e2effa; padding-bottom: 10px;">
                    ¡Invitación Especial!
                </h2>

                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">Buen día,</p>
                <p style="font-size: 16px; line-height: 1.6; color: #4a5568;">Te notificamos que has sido invitado a la próxima <strong>Capacitación ENLAC</strong>. A continuación, te compartimos los detalles del evento:</p>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-left: 4px solid #002855; border-radius: 4px; margin: 25px 0; padding: 20px;">
                    <tr>
                        <td style="font-size: 15px; line-height: 2; color: #2d3748;">
                            <strong style="color: #002855;">Capacitación:</strong> {{ $capacitation->name }}<br>
                            <strong style="color: #002855;">Fecha:</strong> {{ $capacitation->date }}<br>
                            <strong style="color: #002855;">Horario:</strong> de {{ $capacitation->start_time }} a {{ $capacitation->end_time }}<br>
                            <strong style="color: #002855;">Lugar:</strong> {{ $capacitation->location }}
                            @if($capacitation->description)
                                <br><strong style="color: #002855;">Descripción:</strong> {{ $capacitation->description }}
                            @endif
                        </td>
                    </tr>
                </table>

                <p style="font-size: 16px; font-weight: bold; color: #002855; margin-bottom: 0; text-align: center; background-color: #e2effa; padding: 12px; border-radius: 6px;">
                    ¡Te esperamos!
                </p>
            </td>
        </tr>

        <tr>
            <td style="background-color: #002855; padding: 20px; text-align: center; color: #ffffff; font-size: 12px; font-weight: 300; letter-spacing: 0.5px;">
                © {{ date('Y') }} Instituto ENLAC. Todos los derechos reservados.<br>
                <span style="opacity: 0.7;">Este es un correo automático, por favor no respondas a este mensaje.</span>
            </td>
        </tr>
    </table>

</body>
</html>
