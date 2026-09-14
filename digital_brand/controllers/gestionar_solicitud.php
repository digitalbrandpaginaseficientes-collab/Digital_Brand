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

$idSolicitud = (int) ($_POST['id_solicitud'] ?? 0);
$accion = limpiarDato($_POST['accion'] ?? '');
$estado = limpiarDato($_POST['estado'] ?? '');

if ($idSolicitud <= 0) {
    respuestaJson(false, 'Solicitud inválida.');
}

try {

    $conexion = Database::conectar();

    $consulta = $conexion->prepare("
        SELECT
            sp.id,
            sp.id_usuario,
            sp.id_servicio,
            sp.estado,
            s.nombre AS servicio
        FROM solicitudes_planes sp
        INNER JOIN servicios s
            ON s.id_servicio = sp.id_servicio
        WHERE sp.id = ?
        LIMIT 1
    ");

    $consulta->execute([$idSolicitud]);

    $solicitud = $consulta->fetch();

    if (!$solicitud) {
        respuestaJson(false, 'La solicitud no existe.');
    }

    if ($accion === 'cambiar_estado') {

    $mapaEstados = [
        'pendiente'   => 'pendiente',
        'revision'    => 'en revisión',
        'en revision' => 'en revisión',
        'en revisión' => 'en revisión',
        'aprobada'    => 'aprobada',
        'rechazada'   => 'rechazada',
        'atendida'    => 'atendida'
    ];

    $estado = strtolower(trim($estado));

    if (!isset($mapaEstados[$estado])) {

        respuestaJson(
            false,
            "Estado inválido."
        );

    }

    $estado = $mapaEstados[$estado];

    $actualizar = $conexion->prepare("

        UPDATE solicitudes_planes

        SET

            estado=?,

            fecha_atencion=
            CASE
                WHEN ? IN ('rechazada','atendida')
                THEN NOW()
                ELSE fecha_atencion
            END

        WHERE id=?

    ");

    $actualizar->execute([
    $estado,
    $estado,
    $idSolicitud
]);

$verificarEstado = $conexion->prepare("
    SELECT estado
    FROM solicitudes_planes
    WHERE id = ?
    LIMIT 1
");

$verificarEstado->execute([$idSolicitud]);

$estadoGuardado = $verificarEstado->fetchColumn();

if ($estadoGuardado !== $estado) {

    respuestaJson(
        false,
        'La base de datos no aceptó el nuevo estado.'
    );
}

respuestaJson(
    true,
    'Estado actualizado correctamente a "' .
    $estadoGuardado .
    '".'
);

}

    if ($accion === 'atender') {

        if ($solicitud['estado'] !== 'aprobada') {
            respuestaJson(
                false,
                'La solicitud debe estar aprobada antes de asignar el servicio.'
            ); 
        }

        $conexion->beginTransaction();

        $consultaAsignacion = $conexion->prepare("
            SELECT id
            FROM usuario_servicios
            WHERE id_usuario = ?
              AND id_servicio = ?
            LIMIT 1
        ");

        $consultaAsignacion->execute([
            $solicitud['id_usuario'],
            $solicitud['id_servicio']
        ]);

        $asignacionExistente = $consultaAsignacion->fetch();

        if ($asignacionExistente) {

            $actualizarAsignacion = $conexion->prepare("
                UPDATE usuario_servicios
                SET
                    estado = 'en desarrollo',
                    fecha_inicio = CURDATE(),
                    observaciones = ?
                WHERE id = ?
            ");

            $actualizarAsignacion->execute([
                'Servicio reasignado desde la solicitud #' .
                $idSolicitud,
                $asignacionExistente['id']
            ]);

        } else {

            $insertarAsignacion = $conexion->prepare("
                INSERT INTO usuario_servicios (
                    id_usuario,
                    id_servicio,
                    estado,
                    fecha_inicio,
                    observaciones
                )
                VALUES (?, ?, 'en desarrollo', CURDATE(), ?)
            ");

            $insertarAsignacion->execute([
                $solicitud['id_usuario'],
                $solicitud['id_servicio'],
                'Servicio asignado desde la solicitud #' .
                $idSolicitud
            ]);
        }

        $actualizarSolicitud = $conexion->prepare("
            UPDATE solicitudes_planes
            SET
                estado = 'atendida',
                fecha_atencion = NOW()
            WHERE id = ?
        ");

        $actualizarSolicitud->execute([$idSolicitud]);

        $conexion->commit();

        respuestaJson(
            true,
            'Servicio asignado al cliente y solicitud marcada como atendida.'
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
        'Error gestionando solicitud: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al gestionar la solicitud.'
    );

} catch (Throwable $error) {

    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    error_log(
        'Error interno gestionando solicitud: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error interno.'
    );
}