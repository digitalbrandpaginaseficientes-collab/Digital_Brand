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
        'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.'
    );
}

$nombre = limpiarDato($_POST['nombre'] ?? '');
$correo = strtolower(limpiarDato($_POST['correo'] ?? ''));
$telefono = limpiarDato($_POST['telefono'] ?? '');
$empresa = limpiarDato($_POST['empresa'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

if (
    $nombre === '' ||
    $correo === '' ||
    $contrasena === '' ||
    $confirmarContrasena === ''
) {
    respuestaJson(
        false,
        'Completa todos los campos obligatorios.'
    );
}

if (strlen($nombre) < 3 || strlen($nombre) > 100) {
    respuestaJson(
        false,
        'El nombre debe tener entre 3 y 100 caracteres.'
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

if ($telefono !== '' && strlen($telefono) > 20) {
    respuestaJson(
        false,
        'El teléfono es demasiado largo.'
    );
}

if ($empresa !== '' && strlen($empresa) > 120) {
    respuestaJson(
        false,
        'El nombre de la empresa es demasiado largo.'
    );
}

if (!validarContrasena($contrasena)) {
    respuestaJson(
        false,
        'La contraseña debe tener mínimo 8 caracteres.'
    );
}

if (strlen($contrasena) > 72) {
    respuestaJson(
        false,
        'La contraseña no puede tener más de 72 caracteres.'
    );
}

if ($contrasena !== $confirmarContrasena) {
    respuestaJson(
        false,
        'Las contraseñas no coinciden.'
    );
}

try {

    $conexion = Database::conectar();

    $consultaCorreo = $conexion->prepare(
        "SELECT id_usuario
         FROM usuarios
         WHERE correo = ?
         LIMIT 1"
    );

    $consultaCorreo->execute([$correo]);

    if ($consultaCorreo->fetch()) {
        respuestaJson(
            false,
            'Este correo ya está registrado.'
        );
    }

    $contrasenaSegura = password_hash(
        $contrasena,
        PASSWORD_DEFAULT
    );

    $insertarUsuario = $conexion->prepare(
        "INSERT INTO usuarios (
            nombre,
            correo,
            contrasena,
            telefono,
            empresa,
            rol,
            estado
        ) VALUES (?, ?, ?, ?, ?, 'cliente', 'activo')"
    );

    $insertarUsuario->execute([
        $nombre,
        $correo,
        $contrasenaSegura,
        $telefono !== '' ? $telefono : null,
        $empresa !== '' ? $empresa : null
    ]);

    renovarTokenCsrf();

    respuestaJson(
        true,
        'Cuenta creada correctamente. Ahora puedes iniciar sesión.'
    );

} catch (PDOException $error) {

    error_log(
        'Error al registrar usuario: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al crear la cuenta. Inténtalo nuevamente.'
    );

}