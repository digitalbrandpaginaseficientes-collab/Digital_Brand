<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

requerirCliente();

$conexion = Database::conectar();

$idUsuario = $_SESSION['id_usuario'];

$consultaUsuario = $conexion->prepare(
    "SELECT
        nombre,
        correo,
        telefono,
        empresa,
        rol,
        estado,
        fecha_registro
     FROM usuarios
     WHERE id_usuario = ?
     LIMIT 1"
);

$consultaUsuario->execute([$idUsuario]);

$usuario = $consultaUsuario->fetch();

$consultaServicios = $conexion->prepare(
    "SELECT
        us.id,
        s.nombre,
        s.descripcion,
        s.precio,
        us.estado,
        us.fecha_compra,
        us.fecha_inicio,
        us.fecha_vencimiento,
        us.observaciones
     FROM usuario_servicios us
     INNER JOIN servicios s
        ON s.id_servicio = us.id_servicio
     WHERE us.id_usuario = ?
     ORDER BY us.fecha_compra DESC"
);

$consultaServicios->execute([$idUsuario]);

$serviciosUsuario = $consultaServicios->fetchAll();

$consultaSolicitudes = $conexion->prepare(
    "SELECT
        sp.id,
        s.nombre AS servicio,
        sp.mensaje,
        sp.estado,
        sp.fecha_solicitud,
        sp.fecha_atencion
     FROM solicitudes_planes sp
     INNER JOIN servicios s
        ON s.id_servicio = sp.id_servicio
     WHERE sp.id_usuario = ?
     ORDER BY sp.fecha_solicitud DESC
     LIMIT 5"
);

$consultaSolicitudes->execute([$idUsuario]);

$solicitudesUsuario = $consultaSolicitudes->fetchAll();

$totalServicios = count($serviciosUsuario);
$totalSolicitudes = count($solicitudesUsuario);

$tituloPagina = 'Digital Brand - Mi cuenta';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-bienvenida">

            <div>

                <span class="etiqueta-superior">
                    Panel del cliente
                </span>

                <h1>
                    Bienvenido,
                    <?php echo escapar($usuario['nombre']); ?>
                </h1>

                <p>
                    Desde aquí puedes consultar tus datos, tus servicios
                    contratados y el estado de tus solicitudes.
                </p>

            </div>

            <div class="avatar-cliente">

                <?php echo escapar(obtenerIniciales($usuario['nombre'])); ?>

            </div>

        </div>

        <div class="panel-estadisticas">

            <article class="tarjeta-resumen">

                <span class="resumen-icono">
                    💻
                </span>

                <div>

                    <strong>
                        <?php echo $totalServicios; ?>
                    </strong>

                    <span>
                        Servicios contratados
                    </span>

                </div>

            </article>

            <article class="tarjeta-resumen">

                <span class="resumen-icono">
                    📋
                </span>

                <div>

                    <strong>
                        <?php echo $totalSolicitudes; ?>
                    </strong>

                    <span>
                        Solicitudes recientes
                    </span>

                </div>

            </article>

            <article class="tarjeta-resumen">

                <span class="resumen-icono">
                    👤
                </span>

                <div>

                    <strong>
                        <?php echo escapar(ucfirst($usuario['estado'])); ?>
                    </strong>

                    <span>
                        Estado de la cuenta
                    </span>

                </div>

            </article>

        </div>

        <div class="panel-grid">

            <section class="tarjeta panel-perfil">

                <div class="panel-titulo">

                    <div>

                        <h2>
                            Información personal
                        </h2>

                        <p>
                            Datos registrados en tu cuenta.
                        </p>

                    </div>

                    <a
                        href="<?php echo APP_URL; ?>/usuario/perfil.php"
                        class="boton boton-secundario boton-pequeno"
                    >
                        Editar perfil
                    </a>

                </div>

                <div class="datos-grid">

                    <div class="dato-item">

                        <span>
                            Nombre
                        </span>

                        <strong>
                            <?php echo escapar($usuario['nombre']); ?>
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>
                            Correo
                        </span>

                        <strong>
                            <?php echo escapar($usuario['correo']); ?>
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>
                            Teléfono
                        </span>

                        <strong>
                            <?php echo $usuario['telefono']
                                ? escapar($usuario['telefono'])
                                : 'No registrado'; ?>
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>
                            Empresa
                        </span>

                        <strong>
                            <?php echo $usuario['empresa']
                                ? escapar($usuario['empresa'])
                                : 'No registrada'; ?>
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>
                            Rol
                        </span>

                        <strong>
                            <?php echo escapar(ucfirst($usuario['rol'])); ?>
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>
                            Miembro desde
                        </span>

                        <strong>
                            <?php echo date(
                                'd/m/Y',
                                strtotime($usuario['fecha_registro'])
                            ); ?>
                        </strong>

                    </div>

                </div>

            </section>

            <section class="tarjeta accesos-rapidos">

                <h2>
                    Accesos rápidos
                </h2>

                <p>
                    Ingresa directamente a las funciones principales.
                </p>

                <div class="accesos-lista">

                    <a
                        href="<?php echo APP_URL; ?>/usuario/servicios.php"
                        class="acceso-item"
                    >

                        <span>
                            🛍️
                        </span>

                        <div>

                            <strong>
                                Ver planes y servicios
                            </strong>

                            <small>
                                Solicita una solución para tu negocio.
                            </small>

                        </div>

                    </a>

                    <a
                        href="<?php echo APP_URL; ?>/usuario/solicitudes.php"
                        class="acceso-item"
                    >

                        <span>
                            📄
                        </span>

                        <div>

                            <strong>
                                Mis solicitudes
                            </strong>

                            <small>
                                Consulta el estado de tus solicitudes.
                            </small>

                        </div>

                    </a>

                    <a
                        href="<?php echo APP_URL; ?>/contacto.php"
                        class="acceso-item"
                    >

                        <span>
                            💬
                        </span>

                        <div>

                            <strong>
                                Contactar soporte
                            </strong>

                            <small>
                                Escríbenos si necesitas ayuda.
                            </small>

                        </div>

                    </a>

                </div>

            </section>

        </div>

    </div>

</section>

<section class="seccion-pequena seccion-clara">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Servicios
                </span>

                <h2>
                    Mis servicios
                </h2>

                <p>
                    Consulta los servicios que han sido asignados a tu cuenta.
                </p>

            </div>

            <a
                href="<?php echo APP_URL; ?>/usuario/servicios.php"
                class="boton boton-pequeno"
            >
                Ver todos
            </a>

        </div>

        <?php if (empty($serviciosUsuario)): ?>

            <div class="estado-vacio">

                <div class="estado-vacio-icono">
                    💻
                </div>

                <h3>
                    Aún no tienes servicios contratados
                </h3>

                <p>
                    Consulta nuestros planes y envía una solicitud desde
                    tu cuenta.
                </p>

                <a
                    href="<?php echo APP_URL; ?>/usuario/servicios.php"
                    class="boton"
                >
                    Consultar planes
                </a>

            </div>

        <?php else: ?>

            <div class="cuadricula">

                <?php foreach (
                    array_slice($serviciosUsuario, 0, 3)
                    as $servicio
                ): ?>

                    <?php

                    $claseEstado = str_replace(
                        ' ',
                        '-',
                        strtolower($servicio['estado'])
                    );

                    ?>

                    <article class="tarjeta servicio-cliente">

                        <div class="servicio-cliente-cabecera">

                            <div class="tarjeta-icono">
                                🌐
                            </div>

                            <span class="estado estado-<?php
                                echo escapar($claseEstado);
                            ?>">
                                <?php echo escapar($servicio['estado']); ?>
                            </span>

                        </div>

                        <h3>
                            <?php echo escapar($servicio['nombre']); ?>
                        </h3>

                        <p>
                            <?php echo escapar($servicio['descripcion']); ?>
                        </p>

                        <div class="servicio-detalle">

                            <span>
                                Precio
                            </span>

                            <strong>
                                <?php echo formatearPrecio(
                                    $servicio['precio']
                                ); ?>
                            </strong>

                        </div>

                        <div class="servicio-detalle">

                            <span>
                                Fecha de asignación
                            </span>

                            <strong>
                                <?php echo date(
                                    'd/m/Y',
                                    strtotime($servicio['fecha_compra'])
                                ); ?>
                            </strong>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>

<section class="seccion-pequena">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Seguimiento
                </span>

                <h2>
                    Solicitudes recientes
                </h2>

                <p>
                    Revisa las últimas solicitudes enviadas.
                </p>

            </div>

            <a
                href="<?php echo APP_URL; ?>/usuario/solicitudes.php"
                class="boton boton-secundario boton-pequeno"
            >
                Ver historial
            </a>

        </div>

        <?php if (empty($solicitudesUsuario)): ?>

            <div class="estado-vacio estado-vacio-compacto">

                <div class="estado-vacio-icono">
                    📋
                </div>

                <h3>
                    No has enviado solicitudes
                </h3>

                <p>
                    Cuando solicites un plan, aparecerá en esta sección.
                </p>

            </div>

        <?php else: ?>

            <div class="tabla-contenedor">

                <table class="tabla">

                    <thead>

                        <tr>

                            <th>
                                Servicio
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($solicitudesUsuario as $solicitud): ?>

                            <?php

                            $claseEstado = str_replace(
                                [' ', 'ó'],
                                ['-', 'o'],
                                strtolower($solicitud['estado'])
                            );

                            ?>

                            <tr>

                                <td>
                                    <?php echo escapar(
                                        $solicitud['servicio']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo date(
                                        'd/m/Y',
                                        strtotime(
                                            $solicitud['fecha_solicitud']
                                        )
                                    ); ?>
                                </td>

                                <td>

                                    <span class="estado estado-<?php
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

    </div>

</section>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>