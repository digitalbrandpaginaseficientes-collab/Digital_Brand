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

$tituloPagina = 'Digital Brand - Registro';

$tokenCsrf = obtenerTokenCsrf();

require_once __DIR__ . '/includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="formulario-contenedor">

            <div class="encabezado-seccion">

                <span class="etiqueta-superior">
                    Crear cuenta
                </span>

                <h1>
                    Regístrate en Digital Brand
                </h1>

                <p>
                    Crea una cuenta para consultar nuestros planes,
                    enviar solicitudes y hacer seguimiento a tus servicios.
                </p>

            </div>

            <form
                class="formulario"
                id="form-registro"
                novalidate
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    id="csrf-token"
                    value="<?php echo escapar($tokenCsrf); ?>"
                >

                <div class="grupo-campo">

                    <label for="nombre">
                        Nombre completo
                    </label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        minlength="3"
                        maxlength="100"
                        autocomplete="name"
                        placeholder="Ejemplo: Carlos Pérez"
                        required
                    >

                    <small class="ayuda-campo">
                        Escribe tu nombre y apellido.
                    </small>

                </div>

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

                    <label for="telefono">
                        Teléfono
                    </label>

                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        maxlength="20"
                        autocomplete="tel"
                        placeholder="Ejemplo: 3001234567"
                    >

                    <small class="ayuda-campo">
                        Este campo es opcional.
                    </small>

                </div>

                <div class="grupo-campo">

                    <label for="empresa">
                        Nombre del negocio o empresa
                    </label>

                    <input
                        type="text"
                        id="empresa"
                        name="empresa"
                        maxlength="120"
                        autocomplete="organization"
                        placeholder="Ejemplo: Panadería La Esperanza"
                    >

                    <small class="ayuda-campo">
                        Este campo es opcional.
                    </small>

                </div>

                <div class="grupo-campo">

                    <label for="contrasena">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        placeholder="Mínimo 8 caracteres"
                        required
                    >

                    <small class="ayuda-campo">
                        Debe tener mínimo 8 caracteres.
                    </small>

                </div>

                <div class="grupo-campo">

                    <label for="confirmar-contrasena">
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        id="confirmar-contrasena"
                        name="confirmar_contrasena"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        placeholder="Repite la contraseña"
                        required
                    >

                </div>

                <div
                    id="mensaje-registro"
                    class="alerta"
                    role="alert"
                    aria-live="polite"
                    style="display: none;"
                ></div>

                <button
                    type="submit"
                    class="boton boton-ancho"
                    id="boton-registro"
                >
                    Crear cuenta
                </button>

                <p class="texto-formulario">

                    ¿Ya tienes una cuenta?

                    <a
                        href="<?php echo APP_URL; ?>/login.php"
                        class="enlace-formulario"
                    >
                        Inicia sesión
                    </a>

                </p>

            </form>

        </div>

    </div>

</section>

<script>

document.addEventListener("DOMContentLoaded", function () {

    const formulario = document.getElementById("form-registro");
    const mensaje = document.getElementById("mensaje-registro");
    const boton = document.getElementById("boton-registro");

    formulario.addEventListener("submit", async function (evento) {

        evento.preventDefault();

        mensaje.style.display = "none";
        mensaje.className = "alerta";
        mensaje.textContent = "";

        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const contrasena = document.getElementById("contrasena").value;
        const confirmarContrasena =
            document.getElementById("confirmar-contrasena").value;

        if (nombre.length < 3) {

            mostrarMensaje(
                "El nombre debe tener al menos 3 caracteres.",
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

        if (contrasena.length < 8) {

            mostrarMensaje(
                "La contraseña debe tener mínimo 8 caracteres.",
                false
            );

            return;
        }

        if (contrasena !== confirmarContrasena) {

            mostrarMensaje(
                "Las contraseñas no coinciden.",
                false
            );

            return;
        }

        boton.disabled = true;
        boton.textContent = "Creando cuenta...";

        try {

            const datos = new FormData(formulario);

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/procesar_registro.php",
                {
                    method: "POST",
                    body: datos
                }
            );

            const resultado = await respuesta.json();

            mostrarMensaje(
                resultado.msg,
                resultado.ok
            );

            if (resultado.ok) {

                formulario.reset();

                setTimeout(function () {

                    window.location.href =
                        "<?php echo APP_URL; ?>/login.php";

                }, 1800);

            }

        } catch (error) {

            mostrarMensaje(
                "No fue posible conectar con el servidor.",
                false
            );

        } finally {

            boton.disabled = false;
            boton.textContent = "Crear cuenta";

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