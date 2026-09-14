<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requerirCliente();

$conexion = Database::conectar();

$consulta = $conexion->prepare("
    SELECT
        us.id,
        us.estado,
        us.progreso,
        us.fecha_compra,
        us.fecha_inicio,
        us.fecha_entrega_estimada,
        us.fecha_finalizacion,
        us.fecha_vencimiento,
        us.observaciones,
        s.id_servicio,
        s.nombre AS servicio,
        s.descripcion,
        s.precio,
        s.caracteristicas
    FROM usuario_servicios us
    INNER JOIN servicios s
        ON s.id_servicio = us.id_servicio
    WHERE us.id_usuario = ?
    ORDER BY
        CASE us.estado
            WHEN 'pendiente' THEN 1
            WHEN 'en desarrollo' THEN 2
            WHEN 'activo' THEN 3
            WHEN 'finalizado' THEN 4
            WHEN 'cancelado' THEN 5
            ELSE 6
        END,
        us.fecha_compra DESC,
        us.id DESC
");

$consulta->execute([
    $_SESSION['id_usuario']
]);

$serviciosContratados = $consulta->fetchAll();

$totalServicios = count($serviciosContratados);
$serviciosPendientes = 0;
$serviciosEnDesarrollo = 0;
$serviciosFinalizados = 0;

foreach ($serviciosContratados as $item) {

    $estadoContador = mb_strtolower(
        trim($item['estado']),
        'UTF-8'
    );

    if ($estadoContador === 'pendiente') {
        $serviciosPendientes++;
    }

    if (
        $estadoContador === 'en desarrollo' ||
        $estadoContador === 'activo'
    ) {
        $serviciosEnDesarrollo++;
    }

    if ($estadoContador === 'finalizado') {
        $serviciosFinalizados++;
    }
}

$tituloPagina = 'Digital Brand - Mis servicios';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Área del cliente
                </span>

                <h1>Mis servicios contratados</h1>

                <p>
                    Consulta el estado, avance, fechas y observaciones
                    de los proyectos asignados a tu cuenta.
                </p>

            </div>

            <div
                style="
                    display: flex;
                    gap: 12px;
                    flex-wrap: wrap;
                "
            >

                <a
                    href="<?php echo APP_URL; ?>/usuario/index.php"
                    class="boton boton-secundario"
                >
                    Volver al panel
                </a>

                <a
                    href="<?php echo APP_URL; ?>/usuario/servicios.php"
                    class="boton"
                >
                    Solicitar otro servicio
                </a>

            </div>

        </div>

        <div class="resumen-grid resumen-servicios-cliente">

            <article class="resumen-card">

                <div class="resumen-icono">
                    🌐
                </div>

                <div>

                    <strong>
                        <?php echo $totalServicios; ?>
                    </strong>

                    <span>
                        Total de servicios
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    ⏳
                </div>

                <div>

                    <strong>
                        <?php echo $serviciosPendientes; ?>
                    </strong>

                    <span>
                        Pendientes
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    🛠️
                </div>

                <div>

                    <strong>
                        <?php echo $serviciosEnDesarrollo; ?>
                    </strong>

                    <span>
                        En desarrollo o activos
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    ✅
                </div>

                <div>

                    <strong>
                        <?php echo $serviciosFinalizados; ?>
                    </strong>

                    <span>
                        Finalizados
                    </span>

                </div>

            </article>

        </div>

        <?php if (empty($serviciosContratados)): ?>

            <div class="estado-vacio servicios-vacio">

                <div class="estado-vacio-icono">
                    🧾
                </div>

                <h2>Aún no tienes servicios contratados</h2>

                <p>
                    Cuando el administrador apruebe y asigne una solicitud,
                    el proyecto aparecerá en esta sección.
                </p>

                <a
                    href="<?php echo APP_URL; ?>/usuario/servicios.php"
                    class="boton"
                >
                    Ver planes disponibles
                </a>

            </div>

        <?php else: ?>

            <div class="servicios-contratados-grid">

                <?php foreach (
                    $serviciosContratados as $servicio
                ): ?>

                    <?php

                    $estadoNormalizado = mb_strtolower(
                        trim($servicio['estado']),
                        'UTF-8'
                    );

                    $progreso = (int) $servicio['progreso'];

                    if ($progreso < 0) {
                        $progreso = 0;
                    }

                    if ($progreso > 100) {
                        $progreso = 100;
                    }

                    switch ($estadoNormalizado) {

                        case 'pendiente':

                            $claseEstado =
                                'servicio-estado-pendiente';

                            $iconoEstado = '⏳';
                            $nombreEstado = 'Pendiente';

                            break;

                        case 'en desarrollo':

                            $claseEstado =
                                'servicio-estado-desarrollo';

                            $iconoEstado = '🛠️';
                            $nombreEstado = 'En desarrollo';

                            break;

                        case 'activo':

                            $claseEstado =
                                'servicio-estado-revision';

                            $iconoEstado = '⚙️';
                            $nombreEstado = 'Activo';

                            break;

                        case 'finalizado':

                            $claseEstado =
                                'servicio-estado-finalizado';

                            $iconoEstado = '✅';
                            $nombreEstado = 'Finalizado';
                            $progreso = 100;

                            break;

                        case 'cancelado':

                            $claseEstado =
                                'servicio-estado-cancelado';

                            $iconoEstado = '⛔';
                            $nombreEstado = 'Cancelado';

                            break;

                        default:

                            $claseEstado =
                                'servicio-estado-pendiente';

                            $iconoEstado = 'ℹ️';

                            $nombreEstado =
                                $estadoNormalizado !== ''
                                    ? ucfirst($estadoNormalizado)
                                    : 'Pendiente';

                            break;
                    }

                    $caracteristicas = array_filter(
                        array_map(
                            'trim',
                            explode(
                                '|',
                                $servicio['caracteristicas'] ?? ''
                            )
                        )
                    );

                    ?>

                    <article class="servicio-contratado-card">

                        <div class="servicio-contratado-cabecera">

                            <div class="servicio-contratado-icono">
                                🌐
                            </div>

                            <div class="servicio-contratado-titulo">

                                <span
                                    class="servicio-estado <?php
                                        echo escapar($claseEstado);
                                    ?>"
                                >

                                    <?php echo $iconoEstado; ?>

                                    <?php echo escapar($nombreEstado); ?>

                                </span>

                                <h2>
                                    <?php echo escapar(
                                        $servicio['servicio']
                                    ); ?>
                                </h2>

                                <p>
                                    <?php echo escapar(
                                        $servicio['descripcion']
                                    ); ?>
                                </p>

                            </div>

                        </div>

                        <div class="servicio-progreso-contenedor">

                            <div class="servicio-progreso-encabezado">

                                <span>
                                    Avance del proyecto
                                </span>

                                <strong>
                                    <?php echo $progreso; ?> %
                                </strong>

                            </div>

                            <div
                                class="servicio-progreso-barra"
                                role="progressbar"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                aria-valuenow="<?php
                                    echo $progreso;
                                ?>"
                            >

                                <div
                                    class="servicio-progreso-valor"
                                    style="width: <?php
                                        echo $progreso;
                                    ?>%;"
                                ></div>

                            </div>

                        </div>

                        <div class="servicio-fechas-grid">

                            <div class="servicio-fecha-item">

                                <span class="servicio-fecha-icono">
                                    🛒
                                </span>

                                <div>

                                    <small>
                                        Fecha de asignación
                                    </small>

                                    <strong>
                                        <?php echo date(
                                            'd/m/Y',
                                            strtotime(
                                                $servicio['fecha_compra']
                                            )
                                        ); ?>
                                    </strong>

                                </div>

                            </div>

                            <div class="servicio-fecha-item">

                                <span class="servicio-fecha-icono">
                                    📅
                                </span>

                                <div>

                                    <small>
                                        Fecha de inicio
                                    </small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio['fecha_inicio']
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_inicio'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            Por definir

                                        <?php endif; ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="servicio-fecha-item">

                                <span class="servicio-fecha-icono">
                                    🎯
                                </span>

                                <div>

                                    <small>
                                        Entrega estimada
                                    </small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio[
                                                    'fecha_entrega_estimada'
                                                ]
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_entrega_estimada'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            Por definir

                                        <?php endif; ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="servicio-fecha-item">

                                <span class="servicio-fecha-icono">
                                    ✅
                                </span>

                                <div>

                                    <small>
                                        Finalización
                                    </small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio[
                                                    'fecha_finalizacion'
                                                ]
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_finalizacion'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            No finalizado

                                        <?php endif; ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="servicio-fecha-item">

                                <span class="servicio-fecha-icono">
                                    ⏱️
                                </span>

                                <div>

                                    <small>
                                        Vigencia
                                    </small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio[
                                                    'fecha_vencimiento'
                                                ]
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_vencimiento'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            Sin vencimiento

                                        <?php endif; ?>

                                    </strong>

                                </div>

                            </div>

                            <div class="servicio-fecha-item">

                                <span class="servicio-fecha-icono">
                                    💰
                                </span>

                                <div>

                                    <small>
                                        Valor del servicio
                                    </small>

                                    <strong>
                                        <?php echo formatearPrecio(
                                            $servicio['precio']
                                        ); ?>
                                    </strong>

                                </div>

                            </div>

                        </div>

                        <?php if (!empty($caracteristicas)): ?>

                            <div class="servicio-caracteristicas">

                                <h3>
                                    Características incluidas
                                </h3>

                                <ul>

                                    <?php foreach (
                                        $caracteristicas
                                        as $caracteristica
                                    ): ?>

                                        <li>
                                            <?php echo escapar(
                                                $caracteristica
                                            ); ?>
                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                            </div>

                        <?php endif; ?>

                        <div class="servicio-observaciones">

                            <div class="servicio-observaciones-titulo">

                                <span>💬</span>

                                <h3>
                                    Observaciones del administrador
                                </h3>

                            </div>

                            <?php if (
                                !empty(
                                    trim(
                                        $servicio[
                                            'observaciones'
                                        ] ?? ''
                                    )
                                )
                            ): ?>

                                <p>
                                    <?php echo nl2br(
                                        escapar(
                                            $servicio[
                                                'observaciones'
                                            ]
                                        )
                                    ); ?>
                                </p>

                            <?php else: ?>

                                <p class="texto-sin-observaciones">
                                    El administrador todavía no ha agregado
                                    observaciones para este proyecto.
                                </p>

                            <?php endif; ?>

                        </div>

                        <?php if (
                            $estadoNormalizado === 'finalizado'
                        ): ?>

                            <div class="servicio-finalizado-mensaje">

                                <span>🎉</span>

                                <div>

                                    <strong>
                                        Proyecto finalizado
                                    </strong>

                                    <p>
                                        El administrador marcó este servicio
                                        como terminado.
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>

                        <?php if (
                            $estadoNormalizado === 'cancelado'
                        ): ?>

                            <div class="servicio-cancelado-mensaje">

                                <span>ℹ️</span>

                                <div>

                                    <strong>
                                        Servicio cancelado
                                    </strong>

                                    <p>
                                        Consulta las observaciones o
                                        comunícate con Digital Brand.
                                    </p>

                                </div>

                            </div>

                        <?php endif; ?>

                        <div class="servicio-contratado-acciones">

                            <a
                                href="<?php
                                    echo APP_URL;
                                ?>/contacto.php"
                                class="boton boton-secundario"
                            >
                                Contactar al equipo
                            </a>

                            <a
                                href="https://wa.me/573332843241?text=<?php
                                    echo rawurlencode(
                                        'Hola, necesito información sobre mi servicio: ' .
                                        $servicio['servicio']
                                    );
                                ?>"
                                class="boton"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Consultar por WhatsApp
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>