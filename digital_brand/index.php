<?php

$tituloPagina = 'Digital Brand - Inicio';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

$conexion = Database::conectar();

$totalServicios = $conexion->query(
    "SELECT COUNT(*) FROM servicios WHERE estado = 'activo'"
)->fetchColumn();

$totalUsuarios = $conexion->query(
    "SELECT COUNT(*) FROM usuarios WHERE estado = 'activo'"
)->fetchColumn();

$totalSolicitudesAtendidas = $conexion->query(
    "SELECT COUNT(*) FROM solicitudes_planes WHERE estado = 'atendida'"
)->fetchColumn();

$servicios = $conexion->query(
    "SELECT id_servicio, nombre, descripcion, precio, caracteristicas
     FROM servicios
     WHERE estado = 'activo'
     ORDER BY precio ASC"
)->fetchAll();

?>

<section class="hero-principal">

    <div class="contenedor hero-contenido">

        <div class="hero-texto">

            <span class="etiqueta-superior">
                Soluciones web para microempresas
            </span>

            <h1>
                Tu marca digital,
                <span>hecha para crecer</span>
            </h1>

            <p>
                Diseñamos páginas web profesionales, modernas y adaptables
                para que los negocios de Copacabana puedan tener una mejor
                presencia en internet.
            </p>

            <div class="hero-botones">

                <a
                    href="<?php echo APP_URL; ?>/registro.php"
                    class="boton"
                >
                    Crear una cuenta
                </a>

                <a
                    href="#planes"
                    class="boton boton-secundario"
                >
                    Ver planes
                </a>

            </div>

            <div class="hero-confianza">

                <span>✓ Diseño adaptable</span>
                <span>✓ Atención personalizada</span>
                <span>✓ Soporte técnico</span>

            </div>

        </div>

        <div class="hero-visual">

            <div class="ventana-web">

                <div class="ventana-barra">

                    <span></span>
                    <span></span>
                    <span></span>

                </div>

                <div class="ventana-contenido">

                    <div class="vista-logo">
                        <img src="<?php echo APP_URL; ?>/assets/img/logo.jpeg"
                        alt="Logo de Digital Brand">
                    </div>

                    <h2>Tu negocio en internet</h2>

                    <p>
                        Una página clara, moderna y preparada para celulares.
                    </p>

                    <div class="vista-boton">
                        Conocer servicios
                    </div>

                    <div class="vista-tarjetas">

                        <div></div>
                        <div></div>
                        <div></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="seccion seccion-clara">

    <div class="contenedor">

        <div class="estadisticas-grid">

            <article class="estadistica">

                <strong>
                    <?php echo (int) $totalServicios; ?>
                </strong>

                <span>
                    Planes disponibles
                </span>

            </article>

            <article class="estadistica">

                <strong>
                    <?php echo (int) $totalUsuarios; ?>
                </strong>

                <span>
                    Usuarios registrados
                </span>

            </article>

            <article class="estadistica">

                <strong>
                    <?php echo (int) $totalSolicitudesAtendidas; ?>
                </strong>

                <span>
                    Solicitudes atendidas
                </span>

            </article>

            <article class="estadistica">

                <strong>
                    100%
                </strong>

                <span>
                    Diseño adaptable
                </span>

            </article>

        </div>

    </div>

</section>

<section class="seccion">

    <div class="contenedor">

        <div class="encabezado-seccion">

            <span class="etiqueta-superior">
                ¿Qué hacemos?
            </span>

            <h2>
                Soluciones digitales para negocios locales
            </h2>

            <p>
                Acompañamos a microempresas y emprendimientos desde la idea
                inicial hasta la publicación de su sitio web.
            </p>

        </div>

        <div class="cuadricula">

            <article class="tarjeta">

                <div class="tarjeta-icono">
                    <img src="<?php echo APP_URL; ?>/assets/img/emojis/ds.png" 
                         alt="Icono de desarrollo web">
                </div>

                <h3>
                    Desarrollo web
                </h3>

                <p>
                    Creamos páginas modernas, rápidas y preparadas para
                    computadores, tabletas y celulares.
                </p>

            </article>

            <article class="tarjeta">

                <div class="tarjeta-icono">
                    <img src="<?php echo APP_URL; ?>/assets/img/emojis/vs.png" 
                         alt="Icono de diseño visual">
                </div>

                <h3>
                    Diseño visual
                </h3>

                <p>
                    Organizamos colores, textos, imágenes y contenidos para
                    presentar cada negocio de manera profesional.
                </p>

            </article>

            <article class="tarjeta">

                <div class="tarjeta-icono">
                    <img src="<?php echo APP_URL; ?>/assets/img/emojis/st.png" 
                         alt="Icono de soporte técnico">
                </div>

                <h3>
                    Soporte técnico
                </h3>

                <p>
                    Brindamos acompañamiento para mantener la información,
                    servicios y canales de contacto actualizados.
                </p>

            </article>

        </div>

    </div>

</section>

<section class="seccion seccion-clara" id="planes">

    <div class="contenedor">

        <div class="encabezado-seccion">

            <span class="etiqueta-superior">
                Planes
            </span>

            <h2>
                Escoge la solución ideal
            </h2>

            <p>
                Cada plan está pensado para una necesidad diferente.
            </p>

        </div>

        <div class="planes-grid">

            <?php foreach ($servicios as $indice => $servicio): ?>

                <?php

                $caracteristicas = explode(
                    '|',
                    $servicio['caracteristicas']
                );

                ?>

                <article
                    class="plan-tarjeta <?php echo $indice === 1 ? 'plan-destacado' : ''; ?>"
                >

                    <?php if ($indice === 1): ?>

                        <span class="plan-insignia">
                            Recomendado
                        </span>

                    <?php endif; ?>

                    <h3>
                        <?php echo escapar($servicio['nombre']); ?>
                    </h3>

                    <div class="plan-precio">

                        <?php echo formatearPrecio($servicio['precio']); ?>

                    </div>

                    <p>
                        <?php echo escapar($servicio['descripcion']); ?>
                    </p>

                    <ul>

                        <?php foreach ($caracteristicas as $caracteristica): ?>

                            <li>
                                ✓ <?php echo escapar($caracteristica); ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                    <?php if (usuarioAutenticado()): ?>

                        <a
                            href="<?php echo APP_URL; ?>/usuario/servicios.php"
                            class="boton boton-ancho"
                        >
                            Solicitar este plan
                        </a>

                    <?php else: ?>

                        <a
                            href="<?php echo APP_URL; ?>/registro.php"
                            class="boton boton-ancho"
                        >
                            Registrarme para solicitarlo
                        </a>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<section class="seccion">

    <div class="contenedor llamada-accion">

        <div>

            <span class="etiqueta-superior etiqueta-clara">
                Digital Brand
            </span>

            <h2>
                ¿Listo para mejorar la presencia digital de tu negocio?
            </h2>

            <p>
                Regístrate, consulta nuestros planes y envía tu solicitud
                desde el panel de cliente.
            </p>

        </div>

        <a
            href="<?php echo APP_URL; ?>/registro.php"
            class="boton boton-claro"
        >
            Comenzar ahora
        </a>

    </div>

</section>

<?php

require_once __DIR__ . '/includes/footer.php';

?>