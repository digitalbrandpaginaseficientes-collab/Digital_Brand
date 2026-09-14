<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJson(false, 'Método no permitido.');
}

if (!usuarioAutenticado() || !esAdministrador()) {
    respuestaJson(false, 'Acceso no autorizado.');
}

if (!validarTokenCsrf($_POST['csrf_token'] ?? '')) {
    respuestaJson(
        false,
        'La sesión expiró. Recarga la página.'
    );
}

$idMensaje = (int) ($_POST['id_mensaje'] ?? 0);
$accion = limpiarDato($_POST['accion'] ?? '');
$estadoRecibido = limpiarDato($_POST['estado'] ?? '');

if ($idMensaje <= 0) {
    respuestaJson(false, 'Mensaje inválido.');
}

try {

    $conexion = Database::conectar();

    $consulta = $conexion->prepare("
        SELECT
            id,
            estado
        FROM contacto
        WHERE id = ?
        LIMIT 1
    ");

    $consulta->execute([$idMensaje]);

    $mensaje = $consulta->fetch();

    if (!$mensaje) {
        respuestaJson(false, 'El mensaje no existe.');
    }

    if ($accion === 'cambiar_estado') {

        $estadoNormalizado = mb_strtolower(
            trim($estadoRecibido),
            'UTF-8'
        );

        $mapaEstados = [
            'nuevo' => 'nuevo',
            'leido' => 'leído',
            'leído' => 'leído',
            'respondido' => 'respondido'
        ];

        if (!isset($mapaEstados[$estadoNormalizado])) {
            respuestaJson(false, 'Estado no válido.');
        }

        $nuevoEstado = $mapaEstados[$estadoNormalizado];

        $actualizar = $conexion->prepare("
            UPDATE contacto
            SET estado = ?
            WHERE id = ?
        ");

        $actualizar->execute([
            $nuevoEstado,
            $idMensaje
        ]);

        $verificar = $conexion->prepare("
            SELECT estado
            FROM contacto
            WHERE id = ?
            LIMIT 1
        ");

        $verificar->execute([$idMensaje]);

        $estadoGuardado = $verificar->fetchColumn();

        if ($estadoGuardado !== $nuevoEstado) {
            respuestaJson(
                false,
                'La base de datos no aceptó el nuevo estado.'
            );
        }

        respuestaJson(
            true,
            'Mensaje marcado como "' .
            $estadoGuardado .
            '".'
        );
    }

    if ($accion === 'eliminar') {

        $eliminar = $conexion->prepare("
            DELETE FROM contacto
            WHERE id = ?
        ");

        $eliminar->execute([$idMensaje]);

        if ($eliminar->rowCount() !== 1) {
            respuestaJson(
                false,
                'El mensaje no pudo eliminarse.'
            );
        }

        respuestaJson(
            true,
            'Mensaje eliminado correctamente.'
        );
    }

    respuestaJson(false, 'Acción no reconocida.');

} catch (PDOException $error) {

    error_log(
        'Error gestionando mensaje: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al gestionar el mensaje.'
    );
}