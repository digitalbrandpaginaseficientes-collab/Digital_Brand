<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirCliente();

$conexion = Database::conectar();

$idUsuario = (int) $_SESSION['id_usuario'];

$consulta = $conexion->prepare("
    SELECT
        id_usuario,
        nombre,
        correo,
        telefono,
        empresa
    FROM usuarios
    WHERE id_usuario = ?
    LIMIT 1
");

$consulta->execute([
    $idUsuario
]);

$usuario = $consulta->fetch();

if (!$usuario) {

    $_SESSION['mensaje_error'] =
        'No fue posible cargar la información de tu cuenta.';

    redirigir(
        APP_URL . '/usuario/index.php'
    );
}

$tituloPagina = 'Mi perfil';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="perfil-grid">

            <aside class="perfil-resumen">

                <div class="perfil-avatar">

                    <?php echo escapar(
                        obtenerIniciales(
                            $usuario['nombre']
                        )
                    ); ?>

                </div>

                <h2>
                    <?php echo escapar(
                        $usuario['nombre']
                    ); ?>
                </h2>

                <p>
                    <?php echo escapar(
                        $usuario['correo']
                    ); ?>
                </p>

                <?php if (
                    !empty($usuario['empresa'])
                ): ?>

                    <span class="perfil-empresa">

                        <?php echo escapar(
                            $usuario['empresa']
                        ); ?>

                    </span>

                <?php endif; ?>

                <div class="perfil-resumen-enlaces">

                    <a
                        href="<?php echo APP_URL; ?>/usuario/index.php"
                        class="boton boton-secundario boton-ancho"
                    >
                        Volver al panel
                    </a>

                    <a
                        href="<?php echo APP_URL; ?>/usuario/servicios_contratados.php"
                        class="boton boton-ancho"
                    >
                        Mis servicios
                    </a>

                </div>

            </aside>

            <section class="perfil-formulario-contenedor">

                <div class="encabezado-seccion encabezado-perfil">

                    <span class="etiqueta-superior">
                        Mi perfil
                    </span>

                    <h1>
                        Actualizar información
                    </h1>

                    <p>
                        Modifica tus datos personales y, si lo necesitas,
                        cambia la contraseña de acceso.
                    </p>

                </div>

                <form
                    class="formulario"
                    id="form-perfil"
                    novalidate
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo escapar(
                            obtenerTokenCsrf()
                        ); ?>"
                    >

                    <div class="formulario-grid-dos">

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
                                value="<?php echo escapar(
                                    $usuario['nombre']
                                ); ?>"
                                required
                            >

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
                                value="<?php echo escapar(
                                    $usuario['correo']
                                ); ?>"
                                required
                            >

                            <small class="ayuda-campo">
                                Si lo cambias, deberás usar el nuevo correo
                                para iniciar sesión la próxima vez.
                            </small>

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
                                value="<?php echo escapar(
                                    $usuario['telefono'] ?? ''
                                ); ?>"
                            >

                        </div>

                        <div class="grupo-campo">

                            <label for="empresa">
                                Empresa o emprendimiento
                            </label>

                            <input
                                type="text"
                                id="empresa"
                                name="empresa"
                                maxlength="120"
                                autocomplete="organization"
                                placeholder="Opcional"
                                value="<?php echo escapar(
                                    $usuario['empresa'] ?? ''
                                ); ?>"
                            >

                        </div>

                    </div>

                    <div class="perfil-separador">

                        <div>

                            <span class="perfil-separador-icono">
                                🔐
                            </span>

                            <div>

                                <h2>
                                    Cambiar contraseña
                                </h2>

                                <p>
                                    Déjala en blanco si no deseas modificarla.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="formulario-grid-dos">

                        <div class="grupo-campo">

                            <label for="password">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                minlength="8"
                                maxlength="72"
                                autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres"
                            >

                        </div>

                        <div class="grupo-campo">

                            <label for="password2">
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                id="password2"
                                name="password2"
                                minlength="8"
                                maxlength="72"
                                autocomplete="new-password"
                                placeholder="Repite la contraseña"
                            >

                        </div>

                    </div>

                    <div
                        id="msg-perfil"
                        class="alerta"
                        role="alert"
                        aria-live="polite"
                        style="display: none;"
                    ></div>

                    <button
                        type="submit"
                        class="boton boton-ancho"
                        id="btn-perfil"
                    >
                        Guardar cambios
                    </button>

                </form>

            </section>

        </div>

    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const formulario =
        document.getElementById("form-perfil");

    const mensaje =
        document.getElementById("msg-perfil");

    const boton =
        document.getElementById("btn-perfil");

    const campoNombre =
        document.getElementById("nombre");

    const campoCorreo =
        document.getElementById("correo");

    const campoTelefono =
        document.getElementById("telefono");

    const campoPassword =
        document.getElementById("password");

    const campoPassword2 =
        document.getElementById("password2");

    if (
        !formulario ||
        !mensaje ||
        !boton ||
        !campoNombre ||
        !campoCorreo ||
        !campoTelefono ||
        !campoPassword ||
        !campoPassword2
    ) {

        console.error(
            "No fue posible inicializar el formulario del perfil."
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

            const nombre =
                campoNombre.value.trim();

            const correo =
                campoCorreo.value.trim();

            const telefono =
                campoTelefono.value.trim();

            const password =
                campoPassword.value;

            const password2 =
                campoPassword2.value;

            if (nombre.length < 3) {

                mostrarMensaje(
                    "El nombre debe tener mínimo 3 caracteres.",
                    false
                );

                campoNombre.focus();

                return;
            }

            if (
                !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)
            ) {

                mostrarMensaje(
                    "Escribe un correo electrónico válido.",
                    false
                );

                campoCorreo.focus();

                return;
            }

            if (
                telefono !== "" &&
                !/^[0-9+\s()-]{7,20}$/.test(telefono)
            ) {

                mostrarMensaje(
                    "Escribe un número de teléfono válido.",
                    false
                );

                campoTelefono.focus();

                return;
            }

            if (
                password !== "" ||
                password2 !== ""
            ) {

                if (password.length < 8) {

                    mostrarMensaje(
                        "La nueva contraseña debe tener mínimo 8 caracteres.",
                        false
                    );

                    campoPassword.focus();

                    return;
                }

                if (password !== password2) {

                    mostrarMensaje(
                        "Las contraseñas no coinciden.",
                        false
                    );

                    campoPassword2.focus();

                    return;
                }
            }

            boton.disabled = true;
            boton.textContent = "Guardando...";

            try {

                const respuesta = await fetch(
                    "<?php echo APP_URL; ?>/controllers/actualizar_perfil.php",
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

                    resultado =
                        JSON.parse(texto);

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

                    campoPassword.value = "";
                    campoPassword2.value = "";

                    setTimeout(function () {

                        window.location.href =
                            "<?php echo APP_URL; ?>/usuario/perfil.php?actualizado=" +
                            Date.now();

                    }, 1200);
                }

            } catch (error) {

                console.error(
                    "Error actualizando el perfil:",
                    error
                );

                mostrarMensaje(
                    "No fue posible comunicarse con el servidor.",
                    false
                );

            } finally {

                boton.disabled = false;
                boton.textContent = "Guardar cambios";
            }
        }
    );
});
</script>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>