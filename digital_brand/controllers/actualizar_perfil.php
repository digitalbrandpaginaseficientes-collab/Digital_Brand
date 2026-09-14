<?php

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

if (!usuarioAutenticado()) {

    respuestaJson(
        false,
        'Debes iniciar sesión.'
    );
}

if (esAdministrador()) {

    respuestaJson(
        false,
        'Este módulo está disponible para cuentas de cliente.'
    );
}

if (!validarTokenCsrf(
    $_POST['csrf_token'] ?? ''
)) {

    respuestaJson(
        false,
        'La sesión expiró. Recarga la página e inténtalo nuevamente.'
    );
}

$idUsuario = (int) (
    $_SESSION['id_usuario'] ?? 0
);

$nombre = limpiarDato(
    $_POST['nombre'] ?? ''
);

$correo = strtolower(limpiarDato(
    $_POST['correo'] ?? ''
));

$telefono = limpiarDato(
    $_POST['telefono'] ?? ''
);

$empresa = limpiarDato(
    $_POST['empresa'] ?? ''
);

$password = (string) (
    $_POST['password'] ?? ''
);

$password2 = (string) (
    $_POST['password2'] ?? ''
);

if ($idUsuario <= 0) {

    respuestaJson(
        false,
        'La sesión del usuario no es válida.'
    );
}

if (
    mb_strlen($nombre, 'UTF-8') < 3 ||
    mb_strlen($nombre, 'UTF-8') > 100
) {

    respuestaJson(
        false,
        'El nombre debe tener entre 3 y 100 caracteres.'
    );
}

if (
    !preg_match(
        "/^[\p{L}\p{M}\s.'-]+$/u",
        $nombre
    )
) {

    respuestaJson(
        false,
        'El nombre contiene caracteres no permitidos.'
    );
}

if (!validarCorreo($correo)) {

    respuestaJson(
        false,
        'El correo electrónico no es válido.'
    );
}

if (strlen($correo) > 150) {

    respuestaJson(
        false,
        'El correo electrónico es demasiado largo.'
    );
}

if (
    $telefono !== '' &&
    !preg_match(
        '/^[0-9+\s()\-]{7,20}$/',
        $telefono
    )
) {

    respuestaJson(
        false,
        'El teléfono ingresado no es válido.'
    );
}

if (
    mb_strlen($empresa, 'UTF-8') > 120
) {

    respuestaJson(
        false,
        'El nombre de la empresa no puede superar los 120 caracteres.'
    );
}

$cambiarPassword =
    $password !== '' ||
    $password2 !== '';

if ($cambiarPassword) {

    if (strlen($password) < 8) {

        respuestaJson(
            false,
            'La nueva contraseña debe tener mínimo 8 caracteres.'
        );
    }

    if (strlen($password) > 72) {

        respuestaJson(
            false,
            'La nueva contraseña no puede superar los 72 caracteres.'
        );
    }

    if ($password !== $password2) {

        respuestaJson(
            false,
            'Las contraseñas no coinciden.'
        );
    }
}

try {

    $conexion = Database::conectar();

    $buscarUsuario = $conexion->prepare("
        SELECT
            id_usuario,
            nombre,
            estado
        FROM usuarios
        WHERE id_usuario = ?
        LIMIT 1
    ");

    $buscarUsuario->execute([
        $idUsuario
    ]);

    $usuarioActual = $buscarUsuario->fetch();

    if (!$usuarioActual) {

        respuestaJson(
            false,
            'La cuenta del usuario no existe.'
        );
    }

    if (
        isset($usuarioActual['estado']) &&
        $usuarioActual['estado'] !== 'activo'
    ) {

        respuestaJson(
            false,
            'La cuenta se encuentra inactiva.'
        );
    }

    $consultaCorreoDuplicado = $conexion->prepare("
        SELECT id_usuario
        FROM usuarios
        WHERE correo = ?
          AND id_usuario <> ?
        LIMIT 1
    ");

    $consultaCorreoDuplicado->execute([
        $correo,
        $idUsuario
    ]);

    if ($consultaCorreoDuplicado->fetch()) {

        respuestaJson(
            false,
            'Ese correo ya está en uso por otra cuenta.'
        );
    }

    $conexion->beginTransaction();

    if ($cambiarPassword) {

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($hash === false) {

            $conexion->rollBack();

            respuestaJson(
                false,
                'No fue posible proteger la nueva contraseña.'
            );
        }

        $actualizar = $conexion->prepare("
            UPDATE usuarios
            SET
                nombre = ?,
                correo = ?,
                telefono = ?,
                empresa = ?,
                contrasena = ?,
                fecha_actualizacion = CURRENT_TIMESTAMP
            WHERE id_usuario = ?
        ");

        $actualizar->execute([
            $nombre,
            $correo,
            $telefono !== ''
                ? $telefono
                : null,
            $empresa !== ''
                ? $empresa
                : null,
            $hash,
            $idUsuario
        ]);

    } else {

        $actualizar = $conexion->prepare("
            UPDATE usuarios
            SET
                nombre = ?,
                correo = ?,
                telefono = ?,
                empresa = ?,
                fecha_actualizacion = CURRENT_TIMESTAMP
            WHERE id_usuario = ?
        ");

        $actualizar->execute([
            $nombre,
            $correo,
            $telefono !== ''
                ? $telefono
                : null,
            $empresa !== ''
                ? $empresa
                : null,
            $idUsuario
        ]);
    }

    $verificar = $conexion->prepare("
        SELECT
            nombre,
            correo,
            telefono,
            empresa
        FROM usuarios
        WHERE id_usuario = ?
        LIMIT 1
    ");

    $verificar->execute([
        $idUsuario
    ]);

    $usuarioActualizado =
        $verificar->fetch();

    if (
        !$usuarioActualizado ||
        $usuarioActualizado['nombre'] !== $nombre ||
        $usuarioActualizado['correo'] !== $correo
    ) {

        $conexion->rollBack();

        respuestaJson(
            false,
            'La base de datos no conservó correctamente los cambios.'
        );
    }

    $conexion->commit();

    $_SESSION['nombre'] = $nombre;
    $_SESSION['correo'] = $correo;

    respuestaJson(
        true,
        $cambiarPassword
            ? 'La información y la contraseña se actualizaron correctamente.'
            : 'La información se actualizó correctamente.'
    );

} catch (PDOException $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {

        $conexion->rollBack();
    }

    error_log(
        'Error actualizando perfil: ' .
        $error->getMessage()
    );

    if ($error->getCode() === '23000') {

        respuestaJson(
            false,
            'Ese correo ya está en uso por otra cuenta.'
        );
    }

    respuestaJson(
        false,
        'Ocurrió un error al actualizar la información.'
    );

} catch (Throwable $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {

        $conexion->rollBack();
    }

    error_log(
        'Error interno actualizando perfil: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error interno.'
    );
}