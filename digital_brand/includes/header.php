<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/funciones.php';

$tituloPagina = $tituloPagina ?? APP_NAME;
$paginaActual = basename($_SERVER['PHP_SELF']);

$usuarioLogueado = usuarioAutenticado();
$usuarioEsAdmin = esAdministrador();

?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Digital Brand crea soluciones web para microempresas y emprendimientos."
    >

    <title>
        <?php echo escapar($tituloPagina); ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo APP_URL; ?>/assets/css/estilo.css"
    >

</head>

<body>

<header class="encabezado">

    <div class="contenedor navegacion">

        <a
            class="marca"
            href="<?php echo APP_URL; ?>/index.php"
            aria-label="Ir al inicio de Digital Brand"
        >

            <span class="marca-logo">
                <img src="<?php echo APP_URL; ?>/assets/img/logo.jpeg" 
                alt="Logo de Digital Brand">
            </span>

            <span class="marca-texto">
                Digital Brand
            </span>

        </a>

        <button
            type="button"
            class="boton-menu"
            id="boton-menu"
            aria-label="Abrir menú"
            aria-expanded="false"
            aria-controls="menu-principal"
        >
            ☰
        </button>

        <nav
            class="menu-principal"
            id="menu-principal"
            aria-label="Navegación principal"
        >

            <a
                href="<?php echo APP_URL; ?>/index.php"
                class="<?php
                    echo $paginaActual === 'index.php'
                        ? 'activo'
                        : '';
                ?>"
            >
                Inicio
            </a>

            <a
                href="<?php echo APP_URL; ?>/nosotros.php"
                class="<?php
                    echo $paginaActual === 'nosotros.php'
                        ? 'activo'
                        : '';
                ?>"
            >
                Nosotros
            </a>

            <a
                href="<?php echo APP_URL; ?>/portafolio.php"
                class="<?php
                    echo $paginaActual === 'portafolio.php'
                        ? 'activo'
                        : '';
                ?>"
            >
                Portafolio
            </a>

            <?php if (!$usuarioLogueado): ?>

                <a
                    href="<?php echo APP_URL; ?>/contacto.php"
                    class="<?php
                        echo $paginaActual === 'contacto.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Contacto
                </a>

                <a
                    href="<?php echo APP_URL; ?>/login.php"
                    class="enlace-cuenta <?php
                        echo $paginaActual === 'login.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Iniciar sesión
                </a>

               

            <?php elseif ($usuarioEsAdmin): ?>

                <a
                    href="<?php echo APP_URL; ?>/contacto.php"
                    class="<?php
                        echo $paginaActual === 'contacto.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Contacto
                </a>

                <a
                    href="<?php echo APP_URL; ?>/admin/index.php"
                    class="enlace-cuenta"
                >
                    Panel administrativo
                </a>

                <a
                    href="<?php echo APP_URL; ?>/cerrar_sesion.php"
                    class="boton boton-pequeno boton-salir"
                >
                    Salir
                </a>

            <?php else: ?>

                <a
                    href="<?php echo APP_URL; ?>/usuario/servicios.php"
                    class="<?php
                        echo $paginaActual === 'servicios.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Servicios
                </a>

                <a
                    href="<?php echo APP_URL; ?>/usuario/solicitudes.php"
                    class="<?php
                        echo $paginaActual === 'solicitudes.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Mis solicitudes
                </a>

                <a
                    href="<?php
                        echo APP_URL;
                    ?>/usuario/servicios_contratados.php"
                    class="<?php
                        echo $paginaActual ===
                            'servicios_contratados.php'
                                ? 'activo'
                                : '';
                    ?>"
                >
                    Mis servicios
                </a>

                <a
                    href="<?php echo APP_URL; ?>/usuario/perfil.php"
                    class="<?php
                        echo $paginaActual === 'perfil.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Mi cuenta
                </a>

                <a
                    href="<?php echo APP_URL; ?>/contacto.php"
                    class="<?php
                        echo $paginaActual === 'contacto.php'
                            ? 'activo'
                            : '';
                    ?>"
                >
                    Contacto
                </a>

                <a
                    href="<?php echo APP_URL; ?>/cerrar_sesion.php"
                    class="boton boton-pequeno boton-salir"
                >
                    Salir
                </a>

            <?php endif; ?>

            <button
                type="button"
                id="btn-modo"
                class="boton-modo"
                aria-label="Cambiar modo de color"
                title="Cambiar modo"
            >
                🌙
            </button>

        </nav>

    </div>

</header>

<main class="contenido-principal">

<?php if (!empty($_SESSION['mensaje_exito'])): ?>

    <div class="contenedor">

        <div class="alerta alerta-exito">

            <?php

            echo escapar(
                $_SESSION['mensaje_exito']
            );

            unset(
                $_SESSION['mensaje_exito']
            );

            ?>

        </div>

    </div>

<?php endif; ?>

<?php if (!empty($_SESSION['mensaje_error'])): ?>

    <div class="contenedor">

        <div class="alerta alerta-error">

            <?php

            echo escapar(
                $_SESSION['mensaje_error']
            );

            unset(
                $_SESSION['mensaje_error']
            );

            ?>

        </div>

    </div>

<?php endif; ?>