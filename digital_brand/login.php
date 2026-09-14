<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/csrf.php';

if (usuarioAutenticado()) {

    if (esAdministrador()) {
        redirigir(APP_URL . '/admin/index.php');
    }

    redirigir(APP_URL . '/usuario/index.php');
}

$tituloPagina = 'Digital Brand - Iniciar sesión';

$tokenCsrf = obtenerTokenCsrf();

require_once __DIR__ . '/includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="formulario-contenedor">

            <div class="encabezado-seccion">

                <span class="etiqueta-superior">
                    Acceso
                </span>

                <h1>
                    Iniciar sesión
                </h1>

                <p>
                    Ingresa con tu correo y contraseña para consultar tus
                    solicitudes y servicios.
                </p>

            </div>

            <form
                class="formulario"
                id="form-login"
                novalidate
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo escapar($tokenCsrf); ?>"
                >

                <div class="grupo-campo">

                    <label for="correo">
                        Correo electrónico
                    </label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        maxlength="150"
                        autocomplete="email"
                        placeholder="correo@ejemplo.com"
                        required
                    >

                </div>

                <div class="grupo-campo">

                    <label for="contrasena">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        maxlength="72"
                        autocomplete="current-password"
                        placeholder="Escribe tu contraseña"
                        required
                    >

                </div>

                <div class="login-opciones">

                    <a
                        href="<?php echo APP_URL; ?>/recuperar_contrasena.php"
                        class="enlace-recuperacion"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>

                </div>

                <div
                    id="mensaje-login"
                    class="alerta"
                    role="alert"
                    aria-live="polite"
                    style="display: none;"
                ></div>

                <button
                    type="submit"
                    class="boton boton-ancho"
                    id="boton-login"
                >
                    Iniciar sesión
                </button>

                <p class="texto-formulario">

                    ¿No tienes una cuenta?

                    <a
                        href="<?php echo APP_URL; ?>/registro.php"
                        class="enlace-formulario"
                    >
                        Regístrate
                    </a>

                </p>

            </form>

        </div>

    </div>

</section>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const formulario = document.getElementById("form-login");
    const mensaje = document.getElementById("mensaje-login");
    const boton = document.getElementById("boton-login");
    const campoCorreo = document.getElementById("correo");
    const campoContrasena = document.getElementById("contrasena");

    if (
        !formulario ||
        !mensaje ||
        !boton ||
        !campoCorreo ||
        !campoContrasena
    ) {
        console.error(
            "No fue posible inicializar el formulario de inicio de sesión."
        );

        return;
    }

    formulario.addEventListener("submit", async function (evento) {

        evento.preventDefault();

        mensaje.style.display = "none";
        mensaje.className = "alerta";
        mensaje.textContent = "";

        const correo = campoCorreo.value.trim();
        const contrasena = campoContrasena.value;

        if (correo === "" || contrasena === "") {

            mostrarMensaje(
                "Completa el correo y la contraseña.",
                false
            );

            return;
        }

        if (!correo.includes("@")) {

            mostrarMensaje(
                "Escribe un correo electrónico válido.",
                false
            );

            campoCorreo.focus();

            return;
        }

        boton.disabled = true;
        boton.textContent = "Ingresando...";

        try {

            const datos = new FormData(formulario);

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/procesar_login.php",
                {
                    method: "POST",
                    body: datos,
                    cache: "no-store",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                }
            );

            const texto = await respuesta.text();

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
                resultado.msg || "Respuesta recibida.",
                resultado.ok === true
            );

            if (resultado.ok === true) {

                setTimeout(function () {

                    window.location.href =
                        resultado.redirect;

                }, 1000);
            }

        } catch (error) {

            console.error(
                "Error iniciando sesión:",
                error
            );

            mostrarMensaje(
                "No fue posible conectar con el servidor.",
                false
            );

        } finally {

            boton.disabled = false;
            boton.textContent = "Iniciar sesión";
        }
    });

    function mostrarMensaje(texto, exitoso) {

        mensaje.style.display = "block";

        mensaje.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensaje.textContent = texto;
    }
});

</script>

<?php

require_once __DIR__ . '/includes/footer.php';

?>