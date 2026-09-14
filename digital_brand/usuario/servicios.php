<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirCliente();

$conexion = Database::conectar();

$consultaServicios = $conexion->prepare("
    SELECT
        s.id_servicio,
        s.nombre,
        s.descripcion,
        s.precio,
        s.caracteristicas,
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM solicitudes_planes sp
                WHERE sp.id_usuario = ?
                  AND sp.id_servicio = s.id_servicio
                  AND sp.estado = 'pendiente'
            )
            THEN 1
            ELSE 0
        END AS tiene_solicitud_pendiente
    FROM servicios s
    WHERE s.estado = 'activo'
    ORDER BY s.precio ASC
");

$consultaServicios->execute([
    $_SESSION['id_usuario']
]);

$servicios = $consultaServicios->fetchAll();

$tituloPagina = 'Digital Brand - Servicios';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="encabezado-seccion">

            <span class="etiqueta-superior">
                Nuestros planes
            </span>

            <h1>Elige el plan ideal</h1>

            <p>
                Selecciona el plan que mejor se adapte a las necesidades
                de tu negocio.
            </p>

        </div>

        <div class="servicios-grid">

            <?php foreach ($servicios as $servicio): ?>

                <?php
                $caracteristicas = explode(
                    '|',
                    $servicio['caracteristicas']
                );
                ?>

                <article class="servicio-card">

                    <div class="servicio-icono">
                        🌐
                    </div>

                    <h3>
                        <?php echo escapar($servicio['nombre']); ?>
                    </h3>

                    <div class="servicio-precio">
                        <?php echo formatearPrecio($servicio['precio']); ?>
                    </div>

                    <p>
                        <?php echo escapar($servicio['descripcion']); ?>
                    </p>

                    <ul>

                        <?php foreach ($caracteristicas as $caracteristica): ?>

                            <li>
                                <?php echo escapar(trim($caracteristica)); ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                    <?php if ((int) $servicio['tiene_solicitud_pendiente'] === 1): ?>

                        <button
                            type="button"
                            class="boton boton-ancho"
                            disabled
                        >
                            Solicitud pendiente
                        </button>

                        <a
                            href="<?php echo APP_URL; ?>/usuario/solicitudes.php"
                            class="boton boton-secundario boton-ancho"
                            style="margin-top: 10px;"
                        >
                            Ver solicitud
                        </a>

                    <?php else: ?>

                        <button
                            type="button"
                            class="boton boton-ancho solicitar-plan"
                            data-id="<?php echo (int) $servicio['id_servicio']; ?>"
                            data-nombre="<?php echo escapar($servicio['nombre']); ?>"
                        >
                            Solicitar plan
                        </button>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<div
    id="modalSolicitud"
    class="modal"
    aria-hidden="true"
>

    <div
        class="modal-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-solicitud"
    >

        <h2 id="titulo-modal-solicitud">
            Solicitar plan
        </h2>

        <p id="nombre-plan-modal">
            Describe brevemente lo que necesita tu negocio.
        </p>

        <form id="formSolicitud" novalidate>

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo escapar(obtenerTokenCsrf()); ?>"
            >

            <input
                type="hidden"
                id="id_servicio"
                name="id_servicio"
                value=""
            >

            <div class="grupo-campo">

                <label for="mensaje-plan">
                    Mensaje para el administrador
                </label>

                <textarea
                    id="mensaje-plan"
                    name="mensaje"
                    rows="6"
                    maxlength="1000"
                    placeholder="Ejemplo: Necesito una página para promocionar mi panadería..."
                    required
                ></textarea>

                <small class="ayuda-campo">
                    Máximo 1000 caracteres.
                </small>

            </div>

            <div
                id="mensaje-solicitud"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display: none;"
            ></div>

            <div
                style="
                    display: flex;
                    gap: 15px;
                    margin-top: 20px;
                    flex-wrap: wrap;
                "
            >

                <button
                    type="submit"
                    class="boton"
                    id="boton-enviar-solicitud"
                >
                    Enviar solicitud
                </button>

                <button
                    type="button"
                    class="boton boton-secundario"
                    id="cerrarModal"
                >
                    Cancelar
                </button>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("modalSolicitud");
    const formulario = document.getElementById("formSolicitud");
    const campoServicio = document.getElementById("id_servicio");
    const campoMensaje = document.getElementById("mensaje-plan");
    const nombrePlan = document.getElementById("nombre-plan-modal");
    const mensajeRespuesta = document.getElementById("mensaje-solicitud");
    const botonCerrar = document.getElementById("cerrarModal");
    const botonEnviar = document.getElementById("boton-enviar-solicitud");
    const botonesPlanes = document.querySelectorAll(".solicitar-plan");

    if (
        !modal ||
        !formulario ||
        !campoServicio ||
        !campoMensaje ||
        !nombrePlan ||
        !mensajeRespuesta ||
        !botonCerrar ||
        !botonEnviar
    ) {
        console.error("No se pudo inicializar el formulario de solicitudes.");
        return;
    }

    function mostrarMensaje(texto, exitoso) {

        mensajeRespuesta.style.display = "block";

        mensajeRespuesta.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensajeRespuesta.textContent = texto;
    }

    function limpiarMensaje() {

        mensajeRespuesta.style.display = "none";
        mensajeRespuesta.className = "alerta";
        mensajeRespuesta.textContent = "";
    }

    function abrirModal(idServicio, nombreServicio) {

        campoServicio.value = idServicio;
        campoMensaje.value = "";

        nombrePlan.textContent =
            "Estás solicitando el plan: " + nombreServicio + ".";

        limpiarMensaje();

        modal.classList.add("show");
        modal.setAttribute("aria-hidden", "false");

        setTimeout(function () {
            campoMensaje.focus();
        }, 100);
    }

    function cerrarModal() {

        modal.classList.remove("show");
        modal.setAttribute("aria-hidden", "true");

        limpiarMensaje();
    }

    botonesPlanes.forEach(function (boton) {

        boton.addEventListener("click", function () {

            abrirModal(
                boton.dataset.id,
                boton.dataset.nombre
            );
        });
    });

    botonCerrar.addEventListener("click", cerrarModal);

    modal.addEventListener("click", function (evento) {

        if (evento.target === modal) {
            cerrarModal();
        }
    });

    document.addEventListener("keydown", function (evento) {

        if (
            evento.key === "Escape" &&
            modal.classList.contains("show")
        ) {
            cerrarModal();
        }
    });

    formulario.addEventListener("submit", async function (evento) {

        evento.preventDefault();

        limpiarMensaje();

        const mensajeEscrito = campoMensaje.value.trim();

        if (campoServicio.value === "") {
            mostrarMensaje("No se seleccionó un servicio.", false);
            return;
        }

        if (mensajeEscrito.length < 10) {
            mostrarMensaje(
                "El mensaje debe tener al menos 10 caracteres.",
                false
            );
            campoMensaje.focus();
            return;
        }

        botonEnviar.disabled = true;
        botonEnviar.textContent = "Enviando...";

        try {

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/solicitar_plan.php",
                {
                    method: "POST",
                    body: new FormData(formulario),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                }
            );

            const textoRespuesta = await respuesta.text();

            let resultado;

            try {
                resultado = JSON.parse(textoRespuesta);
            } catch (errorJson) {
                console.error("Respuesta no válida:", textoRespuesta);

                mostrarMensaje(
                    "El servidor produjo una respuesta no válida.",
                    false
                );

                return;
            }

            mostrarMensaje(
                resultado.msg || "Respuesta recibida.",
                resultado.ok === true
            );

            if (resultado.ok === true) {

                setTimeout(function () {

                    window.location.href =
                        "<?php echo APP_URL; ?>/usuario/solicitudes.php";

                }, 1800);
            }

        } catch (error) {

            console.error("Error al enviar la solicitud:", error);

            mostrarMensaje(
                "No fue posible comunicarse con el servidor.",
                false
            );

        } finally {

            botonEnviar.disabled = false;
            botonEnviar.textContent = "Enviar solicitud";
        }
    });
});
</script>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>