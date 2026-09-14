<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJson(
        false,
        'Método no permitido.'
    );
}

$tokenCsrf = $_POST['csrf_token'] ?? '';

if (!validarTokenCsrf($tokenCsrf)) {
    respuestaJson(
        false,
        'La sesión del formulario expiró. Recarga la página.'
    );
}

$correo = strtolower(
    limpiarDato($_POST['correo'] ?? '')
);

$contrasena = $_POST['contrasena'] ?? '';

if ($correo === '' || $contrasena === '') {
    respuestaJson(
        false,
        'Completa el correo y la contraseña.'
    );
}

if (!validarCorreo($correo)) {
    respuestaJson(
        false,
        'El correo electrónico no es válido.'
    );
}

try {

    $conexion = Database::conectar();

    $consulta = $conexion->prepare(
        "SELECT
            id_usuario,
            nombre,
            correo,
            contrasena,
            rol,
            estado
         FROM usuarios
         WHERE correo = ?
         LIMIT 1"
    );

    $consulta->execute([$correo]);

    $usuario = $consulta->fetch();

    if (
        !$usuario ||
        !password_verify(
            $contrasena,
            $usuario['contrasena']
        )
    ) {
        respuestaJson(
            false,
            'Correo o contraseña incorrectos.'
        );
    }

    if ($usuario['estado'] !== 'activo') {
        respuestaJson(
            false,
            'Tu cuenta se encuentra inactiva. Contacta al administrador.'
        );
    }

    session_regenerate_id(true);

    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['correo'] = $usuario['correo'];
    $_SESSION['rol'] = $usuario['rol'];

    renovarTokenCsrf();

    $rutaDestino = $usuario['rol'] === 'administrador'
        ? APP_URL . '/admin/index.php'
        : APP_URL . '/usuario/index.php';

    respuestaJson(
        true,
        'Inicio de sesión correcto.',
        [
            'redirect' => $rutaDestino
        ]
    );

} catch (PDOException $error) {

    error_log(
        'Error al iniciar sesión: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al iniciar sesión.'
    );

}