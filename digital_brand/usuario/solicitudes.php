<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requerirCliente();

$conexion = Database::conectar();

$consulta = $conexion->prepare("
    SELECT
        sp.id,
        sp.mensaje,
        sp.estado,
        sp.fecha_solicitud,
        sp.fecha_atencion,
        s.nombre AS servicio,
        s.descripcion,
        s.precio
    FROM solicitudes_planes sp
    INNER JOIN servicios s
        ON s.id_servicio = sp.id_servicio
    WHERE sp.id_usuario = ?
    ORDER BY sp.fecha_solicitud DESC
");

$consulta->execute([
    $_SESSION['id_usuario']
]);

$solicitudes = $consulta->fetchAll();

$tituloPagina = 'Digital Brand - Mis solicitudes';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Seguimiento
                </span>

                <h1>Mis solicitudes</h1>

                <p>
                    Consulta los planes solicitados y su estado actual.
                </p>

            </div>

            <a
                href="<?php echo APP_URL; ?>/usuario/servicios.php"
                class="boton"
            >
                Solicitar otro plan
            </a>

        </div>

        <?php if (empty($solicitudes)): ?>

            <div class="estado-vacio">

                <div class="estado-vacio-icono">
                    📋
                </div>

                <h2>No tienes solicitudes registradas</h2>

                <p>
                    Consulta los planes disponibles y envía la primera
                    solicitud para tu negocio.
                </p>

                <a
                    href="<?php echo APP_URL; ?>/usuario/servicios.php"
                    class="boton"
                >
                    Ver planes
                </a>

            </div>

        <?php else: ?>

            <div class="tabla-contenedor">

                <table class="tabla">

                    <thead>

                        <tr>
                            <th>Servicio</th>
                            <th>Mensaje</th>
                            <th>Precio</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($solicitudes as $solicitud): ?>

                            <?php

                            $estadoNormalizado = strtolower(
                                $solicitud['estado']
                            );

                            $estadoNormalizado = str_replace(
                                [' ', 'ó'],
                                ['-', 'o'],
                                $estadoNormalizado
                            );

                            ?>

                            <tr>

                                <td data-label="Servicio">

                                    <strong>
                                        <?php echo escapar(
                                            $solicitud['servicio']
                                        ); ?>
                                    </strong>

                                </td>

                                <td data-label="Mensaje">

                                    <?php echo escapar(
                                        $solicitud['mensaje'] ?: 'Sin mensaje'
                                    ); ?>

                                </td>

                                <td data-label="Precio">

                                    <?php echo formatearPrecio(
                                        $solicitud['precio']
                                    ); ?>

                                </td>

                                <td data-label="Fecha">

                                    <?php echo date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $solicitud['fecha_solicitud']
                                        )
                                    ); ?>

                                </td>

                                <td data-label="Estado">

                                    <span
                                        class="estado estado-<?php
                                        echo escapar($estadoNormalizado);
                                        ?>"
                                    >
                                        <?php echo escapar(
                                            $solicitud['estado']
                                        ); ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>