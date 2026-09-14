<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/csrf.php';

$tituloPagina = 'Digital Brand - Contacto';

$tokenCsrf = obtenerTokenCsrf();

require_once __DIR__ . '/includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="encabezado-seccion">

            <span class="etiqueta-superior">
                Contacto
            </span>

            <h1>Hablemos sobre tu proyecto</h1>

            <p>
                Cuéntanos qué necesita tu negocio y te responderemos
                lo más pronto posible.
            </p>

        </div>

        <div class="cuadricula-dos">

            <section class="tarjeta">

                <div class="tarjeta-icono">
                    <img src="<?php echo APP_URL; ?>/assets/img/emojis/contacto.png" 
                         alt="Icono de contacto">
                </div>

                <h2>Información de contacto</h2>

                <p>
                    Puedes comunicarte con Digital Brand por cualquiera
                    de estos medios.
                </p>

                <div class="datos-grid">

                    <div class="dato-item">

                        <span>Ubicación</span>

                        <strong>
                            Copacabana, Antioquia
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>Teléfono</span>

                        <strong>
                            +57 333 284 3241
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>Correo</span>

                        <strong>
                            DigitalBrandpaginaseficientes@gmail.com
                        </strong>

                    </div>

                    <div class="dato-item">

                        <span>Horario</span>

                        <strong>
                            Lunes a viernes, 8:00 a. m. a 5:00 p. m.
                        </strong>

                    </div>

                </div>

                <div style="margin-top: 24px;">

                    <a
                        href="https://wa.me/573332843241?text=Hola,%20quiero%20más%20información%20sobre%20Digital%20Brand"
                        class="boton boton-ancho"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Escribir por WhatsApp
                    </a>

                </div>

            </section>

            <section class="formulario">

                <h2>Enviar mensaje</h2>

                <p>
                    Completa los datos y nuestro equipo revisará tu solicitud.
                </p>

                <form id="form-contacto" novalidate>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo escapar($tokenCsrf); ?>"
                    >

                    <div class="grupo-campo">

                        <label for="contacto-nombre">
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            id="contacto-nombre"
                            name="nombre"
                            maxlength="100"
                            autocomplete="name"
                            placeholder="Ejemplo: María González"
                            value="<?php
                                echo isset($_SESSION['nombre'])
                                    ? escapar($_SESSION['nombre'])
                                    : '';
                            ?>"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="contacto-correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="contacto-correo"
                            name="correo"
                            maxlength="150"
                            autocomplete="email"
                            placeholder="correo@ejemplo.com"
                            value="<?php
                                echo isset($_SESSION['correo'])
                                    ? escapar($_SESSION['correo'])
                                    : '';
                            ?>"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="contacto-telefono">
                            Teléfono
                        </label>

                        <input
                            type="tel"
                            id="contacto-telefono"
                            name="telefono"
                            maxlength="20"
                            autocomplete="tel"
                            placeholder="Ejemplo: 3001234567"
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="contacto-asunto">
                            Asunto
                        </label>

                        <input
                            type="text"
                            id="contacto-asunto"
                            name="asunto"
                            maxlength="150"
                            placeholder="Ejemplo: Información sobre el plan corporativo"
                            required
                        >

                    </div>

                    <div class="grupo-campo">

                        <label for="contacto-mensaje">
                            Mensaje
                        </label>

                        <textarea
                            id="contacto-mensaje"
                            name="mensaje"
                            rows="7"
                            minlength="10"
                            maxlength="2000"
                            placeholder="Describe brevemente lo que necesitas..."
                            required
                        ></textarea>

                        <small class="ayuda-campo">
                            Escribe mínimo 10 caracteres.
                        </small>

                    </div>

                    <div
                        id="mensaje-contacto"
                        class="alerta"
                        role="alert"
                        aria-live="polite"
                        style="display:none;"
                    ></div>

                    <button
                        type="submit"
                        class="boton boton-ancho"
                        id="boton-contacto"
                    >
                        Enviar mensaje
                    </button>

                </form>

            </section>

        </div>

    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const formulario =
        document.getElementById("form-contacto");

    const mensaje =
        document.getElementById("mensaje-contacto");

    const boton =
        document.getElementById("boton-contacto");

    if (!formulario || !mensaje || !boton) {
        return;
    }

    function mostrarMensaje(texto, exitoso) {

        mensaje.style.display = "block";

        mensaje.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensaje.textContent = texto;
    }

    formulario.addEventListener("submit", async function (evento) {

        evento.preventDefault();

        mensaje.style.display = "none";

        const nombre =
            document.getElementById("contacto-nombre").value.trim();

        const correo =
            document.getElementById("contacto-correo").value.trim();

        const asunto =
            document.getElementById("contacto-asunto").value.trim();

        const textoMensaje =
            document.getElementById("contacto-mensaje").value.trim();

        if (nombre.length < 3) {

            mostrarMensaje(
                "El nombre debe tener mínimo 3 caracteres.",
                false
            );

            return;
        }

        if (!correo.includes("@")) {

            mostrarMensaje(
                "Escribe un correo electrónico válido.",
                false
            );

            return;
        }

        if (asunto.length < 3) {

            mostrarMensaje(
                "El asunto debe tener mínimo 3 caracteres.",
                false
            );

            return;
        }

        if (textoMensaje.length < 10) {

            mostrarMensaje(
                "El mensaje debe tener mínimo 10 caracteres.",
                false
            );

            return;
        }

        boton.disabled = true;
        boton.textContent = "Enviando...";

        try {

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/procesar_contacto.php",
                {
                    method: "POST",
                    body: new FormData(formulario),
                    cache: "no-store"
                }
            );

            const texto = await respuesta.text();

            let resultado;

            try {

                resultado = JSON.parse(texto);

            } catch (errorJson) {

                console.error(texto);

                mostrarMensaje(
                    "El servidor devolvió una respuesta no válida.",
                    false
                );

                return;
            }

            mostrarMensaje(
                resultado.msg || "Respuesta recibida.",
                resultado.ok === true
            );

            if (resultado.ok === true) {

                formulario.reset();

                <?php if (usuarioAutenticado()): ?>

                document.getElementById("contacto-nombre").value =
                    "<?php echo escapar($_SESSION['nombre']); ?>";

                document.getElementById("contacto-correo").value =
                    "<?php echo escapar($_SESSION['correo']); ?>";

                <?php endif; ?>
            }

        } catch (error) {

            console.error(error);

            mostrarMensaje(
                "No fue posible comunicarse con el servidor.",
                false
            );

        } finally {

            boton.disabled = false;
            boton.textContent = "Enviar mensaje";
        }
    });
});
</script>

<?php

require_once __DIR__ . '/includes/footer.php';

?>