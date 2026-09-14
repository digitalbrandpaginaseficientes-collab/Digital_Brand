<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';

$tituloPagina = 'Digital Brand - Portafolio';

$proyectos = [
    [
        'id' => 1,
        'nombre' => 'Panadería La Esperanza',
        'categoria' => 'comercio',
        'categoria_nombre' => 'Comercio',
        'icono' => '🥖',
        'imagen' => 'panaderia.jpg',
        'descripcion' => 'Sitio web para presentar productos, horarios, ubicación y medios de contacto de una panadería local.',
        'objetivo' => 'Mejorar la visibilidad del negocio y facilitar que los clientes consulten sus productos antes de visitar el establecimiento.',
        'solucion' => 'Se diseñó una página adaptable con catálogo visual, información de contacto, ubicación y acceso directo a WhatsApp.',
        'tecnologias' => 'HTML|CSS|JavaScript|PHP',
        'caracteristicas' => 'Diseño adaptable|Catálogo de productos|Botón de WhatsApp|Información de horarios|Ubicación del negocio',
        'resultado' => 'Una presencia digital clara y accesible para promocionar los productos del negocio.',
        'clase_visual' => 'proyecto-panaderia'
    ],
    [
        'id' => 2,
        'nombre' => 'Moda Urbana',
        'categoria' => 'catalogo',
        'categoria_nombre' => 'Catálogo',
        'icono' => '👕',
        'imagen' => 'moda.jpg',
        'descripcion' => 'Catálogo digital para un emprendimiento dedicado a la comercialización de ropa y accesorios.',
        'objetivo' => 'Organizar los productos por categorías y permitir que los clientes consulten las prendas disponibles.',
        'solucion' => 'Se creó un catálogo visual con categorías, fichas de producto y contacto directo para realizar pedidos.',
        'tecnologias' => 'HTML|CSS|JavaScript|PHP|MySQL',
        'caracteristicas' => 'Catálogo organizado|Filtros por categoría|Diseño móvil|Fichas de productos|Contacto para pedidos',
        'resultado' => 'Un escaparate digital organizado que facilita la consulta y promoción de los productos.',
        'clase_visual' => 'proyecto-moda'
    ],
    [
        'id' => 3,
        'nombre' => 'Café Montaña',
        'categoria' => 'corporativo',
        'categoria_nombre' => 'Corporativo',
        'icono' => '☕',
        'imagen' => 'cafe.jpg',
        'descripcion' => 'Página corporativa para una marca local dedicada a la producción y comercialización de café.',
        'objetivo' => 'Comunicar la historia de la marca, sus valores y las características principales de sus productos.',
        'solucion' => 'Se desarrolló un sitio institucional con historia, productos destacados, proceso de producción y formulario de contacto.',
        'tecnologias' => 'HTML|CSS|JavaScript|PHP',
        'caracteristicas' => 'Historia empresarial|Productos destacados|Formulario de contacto|Diseño corporativo|Sección de valores',
        'resultado' => 'Una identidad digital coherente con el origen y los valores de la marca.',
        'clase_visual' => 'proyecto-cafe'
    ],
    [
        'id' => 4,
        'nombre' => 'Belleza Natural',
        'categoria' => 'servicios',
        'categoria_nombre' => 'Servicios',
        'icono' => '💇',
        'imagen' => 'belleza.jpg',
        'descripcion' => 'Sitio informativo para un emprendimiento de belleza, cuidado personal y servicios estéticos.',
        'objetivo' => 'Presentar los servicios, precios de referencia y canales disponibles para solicitar una cita.',
        'solucion' => 'Se diseñó una página visual con servicios organizados, promociones, testimonios y botón para reservar por WhatsApp.',
        'tecnologias' => 'HTML|CSS|JavaScript|PHP',
        'caracteristicas' => 'Listado de servicios|Promociones|Reservas por WhatsApp|Testimonios|Diseño adaptable',
        'resultado' => 'Una página atractiva que mejora la presentación de los servicios y facilita las reservas.',
        'clase_visual' => 'proyecto-belleza'
    ],
    [
        'id' => 5,
        'nombre' => 'Soluciones Contables JG',
        'categoria' => 'corporativo',
        'categoria_nombre' => 'Corporativo',
        'icono' => '📊',
        'imagen' => 'contable.jpg',
        'descripcion' => 'Página profesional para un servicio independiente de asesoría contable y tributaria.',
        'objetivo' => 'Generar confianza y presentar claramente los servicios ofrecidos a personas y pequeñas empresas.',
        'solucion' => 'Se creó un sitio profesional con presentación del asesor, servicios, preguntas frecuentes y formulario de contacto.',
        'tecnologias' => 'HTML|CSS|JavaScript|PHP|MySQL',
        'caracteristicas' => 'Presentación profesional|Servicios contables|Preguntas frecuentes|Formulario seguro|Panel de mensajes',
        'resultado' => 'Un canal digital formal para captar clientes y atender solicitudes de asesoría.',
        'clase_visual' => 'proyecto-contable'
    ],
    [
        'id' => 6,
        'nombre' => 'Mascotas Felices',
        'categoria' => 'comercio',
        'categoria_nombre' => 'Comercio',
        'icono' => '🐾',
        'imagen' => 'mascotas.jpg',
        'descripcion' => 'Catálogo para una tienda local de alimentos, accesorios y productos para mascotas.',
        'objetivo' => 'Dar visibilidad a los productos y facilitar la comunicación con los propietarios de mascotas.',
        'solucion' => 'Se implementó un catálogo por tipos de mascota, productos destacados y solicitudes mediante WhatsApp.',
        'tecnologias' => 'HTML|CSS|JavaScript|PHP|MySQL',
        'caracteristicas' => 'Categorías de productos|Productos destacados|Buscador|Contacto por WhatsApp|Diseño móvil',
        'resultado' => 'Una vitrina digital funcional para promocionar los productos disponibles.',
        'clase_visual' => 'proyecto-mascotas'
    ]
];

require_once __DIR__ . '/includes/header.php';

?>

<section class="portafolio-hero">

    <div class="contenedor portafolio-hero-grid">

        <div>

            <span class="etiqueta-superior">
                Nuestro trabajo
            </span>

            <h1>
                Soluciones digitales pensadas para negocios reales
            </h1>

            <p>
                Conoce algunos ejemplos de páginas que Digital Brand puede
                desarrollar para comercios, emprendimientos, profesionales
                y microempresas.
            </p>

            <div class="hero-botones">

                <a href="#proyectos" class="boton">
                    Ver proyectos
                </a>

                <a
                    href="<?php echo APP_URL; ?>/contacto.php"
                    class="boton boton-secundario"
                >
                    Cotizar mi página
                </a>

            </div>

        </div>

        <div class="portafolio-presentacion">

            <div class="portafolio-ventana">

                <div class="portafolio-ventana-barra">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="portafolio-ventana-contenido">

                    <div class="portafolio-marca">
                        <img src="<?php echo APP_URL; ?>/assets/img/logo.jpeg"
                        alt="Logo de Digital Brand">
                    </div>

                    <h2>Diseño que comunica</h2>

                    <p>
                        Cada página se adapta a la identidad y las
                        necesidades de cada negocio.
                    </p>

                    <div class="portafolio-miniaturas">
                        <div>🏪</div>
                        <div>📱</div>
                        <div>🌐</div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<section class="seccion" id="proyectos">

    <div class="contenedor">

        <div class="encabezado-seccion">

            <span class="etiqueta-superior">
                Proyectos de ejemplo
            </span>

            <h2>Explora diferentes tipos de soluciones</h2>

            <p>
                Utiliza los filtros para consultar proyectos según el tipo
                de negocio o necesidad.
            </p>

        </div>

        <div
            class="filtros-portafolio"
            role="group"
            aria-label="Filtrar proyectos"
        >

            <button
                type="button"
                class="filtro-portafolio activo"
                data-categoria="todos"
            >
                Todos
            </button>

            <button
                type="button"
                class="filtro-portafolio"
                data-categoria="comercio"
            >
                Comercio
            </button>

            <button
                type="button"
                class="filtro-portafolio"
                data-categoria="catalogo"
            >
                Catálogos
            </button>

            <button
                type="button"
                class="filtro-portafolio"
                data-categoria="corporativo"
            >
                Corporativos
            </button>

            <button
                type="button"
                class="filtro-portafolio"
                data-categoria="servicios"
            >
                Servicios
            </button>

        </div>

        <div class="portafolio-grid" id="portafolio-grid">

            <?php foreach ($proyectos as $proyecto): ?>

                <?php

                $rutaImagen = APP_URL .
                    '/assets/img/portafolio/' .
                    $proyecto['imagen'];

                ?>

                <article
                    class="proyecto-card"
                    data-proyecto
                    data-categoria="<?php
                        echo escapar($proyecto['categoria']);
                    ?>"
                >

                    <div class="proyecto-imagen proyecto-imagen-real">

    <img
        src="<?php echo escapar($rutaImagen); ?>"
        alt="Vista del proyecto <?php
            echo escapar($proyecto['nombre']);
        ?>"
        class="imagen-portafolio"
        loading="lazy"
    >

</div>

                    <div class="proyecto-contenido">

                        <span class="proyecto-categoria">
                            <?php echo escapar(
                                $proyecto['categoria_nombre']
                            ); ?>
                        </span>

                        <h3>
                            <?php echo escapar($proyecto['nombre']); ?>
                        </h3>

                        <p>
                            <?php echo escapar($proyecto['descripcion']); ?>
                        </p>

                        <div class="proyecto-tecnologias">

                            <?php foreach (
                                explode('|', $proyecto['tecnologias'])
                                as $tecnologia
                            ): ?>

                                <span>
                                    <?php echo escapar($tecnologia); ?>
                                </span>

                            <?php endforeach; ?>

                        </div>

                        <button
                            type="button"
                            class="boton boton-secundario boton-ancho ver-proyecto"
                            data-proyecto="<?php
                                echo escapar(
                                    json_encode(
                                        $proyecto,
                                        JSON_UNESCAPED_UNICODE |
                                        JSON_UNESCAPED_SLASHES
                                    )
                                );
                            ?>"
                        >
                            Ver detalles
                        </button>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

        <div
            id="sin-proyectos"
            class="estado-vacio"
            style="display:none;"
        >

            <div class="estado-vacio-icono">
                🔍
            </div>

            <h3>No encontramos proyectos</h3>

            <p>
                Selecciona otra categoría para consultar más ejemplos.
            </p>

        </div>

    </div>

</section>

<section class="seccion seccion-clara">

    <div class="contenedor">

        <div class="encabezado-seccion">

            <span class="etiqueta-superior">
                Nuestro proceso
            </span>

            <h2>¿Cómo desarrollamos una página web?</h2>

            <p>
                Aplicamos un proceso organizado que permite comprender la
                necesidad del cliente, diseñar la solución y comprobar su
                funcionamiento.
            </p>

        </div>

        <div class="proceso-portafolio">

            <?php

            $pasos = [
                [
                    'numero' => '01',
                    'icono' => '<img src="' . APP_URL . '/assets/img/emojis/in.png" alt="Icono de investigación">',
                    'titulo' => 'Investigación',
                    'texto' => 'Identificamos las necesidades, el público y los objetivos principales del negocio.'
                ],
                [
                    'numero' => '02',
                    'icono' => '<img src="' . APP_URL . '/assets/img/emojis/pla.png" alt="Icono de planificación">',
                    'titulo' => 'Planificación',
                    'texto' => 'Definimos la estructura, las secciones, los contenidos y las funciones necesarias.'
                ],
                [
                    'numero' => '03',
                    'icono' => '<img src="' . APP_URL . '/assets/img/emojis/vs.png" alt="Icono de diseño">',
                    'titulo' => 'Diseño',
                    'texto' => 'Creamos una interfaz visual coherente con la identidad y los colores del negocio.'
                ],
                [
                    'numero' => '04',
                    'icono' => '<img src="' . APP_URL . '/assets/img/emojis/dss.png" alt="Icono de desarrollo">',
                    'titulo' => 'Desarrollo',
                    'texto' => 'Programamos la página, sus formularios, paneles y conexión con la base de datos.'
                ],
                [
                    'numero' => '05',
                    'icono' => '<img src="' . APP_URL . '/assets/img/emojis/cal.png" alt="Icono de pruebas">',
                    'titulo' => 'Pruebas',
                    'texto' => 'Verificamos el funcionamiento, la seguridad y la adaptación a celulares y computadores.'
                ],
                [
                    'numero' => '06',
                    'icono' => '<img src="' . APP_URL . '/assets/img/emojis/ent.png" alt="Icono de entrega">',
                    'titulo' => 'Entrega',
                    'texto' => 'Publicamos la solución y brindamos orientación para su uso y mantenimiento.'
                ]
            ];

            ?>

            <?php foreach ($pasos as $paso): ?>

                <article class="paso-portafolio">

                    <span class="paso-numero">
                        <?php echo escapar($paso['numero']); ?>
                    </span>

                    <div class="paso-icono">
                        <?php echo $paso['icono']; ?>
                    </div>

                    <h3>
                        <?php echo escapar($paso['titulo']); ?>
                    </h3>

                    <p>
                        <?php echo escapar($paso['texto']); ?>
                    </p>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<section class="seccion">

    <div class="contenedor llamada-accion">

        <div>

            <span class="etiqueta-superior etiqueta-clara">
                Convierte tu idea en una página
            </span>

            <h2>
                Tu negocio también puede tener una presencia digital
                profesional
            </h2>

            <p>
                Regístrate para solicitar un plan o escríbenos para contarnos
                qué necesitas.
            </p>

        </div>

        <div class="cta-portafolio-botones">

            <?php if (
                usuarioAutenticado() &&
                !esAdministrador()
            ): ?>

                <a
                    href="<?php echo APP_URL; ?>/usuario/servicios.php"
                    class="boton boton-claro"
                >
                    Solicitar un plan
                </a>

            <?php elseif (!usuarioAutenticado()): ?>

                <a
                    href="<?php echo APP_URL; ?>/registro.php"
                    class="boton boton-claro"
                >
                    Crear una cuenta
                </a>

            <?php endif; ?>

            <a
                href="<?php echo APP_URL; ?>/contacto.php"
                class="boton boton-cta-secundario"
            >
                Contactarnos
            </a>

        </div>

    </div>

</section>

<div
    id="modal-proyecto"
    class="modal"
    aria-hidden="true"
>

    <div
        class="modal-card modal-proyecto-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="detalle-proyecto-nombre"
    >

        <div
            id="detalle-proyecto-visual"
            class="detalle-proyecto-visual"
        >

            <img
                id="detalle-proyecto-imagen"
                class="detalle-proyecto-imagen"
                src=""
                alt=""
            >

            <span
                id="detalle-proyecto-icono"
                class="detalle-proyecto-icono"
            ></span>

        </div>

        <span
            id="detalle-proyecto-categoria"
            class="proyecto-categoria"
        ></span>

        <h2 id="detalle-proyecto-nombre"></h2>

        <p id="detalle-proyecto-descripcion"></p>

        <div class="detalle-proyecto-grid">

            <div class="detalle-proyecto-bloque">

                <h3>Objetivo</h3>

                <p id="detalle-proyecto-objetivo"></p>

            </div>

            <div class="detalle-proyecto-bloque">

                <h3>Solución desarrollada</h3>

                <p id="detalle-proyecto-solucion"></p>

            </div>

        </div>

        <div class="detalle-proyecto-bloque">

            <h3>Características</h3>

            <ul
                id="detalle-proyecto-caracteristicas"
                class="detalle-lista"
            ></ul>

        </div>

        <div class="detalle-proyecto-bloque">

            <h3>Tecnologías utilizadas</h3>

            <div
                id="detalle-proyecto-tecnologias"
                class="proyecto-tecnologias"
            ></div>

        </div>

        <div class="detalle-proyecto-bloque resultado-proyecto">

            <h3>Resultado</h3>

            <p id="detalle-proyecto-resultado"></p>

        </div>

        <div class="modal-proyecto-acciones">

            <a
                href="<?php echo APP_URL; ?>/contacto.php"
                class="boton"
            >
                Solicitar algo similar
            </a>

            <button
                type="button"
                class="boton boton-secundario"
                id="cerrar-modal-proyecto"
            >
                Cerrar
            </button>

        </div>

    </div>

</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const rutaImagenes =
        "<?php echo APP_URL; ?>/assets/img/portafolio/";

    const botonesFiltro =
        document.querySelectorAll(".filtro-portafolio");

    const proyectos =
        document.querySelectorAll("[data-proyecto]");

    const sinProyectos =
        document.getElementById("sin-proyectos");

    const modal =
        document.getElementById("modal-proyecto");

    const cerrarModal =
        document.getElementById("cerrar-modal-proyecto");

    const detalleVisual =
        document.getElementById("detalle-proyecto-visual");

    const detalleImagen =
        document.getElementById("detalle-proyecto-imagen");

    const detalleIcono =
        document.getElementById("detalle-proyecto-icono");

    const detalleCategoria =
        document.getElementById("detalle-proyecto-categoria");

    const detalleNombre =
        document.getElementById("detalle-proyecto-nombre");

    const detalleDescripcion =
        document.getElementById("detalle-proyecto-descripcion");

    const detalleObjetivo =
        document.getElementById("detalle-proyecto-objetivo");

    const detalleSolucion =
        document.getElementById("detalle-proyecto-solucion");

    const detalleCaracteristicas =
        document.getElementById(
            "detalle-proyecto-caracteristicas"
        );

    const detalleTecnologias =
        document.getElementById(
            "detalle-proyecto-tecnologias"
        );

    const detalleResultado =
        document.getElementById(
            "detalle-proyecto-resultado"
        );

    botonesFiltro.forEach(function (boton) {

        boton.addEventListener("click", function () {

            const categoria =
                boton.dataset.categoria;

            botonesFiltro.forEach(function (item) {
                item.classList.remove("activo");
            });

            boton.classList.add("activo");

            let visibles = 0;

            proyectos.forEach(function (proyecto) {

                const mostrar =
                    categoria === "todos" ||
                    proyecto.dataset.categoria === categoria;

                proyecto.style.display =
                    mostrar ? "" : "none";

                if (mostrar) {
                    visibles++;
                }
            });

            if (sinProyectos) {

                sinProyectos.style.display =
                    visibles === 0
                        ? "block"
                        : "none";
            }
        });
    });

    document
        .querySelectorAll(".ver-proyecto")
        .forEach(function (boton) {

            boton.addEventListener("click", function () {

                let proyecto;

                try {

                    proyecto = JSON.parse(
                        boton.dataset.proyecto
                    );

                } catch (error) {

                    console.error(
                        "No fue posible leer el proyecto:",
                        error
                    );

                    return;
                }

                detalleVisual.className =
                    "detalle-proyecto-visual " +
                    proyecto.clase_visual;

                detalleImagen.style.display = "block";

                detalleImagen.src =
                    rutaImagenes + proyecto.imagen;

                detalleImagen.alt =
                    "Vista del proyecto " +
                    proyecto.nombre;

                detalleImagen.onerror = function () {

                    detalleImagen.style.display = "none";
                    detalleIcono.style.display = "block";
                };

                detalleImagen.onload = function () {

                    detalleImagen.style.display = "block";
                    detalleIcono.style.display = "none";
                };

                detalleIcono.textContent =
                    proyecto.icono;

                detalleCategoria.textContent =
                    proyecto.categoria_nombre;

                detalleNombre.textContent =
                    proyecto.nombre;

                detalleDescripcion.textContent =
                    proyecto.descripcion;

                detalleObjetivo.textContent =
                    proyecto.objetivo;

                detalleSolucion.textContent =
                    proyecto.solucion;

                detalleResultado.textContent =
                    proyecto.resultado;

                detalleCaracteristicas.innerHTML = "";

                proyecto.caracteristicas
                    .split("|")
                    .forEach(function (caracteristica) {

                        const elemento =
                            document.createElement("li");

                        elemento.textContent =
                            caracteristica;

                        detalleCaracteristicas.appendChild(
                            elemento
                        );
                    });

                detalleTecnologias.innerHTML = "";

                proyecto.tecnologias
                    .split("|")
                    .forEach(function (tecnologia) {

                        const elemento =
                            document.createElement("span");

                        elemento.textContent =
                            tecnologia;

                        detalleTecnologias.appendChild(
                            elemento
                        );
                    });

                modal.classList.add("show");

                modal.setAttribute(
                    "aria-hidden",
                    "false"
                );

                document.body.style.overflow = "hidden";
            });
        });

    function cerrarDetalle() {

        modal.classList.remove("show");

        modal.setAttribute(
            "aria-hidden",
            "true"
        );

        document.body.style.overflow = "";
    }

    cerrarModal.addEventListener(
        "click",
        cerrarDetalle
    );

    modal.addEventListener(
        "click",
        function (evento) {

            if (evento.target === modal) {
                cerrarDetalle();
            }
        }
    );

    document.addEventListener(
        "keydown",
        function (evento) {

            if (
                evento.key === "Escape" &&
                modal.classList.contains("show")
            ) {
                cerrarDetalle();
            }
        }
    );
});
</script>

<?php

require_once __DIR__ . '/includes/footer.php';

?>