<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requerirAdministrador();

$conexion = Database::conectar();

/*
|--------------------------------------------------------------------------
| Estadísticas generales
|--------------------------------------------------------------------------
*/

$totalUsuarios = (int) $conexion->query("
    SELECT COUNT(*)
    FROM usuarios
")->fetchColumn();

$totalClientes = (int) $conexion->query("
    SELECT COUNT(*)
    FROM usuarios
    WHERE rol = 'cliente'
")->fetchColumn();

$totalServicios = (int) $conexion->query("
    SELECT COUNT(*)
    FROM servicios
    WHERE estado = 'activo'
")->fetchColumn();

$totalSolicitudesPendientes = (int) $conexion->query("
    SELECT COUNT(*)
    FROM solicitudes_planes
    WHERE estado = 'pendiente'
")->fetchColumn();

$totalMensajesNuevos = (int) $conexion->query("
    SELECT COUNT(*)
    FROM contacto
    WHERE estado = 'nuevo'
")->fetchColumn();

$totalServiciosAsignados = (int) $conexion->query("
    SELECT COUNT(*)
    FROM usuario_servicios
")->fetchColumn();

/*
|--------------------------------------------------------------------------
| Solicitudes recientes
|--------------------------------------------------------------------------
*/

$solicitudesRecientes = $conexion->query("
    SELECT
        sp.id,
        sp.estado,
        sp.fecha_solicitud,
        u.nombre AS usuario,
        u.correo,
        s.nombre AS servicio
    FROM solicitudes_planes sp
    INNER JOIN usuarios u
        ON u.id_usuario = sp.id_usuario
    INNER JOIN servicios s
        ON s.id_servicio = sp.id_servicio
    ORDER BY sp.fecha_solicitud DESC
    LIMIT 5
")->fetchAll();

/*
|--------------------------------------------------------------------------
| Usuarios recientes
|--------------------------------------------------------------------------
*/

$usuariosRecientes = $conexion->query("
    SELECT
        id_usuario,
        nombre,
        correo,
        rol,
        estado,
        fecha_registro
    FROM usuarios
    ORDER BY fecha_registro DESC
    LIMIT 5
")->fetchAll();

$tituloPagina = 'Digital Brand - Administración';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-bienvenida">

            <div>

                <span class="etiqueta-superior">
                    Panel administrativo
                </span>

                <h1>
                    Bienvenido,
                    <?php echo escapar($_SESSION['nombre']); ?>
                </h1>

                <p>
                    Administra usuarios, servicios, solicitudes y mensajes
                    recibidos desde Digital Brand.
                </p>

            </div>

            <div class="avatar-cliente">
                AD
            </div>

        </div>

        <div class="resumen-grid">

            <article class="resumen-card">

                <div class="resumen-icono">
                    👥
                </div>

                <div>

                    <strong>
                        <?php echo $totalUsuarios; ?>
                    </strong>

                    <span>
                        Usuarios registrados
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    👤
                </div>

                <div>

                    <strong>
                        <?php echo $totalClientes; ?>
                    </strong>

                    <span>
                        Clientes
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    🌐
                </div>

                <div>

                    <strong>
                        <?php echo $totalServicios; ?>
                    </strong>

                    <span>
                        Servicios activos
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    📋
                </div>

                <div>

                    <strong>
                        <?php echo $totalSolicitudesPendientes; ?>
                    </strong>

                    <span>
                        Solicitudes pendientes
                    </span>

                </div>

            </article>

        </div>

        <div class="dashboard">

            <aside class="dashboard-sidebar">

                <div class="dashboard-user">

                    <div class="dashboard-avatar">

                        <?php echo escapar(
                            obtenerIniciales($_SESSION['nombre'])
                        ); ?>

                    </div>

                    <h3>
                        <?php echo escapar($_SESSION['nombre']); ?>
                    </h3>

                    <span>
                        Administrador
                    </span>

                </div>

                <nav class="dashboard-menu">

                    <a
                        href="<?php echo APP_URL; ?>/admin/index.php"
                        class="active"
                    >
                        <span>📊</span>
                        Inicio
                    </a>

                    <a href="<?php echo APP_URL; ?>/admin/usuarios.php">
                        <span>👥</span>
                        Usuarios
                    </a>

                    <a href="<?php echo APP_URL; ?>/admin/servicios.php">
                        <span>🌐</span>
                        Servicios
                    </a>

                    <a href="<?php echo APP_URL; ?>/admin/solicitudes.php">
                        <span>📋</span>
                        Solicitudes

                        <?php if ($totalSolicitudesPendientes > 0): ?>

                            <span class="badge badge-pendiente">
                                <?php echo $totalSolicitudesPendientes; ?>
                            </span>

                        <?php endif; ?>

                    </a>

                    <a href="<?php echo APP_URL; ?>/admin/servicios_clientes.php">

    <span>🛠️</span>

    Servicios asignados

</a>

                    <a href="<?php echo APP_URL; ?>/admin/mensajes.php">
                        <span>✉️</span>
                        Mensajes

                        <?php if ($totalMensajesNuevos > 0): ?>

                            <span class="badge badge-pendiente">
                                <?php echo $totalMensajesNuevos; ?>
                            </span>

                        <?php endif; ?>

                    </a>

                    <a href="<?php echo APP_URL; ?>/cerrar_sesion.php">
                        <span>🚪</span>
                        Cerrar sesión
                    </a>

                </nav>

            </aside>

            <div class="dashboard-content">

                <section class="panel">

                    <div class="panel-header">

                        <div>

                            <h2>
                                Resumen general
                            </h2>

                            <p>
                                Estado actual de la plataforma.
                            </p>

                        </div>

                    </div>

                    <div class="panel-grid">

                        <article class="tarjeta">

                            <div class="tarjeta-icono">
                                🛠️
                            </div>

                            <h3>
                                Servicios asignados
                            </h3>

                            <p>
                                Total de servicios vinculados a clientes.
                            </p>

                            <strong class="numero-panel">
                                <?php echo $totalServiciosAsignados; ?>
                            </strong>

                        </article>

                        <article class="tarjeta">

                            <div class="tarjeta-icono">
                                ✉️
                            </div>

                            <h3>
                                Mensajes nuevos
                            </h3>

                            <p>
                                Mensajes pendientes por revisar.
                            </p>

                            <strong class="numero-panel">
                                <?php echo $totalMensajesNuevos; ?>
                            </strong>

                        </article>

                    </div>

                </section>

                <section class="panel">

                    <div class="panel-header">

                        <div>

                            <h2>
                                Solicitudes recientes
                            </h2>

                            <p>
                                Últimas solicitudes enviadas por los clientes.
                            </p>

                        </div>

                        <a
                            href="<?php echo APP_URL; ?>/admin/solicitudes.php"
                            class="boton boton-pequeno"
                        >
                            Ver todas
                        </a>

                    </div>

                    <?php if (empty($solicitudesRecientes)): ?>

                        <div class="estado-vacio estado-vacio-compacto">

                            <div class="estado-vacio-icono">
                                📋
                            </div>

                            <h3>
                                No existen solicitudes
                            </h3>

                            <p>
                                Las solicitudes de los clientes aparecerán aquí.
                            </p>

                        </div>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table">

                                <thead>

                                    <tr>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach (
                                        $solicitudesRecientes as $solicitud
                                    ): ?>

                                        <?php

                                        $claseEstado = strtolower(
                                            $solicitud['estado']
                                        );

                                        $claseEstado = str_replace(
                                            [' ', 'ó'],
                                            ['-', 'o'],
                                            $claseEstado
                                        );

                                        ?>

                                        <tr>

                                            <td data-label="Cliente">

                                                <strong>
                                                    <?php echo escapar(
                                                        $solicitud['usuario']
                                                    ); ?>
                                                </strong>

                                                <br>

                                                <small>
                                                    <?php echo escapar(
                                                        $solicitud['correo']
                                                    ); ?>
                                                </small>

                                            </td>

                                            <td data-label="Servicio">

                                                <?php echo escapar(
                                                    $solicitud['servicio']
                                                ); ?>

                                            </td>

                                            <td data-label="Fecha">

                                                <?php echo date(
                                                    'd/m/Y H:i',
                                                    strtotime(
                                                        $solicitud[
                                                            'fecha_solicitud'
                                                        ]
                                                    )
                                                ); ?>

                                            </td>

                                            <td data-label="Estado">

                                                <span class="badge badge-<?php
                                                    echo escapar($claseEstado);
                                                ?>">

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

                </section>

                <section class="panel">

                    <div class="panel-header">

                        <div>

                            <h2>
                                Usuarios recientes
                            </h2>

                            <p>
                                Últimas cuentas creadas en la plataforma.
                            </p>

                        </div>

                        <a
                            href="<?php echo APP_URL; ?>/admin/usuarios.php"
                            class="boton boton-secundario boton-pequeno"
                        >
                            Administrar usuarios
                        </a>

                    </div>

                    <?php if (empty($usuariosRecientes)): ?>

                        <div class="estado-vacio estado-vacio-compacto">

                            <p>
                                No existen usuarios registrados.
                            </p>

                        </div>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table">

                                <thead>

                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Registro</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach (
                                        $usuariosRecientes as $usuario
                                    ): ?>

                                        <tr>

                                            <td data-label="Nombre">

                                                <?php echo escapar(
                                                    $usuario['nombre']
                                                ); ?>

                                            </td>

                                            <td data-label="Correo">

                                                <?php echo escapar(
                                                    $usuario['correo']
                                                ); ?>

                                            </td>

                                            <td data-label="Rol">

                                                <?php echo escapar(
                                                    ucfirst($usuario['rol'])
                                                ); ?>

                                            </td>

                                            <td data-label="Estado">

                                                <span class="badge badge-<?php
                                                    echo escapar(
                                                        $usuario['estado']
                                                    );
                                                ?>">

                                                    <?php echo escapar(
                                                        $usuario['estado']
                                                    ); ?>

                                                </span>

                                            </td>

                                            <td data-label="Registro">

                                                <?php echo date(
                                                    'd/m/Y',
                                                    strtotime(
                                                        $usuario[
                                                            'fecha_registro'
                                                        ]
                                                    )
                                                ); ?>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </section>

            </div>

        </div>

    </div>

</section>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>