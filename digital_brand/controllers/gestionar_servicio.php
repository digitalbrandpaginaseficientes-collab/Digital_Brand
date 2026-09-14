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

$accion = limpiarDato($_POST['accion'] ?? '');
$idServicio = (int) ($_POST['id_servicio'] ?? 0);

try {

    $conexion = Database::conectar();

    if ($accion === 'crear' || $accion === 'editar') {

        $nombre = limpiarDato($_POST['nombre'] ?? '');
        $descripcion = limpiarDato(
            $_POST['descripcion'] ?? ''
        );
        $precio = (float) ($_POST['precio'] ?? 0);
        $caracteristicas = limpiarDato(
            $_POST['caracteristicas'] ?? ''
        );
        $estado = limpiarDato($_POST['estado'] ?? '');

        if (
            $nombre === '' ||
            $descripcion === '' ||
            $caracteristicas === ''
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

        if ($precio < 0) {
            respuestaJson(
                false,
                'El precio no puede ser negativo.'
            );
        }

        if (!in_array(
            $estado,
            ['activo', 'inactivo'],
            true
        )) {
            respuestaJson(false, 'Estado no válido.');
        }

        $consultaDuplicado = $conexion->prepare("
            SELECT id_servicio
            FROM servicios
            WHERE nombre = ?
              AND id_servicio <> ?
            LIMIT 1
        ");

        $consultaDuplicado->execute([
            $nombre,
            $idServicio
        ]);

        if ($consultaDuplicado->fetch()) {
            respuestaJson(
                false,
                'Ya existe un servicio con ese nombre.'
            );
        }

        if ($accion === 'crear') {

            $insertar = $conexion->prepare("
                INSERT INTO servicios (
                    nombre,
                    descripcion,
                    precio,
                    caracteristicas,
                    estado
                )
                VALUES (?, ?, ?, ?, ?)
            ");

            $insertar->execute([
                $nombre,
                $descripcion,
                $precio,
                $caracteristicas,
                $estado
            ]);

            respuestaJson(
                true,
                'Servicio creado correctamente.'
            );
        }

        if ($idServicio <= 0) {
            respuestaJson(false, 'Servicio inválido.');
        }

        $actualizar = $conexion->prepare("
            UPDATE servicios
            SET
                nombre = ?,
                descripcion = ?,
                precio = ?,
                caracteristicas = ?,
                estado = ?
            WHERE id_servicio = ?
        ");

        $actualizar->execute([
            $nombre,
            $descripcion,
            $precio,
            $caracteristicas,
            $estado,
            $idServicio
        ]);

        respuestaJson(
            true,
            'Servicio actualizado correctamente.'
        );
    }

    if ($accion === 'cambiar_estado') {

        $estado = limpiarDato($_POST['estado'] ?? '');

        if ($idServicio <= 0) {
            respuestaJson(false, 'Servicio inválido.');
        }

        if (!in_array(
            $estado,
            ['activo', 'inactivo'],
            true
        )) {
            respuestaJson(false, 'Estado no válido.');
        }

        $actualizar = $conexion->prepare("
            UPDATE servicios
            SET estado = ?
            WHERE id_servicio = ?
        ");

        $actualizar->execute([
            $estado,
            $idServicio
        ]);

        respuestaJson(
            true,
            'Estado del servicio actualizado.'
        );
    }

    respuestaJson(false, 'Acción no reconocida.');

} catch (PDOException $error) {

    error_log(
        'Error gestionando servicio: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al gestionar el servicio.'
    );
}