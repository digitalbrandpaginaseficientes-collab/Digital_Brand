<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/csrf.php';

if (usuarioAutenticado()) {

    if (esAdministrador()) {

        header(
            'Location: ' .
            APP_URL .
            '/admin/index.php'
        );

    } else {

        header(
            'Location: ' .
            APP_URL .
            '/usuario/index.php'
        );
    }

    exit;
}

$tituloPagina = 'Digital Brand - Recuperar contraseña';

require_once __DIR__ . '/includes/header.php';

?>

<section class="seccion seccion-recuperacion">

    <div class="contenedor">

        <div class="recuperacion-contenedor">

            <section class="recuperacion-informacion">

                <span class="etiqueta-superior">
                    Seguridad de la cuenta
                </span>

                <h1>
                    Recupera el acceso a tu cuenta
                </h1>

                <p>
                    Escribe el correo electrónico con el que te registraste.
                    Si existe una cuenta asociada, recibirás un enlace
                    temporal para crear una nueva contraseña.
                </p>

                <div class="recuperacion-pasos">

                    <div class="recuperacion-paso">

                        <span>1</span>

                        <div>

                            <strong>
                                Escribe tu correo
                            </strong>

                            <p>
                                Utiliza el mismo correo registrado en
                                Digital Brand.
                            </p>

                        </div>

                    </div>

                    <div class="recuperacion-paso">

                        <span>2</span>

                        <div>

                            <strong>
                                Revisa el mensaje
                            </strong>

                            <p>
                                Recibirás un enlace de recuperación con
                                tiempo limitado.
                            </p>

                        </div>

                    </div>

                    <div class="recuperacion-paso">

                        <span>3</span>

                        <div>

                            <strong>
                                Crea una contraseña nueva
                            </strong>

                            <p>
                                El enlace solo podrá utilizarse una vez.
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            <section class="formulario recuperacion-formulario">

                <div class="recuperacion-icono">
                    🔐
                </div>

                <h2>
                    ¿Olvidaste tu contraseña?
                </h2>

                <p>
                    Te enviaremos las instrucciones de recuperación.
                </p>

                <form
                    id="form-recuperacion"
                    novalidate
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php
                            echo escapar(
                                obtenerTokenCsrf()
                            );
                        ?>"
                    >

                    <div class="grupo-campo">

                        <label for="correo-recuperacion">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="correo-recuperacion"
                            name="correo"
                            maxlength="150"
                            autocomplete="email"
                            placeholder="correo@ejemplo.com"
                            required
                        >

                    </div>

                    <div
                        id="mensaje-recuperacion"
                        class="alerta"
                        role="alert"
                        aria-live="polite"
                        style="display: none;"
                    ></div>

                    <button
                        type="submit"
                        class="boton boton-ancho"
                        id="boton-recuperacion"
                    >
                        Enviar enlace de recuperación
                    </button>

                </form>

                <div class="recuperacion-regreso">

                    <a
                        href="<?php echo APP_URL; ?>/login.php"
                    >
                        ← Volver a iniciar sesión
                    </a>

                </div>

            </section>

        </div>

    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const formulario =
        document.getElementById("form-recuperacion");

    const correo =
        document.getElementById("correo-recuperacion");

    const mensaje =
        document.getElementById("mensaje-recuperacion");

    const boton =
        document.getElementById("boton-recuperacion");

    if (
        !formulario ||
        !correo ||
        !mensaje ||
        !boton
    ) {
        console.error(
            "No fue posible inicializar el formulario de recuperación."
        );

        return;
    }

    function mostrarMensaje(texto, exitoso) {

        mensaje.style.display = "block";

        mensaje.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensaje.textContent = texto;
    }

    formulario.addEventListener(
        "submit",
        async function (evento) {

            evento.preventDefault();

            mensaje.style.display = "none";
            mensaje.textContent = "";

            const correoEscrito =
                correo.value.trim().toLowerCase();

            if (
                correoEscrito === "" ||
                !correoEscrito.includes("@")
            ) {

                mostrarMensaje(
                    "Escribe un correo electrónico válido.",
                    false
                );

                correo.focus();

                return;
            }

            boton.disabled = true;
            boton.textContent = "Procesando...";

            try {

                const respuesta = await fetch(
                    "<?php
                        echo APP_URL;
                    ?>/controllers/solicitar_recuperacion.php",
                    {
                        method: "POST",
                        body: new FormData(formulario),
                        cache: "no-store",
                        headers: {
                            "X-Requested-With":
                                "XMLHttpRequest"
                        }
                    }
                );

                const texto =
                    await respuesta.text();

                let resultado;

                try {

                    resultado = JSON.parse(texto);

                } catch (errorJson) {

                    console.error(
                        "Respuesta no válida:",
                        texto
                    );

                    mostrarMensaje(
                        "El servidor devolvió una respuesta no válida.",
                        false
                    );

                    return;
                }

                mostrarMensaje(
                    resultado.msg ||
                    "Solicitud procesada.",
                    resultado.ok === true
                );

                if (resultado.ok === true) {

                    formulario.reset();
                }

            } catch (error) {

                console.error(
                    "Error solicitando recuperación:",
                    error
                );

                mostrarMensaje(
                    "No fue posible comunicarse con el servidor.",
                    false
                );

            } finally {

                boton.disabled = false;

                boton.textContent =
                    "Enviar enlace de recuperación";
            }
        }
    );
});
</script>

<?php

require_once __DIR__ . '/includes/footer.php';

?>