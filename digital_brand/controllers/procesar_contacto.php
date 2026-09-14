<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJson(false, 'Método no permitido.');
}

if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
    respuestaJson(
        false,
        'La sesión expiró. Recarga la página.'
    );
}

$nombre = limpiarDato($_POST['nombre'] ?? '');
$correo = strtolower(
    limpiarDato($_POST['correo'] ?? '')
);
$telefono = limpiarDato($_POST['telefono'] ?? '');
$asunto = limpiarDato($_POST['asunto'] ?? '');
$mensaje = limpiarDato($_POST['mensaje'] ?? '');

if (
    $nombre === '' ||
    $correo === '' ||
    $asunto === '' ||
    $mensaje === ''
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

if (strlen($asunto) < 3 || strlen($asunto) > 150) {
    respuestaJson(
        false,
        'El asunto debe tener entre 3 y 150 caracteres.'
    );
}

if (strlen($mensaje) < 10 || strlen($mensaje) > 2000) {
    respuestaJson(
        false,
        'El mensaje debe tener entre 10 y 2000 caracteres.'
    );
}

$idUsuario = isset($_SESSION['id_usuario'])
    ? (int) $_SESSION['id_usuario']
    : null;

try {

    $conexion = Database::conectar();

    $insertar = $conexion->prepare("
        INSERT INTO contacto (
            id_usuario,
            nombre,
            correo,
            telefono,
            asunto,
            mensaje,
            estado
        )
        VALUES (?, ?, ?, ?, ?, ?, 'nuevo')
    ");

    $insertar->execute([
        $idUsuario,
        $nombre,
        $correo,
        $telefono !== '' ? $telefono : null,
        $asunto,
        $mensaje
    ]);

    respuestaJson(
        true,
        'Mensaje enviado correctamente. Te responderemos pronto.'
    );

} catch (PDOException $error) {

    error_log(
        'Error guardando mensaje de contacto: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al enviar el mensaje.'
    );
}