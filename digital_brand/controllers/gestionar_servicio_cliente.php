<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');

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

$id = (int) ($_POST['id'] ?? 0);

$estado = mb_strtolower(
    trim($_POST['estado'] ?? ''),
    'UTF-8'
);

$progreso = filter_input(
    INPUT_POST,
    'progreso',
    FILTER_VALIDATE_INT
);

$fechaInicio = trim(
    $_POST['fecha_inicio'] ?? ''
);

$fechaEntrega = trim(
    $_POST['fecha_entrega_estimada'] ?? ''
);

$fechaFinalizacion = trim(
    $_POST['fecha_finalizacion'] ?? ''
);

$fechaVencimiento = trim(
    $_POST['fecha_vencimiento'] ?? ''
);

$observaciones = limpiarDato(
    $_POST['observaciones'] ?? ''
);

if ($id <= 0) {
    respuestaJson(false, 'Servicio asignado inválido.');
}

$estadosPermitidos = [
    'pendiente',
    'en desarrollo',
    'en revisión',
    'activo',
    'finalizado',
    'cancelado'
];

if (!in_array($estado, $estadosPermitidos, true)) {
    respuestaJson(false, 'El estado seleccionado no es válido.');
}

if ($progreso === false || $progreso === null) {
    respuestaJson(false, 'El progreso no es válido.');
}

if ($progreso < 0 || $progreso > 100) {
    respuestaJson(
        false,
        'El progreso debe estar entre 0 y 100.'
    );
}

function fechaValidaServicio(
    string $fecha
): bool {

    if ($fecha === '') {
        return true;
    }

    $objetoFecha = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    return $objetoFecha !== false &&
        $objetoFecha->format('Y-m-d') === $fecha;
}

if (!fechaValidaServicio($fechaInicio)) {
    respuestaJson(false, 'La fecha de inicio no es válida.');
}

if (!fechaValidaServicio($fechaEntrega)) {
    respuestaJson(
        false,
        'La fecha estimada de entrega no es válida.'
    );
}

if (!fechaValidaServicio($fechaFinalizacion)) {
    respuestaJson(
        false,
        'La fecha de finalización no es válida.'
    );
}

if (!fechaValidaServicio($fechaVencimiento)) {
    respuestaJson(
        false,
        'La fecha de vencimiento no es válida.'
    );
}

if (
    $fechaInicio !== '' &&
    $fechaEntrega !== '' &&
    $fechaEntrega < $fechaInicio
) {
    respuestaJson(
        false,
        'La entrega estimada no puede ser anterior a la fecha de inicio.'
    );
}

if (
    $fechaInicio !== '' &&
    $fechaFinalizacion !== '' &&
    $fechaFinalizacion < $fechaInicio
) {
    respuestaJson(
        false,
        'La finalización no puede ser anterior a la fecha de inicio.'
    );
}

if (mb_strlen($observaciones, 'UTF-8') > 3000) {
    respuestaJson(
        false,
        'Las observaciones no pueden superar los 3000 caracteres.'
    );
}

if ($estado === 'finalizado') {

    $progreso = 100;

    if ($fechaFinalizacion === '') {
        $fechaFinalizacion = date('Y-m-d');
    }
}

if (
    $estado === 'cancelado' &&
    $fechaFinalizacion === ''
) {
    $fechaFinalizacion = date('Y-m-d');
}

if (
    in_array(
        $estado,
        ['pendiente', 'en desarrollo', 'en revisión', 'activo'],
        true
    )
) {
    $fechaFinalizacion = '';
}

try {

    $conexion = Database::conectar();

    $consulta = $conexion->prepare("
        SELECT
            us.id,
            us.estado,
            us.progreso,
            u.nombre AS cliente,
            s.nombre AS servicio
        FROM usuario_servicios us
        INNER JOIN usuarios u
            ON u.id_usuario = us.id_usuario
        INNER JOIN servicios s
            ON s.id_servicio = us.id_servicio
        WHERE us.id = ?
        LIMIT 1
    ");

    $consulta->execute([$id]);

    $servicioActual = $consulta->fetch();

    if (!$servicioActual) {
        respuestaJson(
            false,
            'El servicio asignado no existe.'
        );
    }

    $actualizar = $conexion->prepare("
        UPDATE usuario_servicios
        SET
            estado = ?,
            progreso = ?,
            fecha_inicio = ?,
            fecha_entrega_estimada = ?,
            fecha_finalizacion = ?,
            fecha_vencimiento = ?,
            observaciones = ?
        WHERE id = ?
    ");

    $actualizar->execute([
        $estado,
        $progreso,
        $fechaInicio !== ''
            ? $fechaInicio
            : null,
        $fechaEntrega !== ''
            ? $fechaEntrega
            : null,
        $fechaFinalizacion !== ''
            ? $fechaFinalizacion
            : null,
        $fechaVencimiento !== ''
            ? $fechaVencimiento
            : null,
        $observaciones !== ''
            ? $observaciones
            : null,
        $id
    ]);

    $verificar = $conexion->prepare("
        SELECT
            estado,
            progreso,
            fecha_inicio,
            fecha_entrega_estimada,
            fecha_finalizacion,
            fecha_vencimiento,
            observaciones
        FROM usuario_servicios
        WHERE id = ?
        LIMIT 1
    ");

    $verificar->execute([$id]);

    $resultadoGuardado = $verificar->fetch();

    if (!$resultadoGuardado) {
        respuestaJson(
            false,
            'No fue posible verificar la actualización.'
        );
    }

    if (
        $resultadoGuardado['estado'] !== $estado ||
        (int) $resultadoGuardado['progreso'] !== $progreso
    ) {
        respuestaJson(
            false,
            'La base de datos no conservó correctamente los cambios.'
        );
    }

    respuestaJson(
        true,
        'Seguimiento actualizado correctamente.'
    );

} catch (PDOException $error) {

    error_log(
        'Error gestionando servicio del cliente: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error al actualizar el servicio.'
    );

} catch (Throwable $error) {

    error_log(
        'Error interno gestionando servicio: ' .
        $error->getMessage()
    );

    respuestaJson(
        false,
        'Ocurrió un error interno.'
    );
}