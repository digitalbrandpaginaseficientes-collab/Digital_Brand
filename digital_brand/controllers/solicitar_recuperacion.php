<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJson(
        false,
        'Método no permitido.'
    );
}

if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
    respuestaJson(
        false,
        'La sesión expiró. Recarga la página e inténtalo nuevamente.'
    );
}

$correo = mb_strtolower(
    trim($_POST['correo'] ?? ''),
    'UTF-8'
);

if (
    $correo === '' ||
    !filter_var($correo, FILTER_VALIDATE_EMAIL)
) {
    respuestaJson(
        false,
        'Escribe un correo electrónico válido.'
    );
}

if (mb_strlen($correo, 'UTF-8') > 150) {
    respuestaJson(
        false,
        'El correo electrónico es demasiado largo.'
    );
}

/*
|--------------------------------------------------------------------------
| Mensaje genérico
|--------------------------------------------------------------------------
|
| Se utiliza el mismo mensaje exista o no la cuenta. Así no revelamos
| públicamente qué correos están registrados en el sistema.
|
*/

$mensajeGenerico =
    'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.';

try {

    $conexion = Database::conectar();

    /*
    |--------------------------------------------------------------------------
    | Buscar usuario
    |--------------------------------------------------------------------------
    */

    $consultaUsuario = $conexion->prepare("
        SELECT
            id_usuario,
            nombre,
            correo,
            estado
        FROM usuarios
        WHERE LOWER(correo) = LOWER(?)
        LIMIT 1
    ");

    $consultaUsuario->execute([
        $correo
    ]);

    $usuario = $consultaUsuario->fetch();

    /*
    |--------------------------------------------------------------------------
    | No revelar si el correo existe
    |--------------------------------------------------------------------------
    */

    if (!$usuario) {

        /*
        | Pequeña espera para reducir diferencias de tiempo entre
        | correos existentes y no existentes.
        */

        usleep(250000);

        respuestaJson(
            true,
            $mensajeGenerico
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validar estado de la cuenta
    |--------------------------------------------------------------------------
    */

    if (
        isset($usuario['estado']) &&
        $usuario['estado'] !== 'activo'
    ) {

        respuestaJson(
            true,
            $mensajeGenerico
        );
    }

    $idUsuario = (int) $usuario['id_usuario'];

    /*
    |--------------------------------------------------------------------------
    | Evitar solicitudes excesivas
    |--------------------------------------------------------------------------
    |
    | Solo permitimos generar un enlace nuevo si no se creó otro durante
    | los últimos dos minutos.
    |
    */

    $consultaReciente = $conexion->prepare("
        SELECT fecha_creacion
        FROM recuperacion_contrasenas
        WHERE id_usuario = ?
          AND utilizado = 0
          AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)
        ORDER BY fecha_creacion DESC
        LIMIT 1
    ");

    $consultaReciente->execute([
        $idUsuario
    ]);

    if ($consultaReciente->fetch()) {

        respuestaJson(
            true,
            'Ya se generó un enlace recientemente. Revisa tu correo o espera dos minutos antes de solicitar otro.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generar token seguro
    |--------------------------------------------------------------------------
    |
    | El token original se envía al usuario.
    | En la base de datos solamente se guarda su hash SHA-256.
    |
    */

    $token = bin2hex(
        random_bytes(32)
    );

    $tokenHash = hash(
        'sha256',
        $token
    );

    $fechaExpiracion = date(
        'Y-m-d H:i:s',
        time() + (30 * 60)
    );

    $conexion->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Invalidar enlaces anteriores
    |--------------------------------------------------------------------------
    */

    $invalidarAnteriores = $conexion->prepare("
        UPDATE recuperacion_contrasenas
        SET utilizado = 1
        WHERE id_usuario = ?
          AND utilizado = 0
    ");

    $invalidarAnteriores->execute([
        $idUsuario
    ]);

    /*
    |--------------------------------------------------------------------------
    | Guardar el nuevo token
    |--------------------------------------------------------------------------
    */

    $insertarToken = $conexion->prepare("
        INSERT INTO recuperacion_contrasenas (
            id_usuario,
            token_hash,
            fecha_expiracion,
            utilizado
        )
        VALUES (?, ?, ?, 0)
    ");

    $insertarToken->execute([
        $idUsuario,
        $tokenHash,
        $fechaExpiracion
    ]);

    $conexion->commit();

    /*
    |--------------------------------------------------------------------------
    | Construir enlace temporal
    |--------------------------------------------------------------------------
    */

    $urlRecuperacion =
        APP_URL .
        '/restablecer_contrasena.php?token=' .
        rawurlencode($token);

    /*
    |--------------------------------------------------------------------------
    | Preparar correo
    |--------------------------------------------------------------------------
    */

    $nombreUsuario = trim(
        $usuario['nombre'] ?? 'Usuario'
    );

    $asunto =
        'Recuperación de contraseña - Digital Brand';

    $mensajeHtml = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Recuperación de contraseña</title>
        </head>
        <body style="
            margin:0;
            padding:30px;
            background:#f2f7f3;
            font-family:Arial,Helvetica,sans-serif;
            color:#263238;
        ">

            <div style="
                max-width:620px;
                margin:0 auto;
                padding:35px;
                background:#ffffff;
                border-radius:18px;
                border:1px solid #dce9de;
            ">

                <div style="
                    width:65px;
                    height:65px;
                    margin-bottom:22px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    border-radius:18px;
                    background:#e8f5e9;
                    color:#2e7d32;
                    font-size:22px;
                    font-weight:bold;
                ">
                    DB
                </div>

                <h1 style="
                    margin:0 0 15px;
                    color:#1b5e20;
                    font-size:25px;
                ">
                    Restablecer contraseña
                </h1>

                <p style="line-height:1.7;">
                    Hola ' . htmlspecialchars(
                        $nombreUsuario,
                        ENT_QUOTES,
                        'UTF-8'
                    ) . ':
                </p>

                <p style="line-height:1.7;">
                    Recibimos una solicitud para cambiar la contraseña
                    de tu cuenta en Digital Brand.
                </p>

                <p style="
                    margin:28px 0;
                    text-align:center;
                ">

                    <a
                        href="' . htmlspecialchars(
                            $urlRecuperacion,
                            ENT_QUOTES,
                            'UTF-8'
                        ) . '"
                        style="
                            display:inline-block;
                            padding:14px 25px;
                            border-radius:10px;
                            background:#2e7d32;
                            color:#ffffff;
                            text-decoration:none;
                            font-weight:bold;
                        "
                    >
                        Crear nueva contraseña
                    </a>

                </p>

                <p style="line-height:1.7;">
                    Este enlace vence en <strong>30 minutos</strong>
                    y solo puede utilizarse una vez.
                </p>

                <p style="line-height:1.7;">
                    Si no solicitaste este cambio, puedes ignorar este
                    mensaje. Tu contraseña actual continuará funcionando.
                </p>

                <hr style="
                    margin:28px 0;
                    border:0;
                    border-top:1px solid #e3ece5;
                ">

                <p style="
                    margin:0;
                    color:#66756b;
                    font-size:13px;
                    line-height:1.6;
                ">
                    Digital Brand<br>
                    Páginas eficientes para empresas que rinden.
                </p>

            </div>

        </body>
        </html>
    ';

    /*
    |--------------------------------------------------------------------------
    | Encabezados del correo
    |--------------------------------------------------------------------------
    */

    $dominioRemitente = 'localhost';

    if (!empty($_SERVER['HTTP_HOST'])) {

        $dominioRemitente = preg_replace(
            '/:\d+$/',
            '',
            $_SERVER['HTTP_HOST']
        );
    }

    $correoRemitente =
        'no-reply@' .
        $dominioRemitente;

    $encabezados = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Digital Brand <' . $correoRemitente . '>',
        'Reply-To: DigitalBrandpaginaseficientes@gmail.com',
        'X-Mailer: PHP/' . phpversion()
    ];

    /*
    |--------------------------------------------------------------------------
    | Intentar enviar el correo
    |--------------------------------------------------------------------------
    |
    | En un servidor con SMTP configurado, mail() enviará el mensaje.
    | En XAMPP normalmente no se envía hasta configurar SMTP.
    |
    */

    $correoEnviado = @mail(
        $usuario['correo'],
        $asunto,
        $mensajeHtml,
        implode("\r\n", $encabezados)
    );

    /*
    |--------------------------------------------------------------------------
    | Entorno local
    |--------------------------------------------------------------------------
    |
    | Como XAMPP normalmente no tiene un servidor de correo configurado,
    | devolvemos el enlace de prueba únicamente cuando la aplicación se
    | ejecuta en localhost.
    |
    */

    $hostActual = mb_strtolower(
        $_SERVER['HTTP_HOST'] ?? '',
        'UTF-8'
    );

    $esEntornoLocal =
        str_contains($hostActual, 'localhost') ||
        str_contains($hostActual, '127.0.0.1');

    if ($esEntornoLocal) {

        /*
        | También queda registrado en el log de PHP/Apache.
        */

        error_log(
            'Digital Brand - Enlace de recuperación para ' .
            $usuario['correo'] .
            ': ' .
            $urlRecuperacion
        );

        respuestaJson(
            true,
            $correoEnviado
                ? 'El enlace de recuperación fue enviado correctamente.'
                : 'Solicitud procesada. En el entorno local puedes utilizar el enlace de prueba.',
            [
                'correo_enviado' => $correoEnviado,
                'modo_local' => true,
                'url_prueba' => $urlRecuperacion,
                'expira' => $fechaExpiracion
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Producción
    |--------------------------------------------------------------------------
    */

    if (!$correoEnviado) {

        error_log(
            'No fue posible enviar el correo de recuperación a: ' .
            $usuario['correo']
        );

        /*
        | No mostramos el error técnico ni revelamos información
        | sensible al visitante.
        */

        respuestaJson(
            true,
            $mensajeGenerico
        );
    }

    respuestaJson(
        true,
        'El enlace de recuperación fue enviado. Revisa tu bandeja de entrada y la carpeta de correo no deseado.'
    );

} catch (PDOException $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    error_log(
        'Error de base de datos solicitando recuperación: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al procesar la solicitud.'
    );

} catch (Throwable $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    error_log(
        'Error interno solicitando recuperación: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error interno al procesar la solicitud.'
    );
}