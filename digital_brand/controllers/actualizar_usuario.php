<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJson(false, 'Método no permitido.');
}

if (!usuarioAutenticado()) {
    respuestaJson(false, 'Debes iniciar sesión.');
}

if (!esAdministrador()) {
    respuestaJson(false, 'No tienes permisos de administrador.');
}

if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
    respuestaJson(
        false,
        'La sesión expiró. Recarga la página.'
    );
}

$idUsuario = filter_input(
    INPUT_POST,
    'id_usuario',
    FILTER_VALIDATE_INT
);

$accion = trim($_POST['accion'] ?? '');
$valor = trim($_POST['valor'] ?? '');

if (!$idUsuario || $idUsuario < 1) {
    respuestaJson(false, 'El usuario seleccionado no es válido.');
}

if (
    (int) $idUsuario === (int) $_SESSION['id_usuario'] &&
    $accion !== 'editar_datos'
) {
    respuestaJson(
        false,
        'No puedes modificar tu propia cuenta desde este módulo.'
    );
}

try {

    $conexion = Database::conectar();

    /*
    |--------------------------------------------------------------------------
    | Confirmar qué base de datos utiliza PHP
    |--------------------------------------------------------------------------
    */

    $baseActual = $conexion
        ->query('SELECT DATABASE()')
        ->fetchColumn();

    if ($baseActual !== DB_NAME) {
        respuestaJson(
            false,
            'PHP está conectado a una base de datos incorrecta: ' .
            $baseActual
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Buscar usuario antes del cambio
    |--------------------------------------------------------------------------
    */

    $consultaUsuario = $conexion->prepare("
        SELECT
            id_usuario,
            nombre,
            correo,
            rol,
            estado
        FROM usuarios
        WHERE id_usuario = :id_usuario
        LIMIT 1
    ");

    $consultaUsuario->execute([
        ':id_usuario' => $idUsuario
    ]);

    $usuarioAntes = $consultaUsuario->fetch();

    if (!$usuarioAntes) {
        respuestaJson(false, 'El usuario no existe.');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambiar rol
    |--------------------------------------------------------------------------
    */

    if ($accion === 'cambiar_rol') {

        $rolesPermitidos = [
            'cliente',
            'administrador'
        ];

        if (!in_array($valor, $rolesPermitidos, true)) {
            respuestaJson(false, 'El rol seleccionado no es válido.');
        }

        $conexion->beginTransaction();

        $actualizar = $conexion->prepare("
            UPDATE usuarios
            SET
                rol = :rol,
                fecha_actualizacion = CURRENT_TIMESTAMP
            WHERE id_usuario = :id_usuario
        ");

        $actualizar->execute([
            ':rol' => $valor,
            ':id_usuario' => $idUsuario
        ]);

        $filasAfectadas = $actualizar->rowCount();

        $verificar = $conexion->prepare("
            SELECT rol
            FROM usuarios
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ");

        $verificar->execute([
            ':id_usuario' => $idUsuario
        ]);

        $rolDespues = $verificar->fetchColumn();

        if ($rolDespues !== $valor) {

            $conexion->rollBack();

            respuestaJson(
                false,
                'MySQL no conservó el nuevo rol.',
                [
                    'base_datos' => $baseActual,
                    'rol_antes' => $usuarioAntes['rol'],
                    'rol_solicitado' => $valor,
                    'rol_despues' => $rolDespues,
                    'filas_afectadas' => $filasAfectadas
                ]
            );
        }

        $conexion->commit();

        respuestaJson(
            true,
            'Rol actualizado de "' .
            $usuarioAntes['rol'] .
            '" a "' .
            $rolDespues .
            '".',
            [
                'base_datos' => $baseActual,
                'id_usuario' => (int) $idUsuario,
                'rol_antes' => $usuarioAntes['rol'],
                'rol_despues' => $rolDespues,
                'filas_afectadas' => $filasAfectadas
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cambiar estado
    |--------------------------------------------------------------------------
    */

    if ($accion === 'cambiar_estado') {

        $estadosPermitidos = [
            'activo',
            'inactivo'
        ];

        if (!in_array($valor, $estadosPermitidos, true)) {
            respuestaJson(false, 'El estado seleccionado no es válido.');
        }

        $conexion->beginTransaction();

        $actualizar = $conexion->prepare("
            UPDATE usuarios
            SET
                estado = :estado,
                fecha_actualizacion = CURRENT_TIMESTAMP
            WHERE id_usuario = :id_usuario
        ");

        $actualizar->execute([
            ':estado' => $valor,
            ':id_usuario' => $idUsuario
        ]);

        $filasAfectadas = $actualizar->rowCount();

        $verificar = $conexion->prepare("
            SELECT estado
            FROM usuarios
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ");

        $verificar->execute([
            ':id_usuario' => $idUsuario
        ]);

        $estadoDespues = $verificar->fetchColumn();

        if ($estadoDespues !== $valor) {

            $conexion->rollBack();

            respuestaJson(
                false,
                'MySQL no conservó el nuevo estado.',
                [
                    'base_datos' => $baseActual,
                    'estado_antes' => $usuarioAntes['estado'],
                    'estado_solicitado' => $valor,
                    'estado_despues' => $estadoDespues,
                    'filas_afectadas' => $filasAfectadas
                ]
            );
        }

        $conexion->commit();

        respuestaJson(
            true,
            'Estado actualizado de "' .
            $usuarioAntes['estado'] .
            '" a "' .
            $estadoDespues .
            '".',
            [
                'base_datos' => $baseActual,
                'id_usuario' => (int) $idUsuario,
                'estado_antes' => $usuarioAntes['estado'],
                'estado_despues' => $estadoDespues,
                'filas_afectadas' => $filasAfectadas
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Editar datos (nombre, correo, teléfono, empresa)
    |--------------------------------------------------------------------------
    */

    if ($accion === 'editar_datos') {

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

        if (mb_strlen($empresa, 'UTF-8') > 120) {
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

        $consultaCorreoDuplicado = $conexion->prepare("
            SELECT id_usuario
            FROM usuarios
            WHERE correo = :correo
              AND id_usuario <> :id_usuario
            LIMIT 1
        ");

        $consultaCorreoDuplicado->execute([
            ':correo' => $correo,
            ':id_usuario' => $idUsuario
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
                    nombre = :nombre,
                    correo = :correo,
                    telefono = :telefono,
                    empresa = :empresa,
                    contrasena = :contrasena,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id_usuario = :id_usuario
            ");

            $actualizar->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':telefono' => $telefono !== '' ? $telefono : null,
                ':empresa' => $empresa !== '' ? $empresa : null,
                ':contrasena' => $hash,
                ':id_usuario' => $idUsuario
            ]);

        } else {

            $actualizar = $conexion->prepare("
                UPDATE usuarios
                SET
                    nombre = :nombre,
                    correo = :correo,
                    telefono = :telefono,
                    empresa = :empresa,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id_usuario = :id_usuario
            ");

            $actualizar->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':telefono' => $telefono !== '' ? $telefono : null,
                ':empresa' => $empresa !== '' ? $empresa : null,
                ':id_usuario' => $idUsuario
            ]);
        }

        $verificar = $conexion->prepare("
            SELECT nombre, correo
            FROM usuarios
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ");

        $verificar->execute([
            ':id_usuario' => $idUsuario
        ]);

        $datosDespues = $verificar->fetch();

        if (
            !$datosDespues ||
            $datosDespues['nombre'] !== $nombre ||
            $datosDespues['correo'] !== $correo
        ) {

            $conexion->rollBack();

            respuestaJson(
                false,
                'MySQL no conservó los cambios realizados.'
            );
        }

        $conexion->commit();

        if ((int) $idUsuario === (int) $_SESSION['id_usuario']) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['correo'] = $correo;
        }

        respuestaJson(
            true,
            $cambiarPassword
                ? 'Información y contraseña del usuario actualizadas correctamente.'
                : 'Información del usuario actualizada correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar usuario
    |--------------------------------------------------------------------------
    */

    if ($accion === 'eliminar') {

        $eliminar = $conexion->prepare("
            DELETE FROM usuarios
            WHERE id_usuario = :id_usuario
        ");

        $eliminar->execute([
            ':id_usuario' => $idUsuario
        ]);

        if ($eliminar->rowCount() !== 1) {
            respuestaJson(false, 'El usuario no pudo eliminarse.');
        }

        respuestaJson(
            true,
            'Usuario eliminado correctamente.'
        );
    }

    respuestaJson(false, 'Acción no reconocida.');

} catch (PDOException $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    error_log(
        'Error al actualizar usuario: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Error de base de datos: ' . $error->getMessage()
    );

} catch (Throwable $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    error_log(
        'Error interno al actualizar usuario: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Error interno: ' . $error->getMessage()
    );
}