<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirAdministrador();

$conexion = Database::conectar();

$usuarios = $conexion->query("
    SELECT
        id_usuario,
        nombre,
        correo,
        telefono,
        empresa,
        rol,
        estado,
        fecha_registro
    FROM usuarios
    ORDER BY fecha_registro DESC
")->fetchAll();

$tituloPagina = 'Digital Brand - Usuarios';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Administración
                </span>

                <h1>Gestión de usuarios</h1>

                <p>
                    Consulta las cuentas registradas y administra sus roles
                    y estados.
                </p>

            </div>

            <a
                href="<?php echo APP_URL; ?>/admin/index.php"
                class="boton boton-secundario"
            >
                Volver al panel
            </a>

        </div>

        <section class="panel">

            <div class="toolbar">

                <div class="toolbar-left">

                    <input
                        type="search"
                        id="buscar-usuario"
                        class="buscar"
                        placeholder="Buscar por nombre, correo o empresa..."
                    >

                    <select id="filtro-rol" class="filtro">

                        <option value="">
                            Todos los roles
                        </option>

                        <option value="cliente">
                            Clientes
                        </option>

                        <option value="administrador">
                            Administradores
                        </option>

                    </select>

                    <select id="filtro-estado" class="filtro">

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="activo">
                            Activos
                        </option>

                        <option value="inactivo">
                            Inactivos
                        </option>

                    </select>

                </div>

                <div class="toolbar-right">

                    <span>
                        Total:
                        <strong id="total-visible">
                            <?php echo count($usuarios); ?>
                        </strong>
                    </span>

                </div>

            </div>

            <div
                id="mensaje-usuarios"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display: none;"
            ></div>

            <?php if (empty($usuarios)): ?>

                <div class="estado-vacio">

                    <div class="estado-vacio-icono">
                        👥
                    </div>

                    <h2>No existen usuarios registrados</h2>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table" id="tabla-usuarios">

                        <thead>

                            <tr>
                                <th>Usuario</th>
                                <th>Contacto</th>
                                <th>Empresa</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($usuarios as $usuario): ?>

                                <tr
                                    data-fila-usuario
                                    data-id="<?php
                                        echo (int) $usuario['id_usuario'];
                                    ?>"
                                    data-nombre="<?php
                                        echo escapar($usuario['nombre']);
                                    ?>"
                                    data-correo="<?php
                                        echo escapar($usuario['correo']);
                                    ?>"
                                    data-telefono="<?php
                                        echo escapar($usuario['telefono'] ?? '');
                                    ?>"
                                    data-empresa="<?php
                                        echo escapar($usuario['empresa'] ?? '');
                                    ?>"
                                    data-busqueda="<?php
                                        echo escapar(
                                            strtolower(
                                                $usuario['nombre'] . ' ' .
                                                $usuario['correo'] . ' ' .
                                                ($usuario['empresa'] ?? '')
                                            )
                                        );
                                    ?>"
                                    data-rol="<?php
                                        echo escapar($usuario['rol']);
                                    ?>"
                                    data-estado="<?php
                                        echo escapar($usuario['estado']);
                                    ?>"
                                >

                                    <td data-label="Usuario">

                                        <strong>
                                            <?php echo escapar(
                                                $usuario['nombre']
                                            ); ?>
                                        </strong>

                                        <br>

                                        <small>
                                            ID:
                                            <?php echo (int) $usuario['id_usuario']; ?>
                                        </small>

                                    </td>

                                    <td data-label="Contacto">

                                        <?php echo escapar(
                                            $usuario['correo']
                                        ); ?>

                                        <br>

                                        <small>
                                            <?php echo $usuario['telefono']
                                                ? escapar($usuario['telefono'])
                                                : 'Sin teléfono'; ?>
                                        </small>

                                    </td>

                                    <td data-label="Empresa">

                                        <?php echo $usuario['empresa']
                                            ? escapar($usuario['empresa'])
                                            : 'No registrada'; ?>

                                    </td>

                                    <td data-label="Rol">

                                        <select
                                            class="filtro selector-rol"
                                            data-id="<?php
                                                echo (int) $usuario['id_usuario'];
                                            ?>"
                                            <?php echo
                                                (int) $usuario['id_usuario'] ===
                                                (int) $_SESSION['id_usuario']
                                                    ? 'disabled'
                                                    : '';
                                            ?>
                                        >

                                            <option
                                                value="cliente"
                                                <?php echo
                                                    $usuario['rol'] === 'cliente'
                                                        ? 'selected'
                                                        : '';
                                                ?>
                                            >
                                                Cliente
                                            </option>

                                            <option
                                                value="administrador"
                                                <?php echo
                                                    $usuario['rol'] ===
                                                    'administrador'
                                                        ? 'selected'
                                                        : '';
                                                ?>
                                            >
                                                Administrador
                                            </option>

                                        </select>

                                    </td>

                                    <td data-label="Estado">

                                        <select
                                            class="filtro selector-estado"
                                            data-id="<?php
                                                echo (int) $usuario['id_usuario'];
                                            ?>"
                                            <?php echo
                                                (int) $usuario['id_usuario'] ===
                                                (int) $_SESSION['id_usuario']
                                                    ? 'disabled'
                                                    : '';
                                            ?>
                                        >

                                            <option
                                                value="activo"
                                                <?php echo
                                                    $usuario['estado'] === 'activo'
                                                        ? 'selected'
                                                        : '';
                                                ?>
                                            >
                                                Activo
                                            </option>

                                            <option
                                                value="inactivo"
                                                <?php echo
                                                    $usuario['estado'] === 'inactivo'
                                                        ? 'selected'
                                                        : '';
                                                ?>
                                            >
                                                Inactivo
                                            </option>

                                        </select>

                                    </td>

                                    <td data-label="Registro">

                                        <?php echo date(
                                            'd/m/Y',
                                            strtotime(
                                                $usuario['fecha_registro']
                                            )
                                        ); ?>

                                    </td>

                                    <td data-label="Acciones">

                                        <button
                                            type="button"
                                            class="btn-icon editar-usuario"
                                            data-id="<?php
                                                echo (int) $usuario['id_usuario'];
                                            ?>"
                                            title="Editar información"
                                        >
                                            ✏️
                                        </button>

                                        <?php if (
                                            (int) $usuario['id_usuario'] ===
                                            (int) $_SESSION['id_usuario']
                                        ): ?>

                                            <span class="badge badge-activo">
                                                Tu cuenta
                                            </span>

                                        <?php else: ?>

                                            <button
                                                type="button"
                                                class="btn-icon btn-eliminar eliminar-usuario"
                                                data-id="<?php
                                                    echo (int) $usuario['id_usuario'];
                                                ?>"
                                                data-nombre="<?php
                                                    echo escapar($usuario['nombre']);
                                                ?>"
                                                title="Eliminar usuario"
                                            >
                                                🗑️
                                            </button>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    </div>

</section>

<div id="modal-usuario" class="modal">

    <div class="modal-card">

        <h2>
            Editar información del usuario
        </h2>

        <form id="form-usuario">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo escapar(obtenerTokenCsrf()); ?>"
            >

            <input
                type="hidden"
                name="id_usuario"
                id="usuario-id"
                value=""
            >

            <input
                type="hidden"
                name="accion"
                value="editar_datos"
            >

            <div class="grupo-campo">

                <label for="usuario-nombre">
                    Nombre
                </label>

                <input
                    type="text"
                    id="usuario-nombre"
                    name="nombre"
                    maxlength="100"
                    required
                >

            </div>

            <div class="grupo-campo">

                <label for="usuario-correo">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    id="usuario-correo"
                    name="correo"
                    maxlength="150"
                    required
                >

            </div>

            <div class="grupo-campo">

                <label for="usuario-telefono">
                    Teléfono
                </label>

                <input
                    type="text"
                    id="usuario-telefono"
                    name="telefono"
                    maxlength="20"
                >

            </div>

            <div class="grupo-campo">

                <label for="usuario-empresa">
                    Empresa
                </label>

                <input
                    type="text"
                    id="usuario-empresa"
                    name="empresa"
                    maxlength="120"
                >

            </div>

            <div class="grupo-campo">

                <label for="usuario-password">
                    Nueva contraseña
                </label>

                <input
                    type="password"
                    id="usuario-password"
                    name="password"
                    maxlength="72"
                    autocomplete="new-password"
                    placeholder="Dejar en blanco para no cambiarla"
                >

                <small class="ayuda-campo">
                    Mínimo 8 caracteres. Déjalo vacío si no deseas cambiarla.
                </small>

            </div>

            <div class="grupo-campo">

                <label for="usuario-password2">
                    Confirmar nueva contraseña
                </label>

                <input
                    type="password"
                    id="usuario-password2"
                    name="password2"
                    maxlength="72"
                    autocomplete="new-password"
                >

            </div>

            <div
                id="mensaje-form-usuario"
                class="alerta"
                style="display:none;"
            ></div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">

                <button
                    type="submit"
                    class="boton"
                    id="guardar-usuario"
                >
                    Guardar cambios
                </button>

                <button
                    type="button"
                    class="boton boton-secundario"
                    id="cerrar-modal-usuario"
                >
                    Cancelar
                </button>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const tokenCsrf = "<?php echo escapar(obtenerTokenCsrf()); ?>";
    const buscador = document.getElementById("buscar-usuario");
    const filtroRol = document.getElementById("filtro-rol");
    const filtroEstado = document.getElementById("filtro-estado");
    const totalVisible = document.getElementById("total-visible");
    const mensaje = document.getElementById("mensaje-usuarios");
    const filas = document.querySelectorAll("[data-fila-usuario]");

    function mostrarMensaje(texto, exitoso) {

        mensaje.style.display = "block";

        mensaje.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensaje.textContent = texto;
    }

    function filtrarUsuarios() {

        const texto = buscador.value.trim().toLowerCase();
        const rol = filtroRol.value;
        const estado = filtroEstado.value;

        let visibles = 0;

        filas.forEach(function (fila) {

            const coincideTexto =
                fila.dataset.busqueda.includes(texto);

            const coincideRol =
                rol === "" || fila.dataset.rol === rol;

            const coincideEstado =
                estado === "" || fila.dataset.estado === estado;

            const mostrar =
                coincideTexto &&
                coincideRol &&
                coincideEstado;

            fila.style.display = mostrar ? "" : "none";

            if (mostrar) {
                visibles++;
            }
        });

        totalVisible.textContent = visibles;
    }

    buscador.addEventListener("input", filtrarUsuarios);
    filtroRol.addEventListener("change", filtrarUsuarios);
    filtroEstado.addEventListener("change", filtrarUsuarios);

    document.querySelectorAll(".selector-rol").forEach(function (selector) {

    selector.addEventListener("change", async function () {

        const confirmar = confirm(
            "¿Deseas cambiar el rol de este usuario a " +
            selector.value +
            "?"
        );

        if (!confirmar) {
            window.location.reload();
            return;
        }

        selector.disabled = true;

        await actualizarUsuario(
            selector.dataset.id,
            "cambiar_rol",
            selector.value
        );
    });
});

    document.querySelectorAll(".selector-estado").forEach(function (selector) {

    selector.addEventListener("change", async function () {

        const confirmar = confirm(
            "¿Deseas cambiar el estado de este usuario a " +
            selector.value +
            "?"
        );

        if (!confirmar) {
            window.location.reload();
            return;
        }

        selector.disabled = true;

        await actualizarUsuario(
            selector.dataset.id,
            "cambiar_estado",
            selector.value
        );
    });
});

    document.querySelectorAll(".eliminar-usuario").forEach(function (boton) {

        boton.addEventListener("click", async function () {

            const confirmar = confirm(
                "¿Seguro que deseas eliminar al usuario " +
                boton.dataset.nombre +
                "? Esta acción también eliminará sus solicitudes."
            );

            if (!confirmar) {
                return;
            }

            await actualizarUsuario(
                boton.dataset.id,
                "eliminar",
                ""
            );
        });
    });

    const modalUsuario = document.getElementById("modal-usuario");
    const formUsuario = document.getElementById("form-usuario");
    const cerrarModalUsuario = document.getElementById("cerrar-modal-usuario");
    const mensajeFormUsuario = document.getElementById("mensaje-form-usuario");
    const campoUsuarioId = document.getElementById("usuario-id");
    const campoUsuarioNombre = document.getElementById("usuario-nombre");
    const campoUsuarioCorreo = document.getElementById("usuario-correo");
    const campoUsuarioTelefono = document.getElementById("usuario-telefono");
    const campoUsuarioEmpresa = document.getElementById("usuario-empresa");
    const campoUsuarioPassword = document.getElementById("usuario-password");
    const campoUsuarioPassword2 = document.getElementById("usuario-password2");

    function mostrarMensajeForm(texto, exitoso) {

        mensajeFormUsuario.style.display = "block";

        mensajeFormUsuario.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensajeFormUsuario.textContent = texto;
    }

    function cerrarFormularioUsuario() {

        modalUsuario.classList.remove("show");
        mensajeFormUsuario.style.display = "none";
        formUsuario.reset();
    }

    document.querySelectorAll(".editar-usuario").forEach(function (boton) {

        boton.addEventListener("click", function () {

            const fila = boton.closest("[data-fila-usuario]");

            campoUsuarioId.value = fila.dataset.id;
            campoUsuarioNombre.value = fila.dataset.nombre;
            campoUsuarioCorreo.value = fila.dataset.correo;
            campoUsuarioTelefono.value = fila.dataset.telefono;
            campoUsuarioEmpresa.value = fila.dataset.empresa;
            campoUsuarioPassword.value = "";
            campoUsuarioPassword2.value = "";

            mensajeFormUsuario.style.display = "none";

            modalUsuario.classList.add("show");
        });
    });

    cerrarModalUsuario.addEventListener("click", cerrarFormularioUsuario);

    modalUsuario.addEventListener("click", function (evento) {

        if (evento.target === modalUsuario) {
            cerrarFormularioUsuario();
        }
    });

    formUsuario.addEventListener("submit", async function (evento) {

        evento.preventDefault();

        const nombre = campoUsuarioNombre.value.trim();
        const correo = campoUsuarioCorreo.value.trim();
        const telefono = campoUsuarioTelefono.value.trim();

        if (nombre.length < 3) {

            mostrarMensajeForm(
                "El nombre debe tener mínimo 3 caracteres.",
                false
            );

            campoUsuarioNombre.focus();

            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {

            mostrarMensajeForm(
                "Escribe un correo electrónico válido.",
                false
            );

            campoUsuarioCorreo.focus();

            return;
        }

        if (
            telefono !== "" &&
            !/^[0-9+\s()-]{7,20}$/.test(telefono)
        ) {

            mostrarMensajeForm(
                "El teléfono ingresado no es válido.",
                false
            );

            campoUsuarioTelefono.focus();

            return;
        }

        const nuevaPassword = campoUsuarioPassword.value;
        const nuevaPassword2 = campoUsuarioPassword2.value;

        if (nuevaPassword !== "" || nuevaPassword2 !== "") {

            if (nuevaPassword.length < 8) {

                mostrarMensajeForm(
                    "La nueva contraseña debe tener mínimo 8 caracteres.",
                    false
                );

                campoUsuarioPassword.focus();

                return;
            }

            if (nuevaPassword !== nuevaPassword2) {

                mostrarMensajeForm(
                    "Las contraseñas no coinciden.",
                    false
                );

                campoUsuarioPassword2.focus();

                return;
            }
        }

        const botonGuardar = document.getElementById("guardar-usuario");

        botonGuardar.disabled = true;

        try {

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/actualizar_usuario.php",
                {
                    method: "POST",
                    body: new FormData(formUsuario),
                    cache: "no-store"
                }
            );

            const texto = await respuesta.text();

            let resultado;

            try {

                resultado = JSON.parse(texto);

            } catch (errorJson) {

                mostrarMensajeForm(
                    "El servidor devolvió una respuesta incorrecta.",
                    false
                );

                console.error(texto);

                botonGuardar.disabled = false;

                return;
            }

            mostrarMensajeForm(
                resultado.msg,
                resultado.ok === true
            );

            if (resultado.ok === true) {

                setTimeout(function () {

                    window.location.href =
                        "<?php echo APP_URL; ?>/admin/usuarios.php?actualizado=" +
                        Date.now();

                }, 1200);

                return;
            }

            botonGuardar.disabled = false;

        } catch (error) {

            console.error(error);

            mostrarMensajeForm(
                "No fue posible comunicarse con el servidor.",
                false
            );

            botonGuardar.disabled = false;
        }
    });

 async function actualizarUsuario(idUsuario, accion, valor) {

    const datos = new FormData();

    datos.append("csrf_token", tokenCsrf);
    datos.append("id_usuario", idUsuario);
    datos.append("accion", accion);
    datos.append("valor", valor);

    try {

        const respuesta = await fetch(
            "<?php echo APP_URL; ?>/controllers/actualizar_usuario.php",
            {
                method: "POST",
                body: datos,
                cache: "no-store"
            }
        );

        const texto = await respuesta.text();

        console.log("Respuesta actualizar usuario:", texto);

        let resultado;

        try {

            resultado = JSON.parse(texto);

        } catch (errorJson) {

            mostrarMensaje(
                "El servidor devolvió una respuesta incorrecta.",
                false
            );

            console.error(texto);

            return;
        }

        mostrarMensaje(
            resultado.msg,
            resultado.ok === true
        );

        console.log("Detalles del cambio:", resultado);

        if (resultado.ok === true) {

            setTimeout(function () {

                window.location.href =
                    "<?php echo APP_URL; ?>/admin/usuarios.php?actualizado=" +
                    Date.now();

            }, 1500);

            return;
        }

        setTimeout(function () {

            window.location.reload();

        }, 2500);

    } catch (error) {

        console.error(error);

        mostrarMensaje(
            "No fue posible comunicarse con el servidor.",
            false
        );

        setTimeout(function () {
            window.location.reload();
        }, 2500);
    }
}

});
</script>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>