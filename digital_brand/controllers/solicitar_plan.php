<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');

try {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        respuestaJson(false, "Método inválido.");
    }

    if (!usuarioAutenticado()) {
        respuestaJson(false, "Debe iniciar sesión.");
    }

    if (!validarTokenCsrf($_POST["csrf_token"] ?? "")) {
        respuestaJson(false, "La sesión expiró. Recarga la página.");
    }

    $idServicio = (int)($_POST["id_servicio"] ?? 0);
    $mensaje = limpiarDato($_POST["mensaje"] ?? "");

    if ($idServicio <= 0) {
        respuestaJson(false, "Servicio inválido.");
    }

    $conexion = Database::conectar();

    /*
    =====================================================
    Verificar que exista el servicio
    =====================================================
    */

    $consulta = $conexion->prepare("
        SELECT id_servicio
        FROM servicios
        WHERE id_servicio = ?
        LIMIT 1
    ");

    $consulta->execute([$idServicio]);

    if (!$consulta->fetch()) {
        respuestaJson(false, "El servicio no existe.");
    }

    /*
    =====================================================
    Verificar solicitudes duplicadas
    =====================================================
    */

    $duplicado = $conexion->prepare("
        SELECT id
        FROM solicitudes_planes
        WHERE
            id_usuario = ?
            AND id_servicio = ?
            AND estado = 'pendiente'
        LIMIT 1
    ");

    $duplicado->execute([
        $_SESSION["id_usuario"],
        $idServicio
    ]);

    if ($duplicado->fetch()) {

        respuestaJson(
            false,
            "Ya existe una solicitud pendiente para este servicio."
        );

    }

    /*
    =====================================================
    Insertar solicitud
    =====================================================
    */

    $insert = $conexion->prepare("
        INSERT INTO solicitudes_planes
        (
            id_usuario,
            id_servicio,
            mensaje,
            estado
        )
        VALUES
        (
            ?, ?, ?, 'pendiente'
        )
    ");

    $insert->execute([
        $_SESSION["id_usuario"],
        $idServicio,
        $mensaje
    ]);

    respuestaJson(
        true,
        "Solicitud enviada correctamente."
    );

} catch (PDOException $e) {

    error_log($e->getMessage());

    respuestaJson(
        false,
        "Error en la base de datos."
    );

} catch (Throwable $e) {

    error_log($e->getMessage());

    respuestaJson(
        false,
        "Error interno del servidor."
    );

}