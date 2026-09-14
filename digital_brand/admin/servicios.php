<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirAdministrador();

$conexion = Database::conectar();

$servicios = $conexion->query("
    SELECT
        id_servicio,
        nombre,
        descripcion,
        precio,
        caracteristicas,
        estado,
        fecha_creacion,
        fecha_actualizacion
    FROM servicios
    ORDER BY precio ASC
")->fetchAll();

$tituloPagina = 'Digital Brand - Servicios';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Administración
                </span>

                <h1>Gestión de servicios</h1>

                <p>
                    Crea, edita, activa o desactiva los planes ofrecidos
                    por Digital Brand.
                </p>

            </div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">

                <a
                    href="<?php echo APP_URL; ?>/admin/index.php"
                    class="boton boton-secundario"
                >
                    Volver al panel
                </a>

                <button
                    type="button"
                    class="boton"
                    id="abrir-modal-servicio"
                >
                    Crear servicio
                </button>

            </div>

        </div>

        <section class="panel">

            <div class="toolbar">

                <div class="toolbar-left">

                    <input
                        type="search"
                        id="buscar-servicio"
                        class="buscar"
                        placeholder="Buscar servicio..."
                    >

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
                        <strong id="total-servicios">
                            <?php echo count($servicios); ?>
                        </strong>
                    </span>

                </div>

            </div>

            <div
                id="mensaje-servicios"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display:none;"
            ></div>

            <?php if (empty($servicios)): ?>

                <div class="estado-vacio">

                    <div class="estado-vacio-icono">
                        🌐
                    </div>

                    <h2>No existen servicios registrados</h2>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Servicio</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Actualización</th>
                                <th>Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($servicios as $servicio): ?>

                                <tr
                                    data-fila-servicio
                                    data-busqueda="<?php
                                        echo escapar(
                                            strtolower(
                                                $servicio['nombre'] . ' ' .
                                                $servicio['descripcion']
                                            )
                                        );
                                    ?>"
                                    data-estado="<?php
                                        echo escapar($servicio['estado']);
                                    ?>"
                                >

                                    <td data-label="Servicio">

                                        <strong>
                                            <?php echo escapar(
                                                $servicio['nombre']
                                            ); ?>
                                        </strong>

                                    </td>

                                    <td data-label="Descripción">

                                        <?php echo escapar(
                                            mb_substr(
                                                $servicio['descripcion'],
                                                0,
                                                90
                                            )
                                        ); ?>

                                        <?php if (
                                            mb_strlen(
                                                $servicio['descripcion']
                                            ) > 90
                                        ): ?>
                                            ...
                                        <?php endif; ?>

                                    </td>

                                    <td data-label="Precio">

                                        <?php echo formatearPrecio(
                                            $servicio['precio']
                                        ); ?>

                                    </td>

                                    <td data-label="Estado">

                                        <span class="badge badge-<?php
                                            echo escapar(
                                                $servicio['estado']
                                            );
                                        ?>">
                                            <?php echo escapar(
                                                $servicio['estado']
                                            ); ?>
                                        </span>

                                    </td>

                                    <td data-label="Actualización">

                                        <?php echo date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $servicio['fecha_actualizacion']
                                            )
                                        ); ?>

                                    </td>

                                    <td data-label="Acciones">

                                        <div style="display:flex; gap:8px;">

                                            <button
                                                type="button"
                                                class="btn-icon btn-editar editar-servicio"
                                                data-servicio='<?php
                                                    echo escapar(
                                                        json_encode(
                                                            $servicio,
                                                            JSON_UNESCAPED_UNICODE
                                                        )
                                                    );
                                                ?>'
                                                title="Editar servicio"
                                            >
                                                ✏️
                                            </button>

                                            <button
                                                type="button"
                                                class="btn-icon <?php
                                                    echo $servicio['estado'] === 'activo'
                                                        ? 'btn-eliminar'
                                                        : 'btn-ver';
                                                ?> cambiar-estado-servicio"
                                                data-id="<?php
                                                    echo (int) $servicio['id_servicio'];
                                                ?>"
                                                data-estado="<?php
                                                    echo escapar(
                                                        $servicio['estado'] === 'activo'
                                                            ? 'inactivo'
                                                            : 'activo'
                                                    );
                                                ?>"
                                                title="<?php
                                                    echo $servicio['estado'] === 'activo'
                                                        ? 'Desactivar'
                                                        : 'Activar';
                                                ?>"
                                            >
                                                <?php echo
                                                    $servicio['estado'] === 'activo'
                                                        ? '⛔'
                                                        : '✅';
                                                ?>
                                            </button>

                                        </div>

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

<div id="modal-servicio" class="modal">

    <div class="modal-card">

        <h2 id="titulo-modal-servicio">
            Crear servicio
        </h2>

        <form id="form-servicio">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo escapar(obtenerTokenCsrf()); ?>"
            >

            <input
                type="hidden"
                name="id_servicio"
                id="servicio-id"
                value=""
            >

            <input
                type="hidden"
                name="accion"
                id="servicio-accion"
                value="crear"
            >

            <div class="grupo-campo">

                <label for="servicio-nombre">
                    Nombre
                </label>

                <input
                    type="text"
                    id="servicio-nombre"
                    name="nombre"
                    maxlength="100"
                    required
                >

            </div>

            <div class="grupo-campo">

                <label for="servicio-descripcion">
                    Descripción
                </label>

                <textarea
                    id="servicio-descripcion"
                    name="descripcion"
                    rows="5"
                    maxlength="2000"
                    required
                ></textarea>

            </div>

            <div class="grupo-campo">

                <label for="servicio-precio">
                    Precio
                </label>

                <input
                    type="number"
                    id="servicio-precio"
                    name="precio"
                    min="0"
                    step="1000"
                    required
                >

            </div>

            <div class="grupo-campo">

                <label for="servicio-caracteristicas">
                    Características
                </label>

                <textarea
                    id="servicio-caracteristicas"
                    name="caracteristicas"
                    rows="5"
                    placeholder="Separar cada característica con |"
                    required
                ></textarea>

                <small class="ayuda-campo">
                    Ejemplo: Diseño adaptable|Formulario de contacto|Chatbot
                </small>

            </div>

            <div class="grupo-campo">

                <label for="servicio-estado">
                    Estado
                </label>

                <select
                    id="servicio-estado"
                    name="estado"
                    required
                >
                    <option value="activo">
                        Activo
                    </option>

                    <option value="inactivo">
                        Inactivo
                    </option>
                </select>

            </div>

            <div
                id="mensaje-form-servicio"
                class="alerta"
                style="display:none;"
            ></div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">

                <button
                    type="submit"
                    class="boton"
                    id="guardar-servicio"
                >
                    Guardar servicio
                </button>

                <button
                    type="button"
                    class="boton boton-secundario"
                    id="cerrar-modal-servicio"
                >
                    Cancelar
                </button>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("modal-servicio");
    const abrirModal = document.getElementById("abrir-modal-servicio");
    const cerrarModal = document.getElementById("cerrar-modal-servicio");
    const formulario = document.getElementById("form-servicio");
    const tituloModal = document.getElementById("titulo-modal-servicio");
    const mensajeFormulario = document.getElementById(
        "mensaje-form-servicio"
    );
    const mensajeGeneral = document.getElementById(
        "mensaje-servicios"
    );

    const campoId = document.getElementById("servicio-id");
    const campoAccion = document.getElementById("servicio-accion");
    const campoNombre = document.getElementById("servicio-nombre");
    const campoDescripcion = document.getElementById(
        "servicio-descripcion"
    );
    const campoPrecio = document.getElementById("servicio-precio");
    const campoCaracteristicas = document.getElementById(
        "servicio-caracteristicas"
    );
    const campoEstado = document.getElementById("servicio-estado");
    const botonGuardar = document.getElementById("guardar-servicio");

    const buscador = document.getElementById("buscar-servicio");
    const filtroEstado = document.getElementById("filtro-estado");
    const totalServicios = document.getElementById("total-servicios");
    const filas = document.querySelectorAll("[data-fila-servicio]");

    function mostrarMensaje(elemento, texto, exitoso) {

        elemento.style.display = "block";

        elemento.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        elemento.textContent = texto;
    }

    function limpiarFormulario() {

        formulario.reset();
        campoId.value = "";
        campoAccion.value = "crear";

        tituloModal.textContent = "Crear servicio";

        mensajeFormulario.style.display = "none";
        mensajeFormulario.textContent = "";
    }

    function abrirFormulario() {

        modal.classList.add("show");
    }

    function cerrarFormulario() {

        modal.classList.remove("show");
        limpiarFormulario();
    }

    abrirModal.addEventListener("click", function () {

        limpiarFormulario();
        abrirFormulario();
    });

    cerrarModal.addEventListener("click", cerrarFormulario);

    modal.addEventListener("click", function (evento) {

        if (evento.target === modal) {
            cerrarFormulario();
        }
    });

    document.querySelectorAll(".editar-servicio").forEach(function (boton) {

        boton.addEventListener("click", function () {

            const servicio = JSON.parse(
                boton.dataset.servicio
            );

            campoId.value = servicio.id_servicio;
            campoAccion.value = "editar";
            campoNombre.value = servicio.nombre;
            campoDescripcion.value = servicio.descripcion;
            campoPrecio.value = servicio.precio;
            campoCaracteristicas.value =
                servicio.caracteristicas;
            campoEstado.value = servicio.estado;

            tituloModal.textContent = "Editar servicio";

            abrirFormulario();
        });
    });

    document.querySelectorAll(
        ".cambiar-estado-servicio"
    ).forEach(function (boton) {

        boton.addEventListener("click", async function () {

            const confirmar = confirm(
                "¿Deseas cambiar el estado del servicio?"
            );

            if (!confirmar) {
                return;
            }

            const datos = new FormData();

            datos.append(
                "csrf_token",
                "<?php echo escapar(obtenerTokenCsrf()); ?>"
            );

            datos.append("accion", "cambiar_estado");
            datos.append("id_servicio", boton.dataset.id);
            datos.append("estado", boton.dataset.estado);

            await enviarServicio(datos, mensajeGeneral);
        });
    });

    formulario.addEventListener("submit", async function (evento) {

        evento.preventDefault();

        botonGuardar.disabled = true;
        botonGuardar.textContent = "Guardando...";

        await enviarServicio(
            new FormData(formulario),
            mensajeFormulario
        );

        botonGuardar.disabled = false;
        botonGuardar.textContent = "Guardar servicio";
    });

    async function enviarServicio(datos, destinoMensaje) {

        try {

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/gestionar_servicio.php",
                {
                    method: "POST",
                    body: datos
                }
            );

            const resultado = await respuesta.json();

            mostrarMensaje(
                destinoMensaje,
                resultado.msg,
                resultado.ok === true
            );

            if (resultado.ok === true) {

                setTimeout(function () {

                    window.location.reload();

                }, 1000);
            }

        } catch (error) {

            console.error(error);

            mostrarMensaje(
                destinoMensaje,
                "No fue posible comunicarse con el servidor.",
                false
            );
        }
    }

    function filtrarServicios() {

        const texto = buscador.value.trim().toLowerCase();
        const estado = filtroEstado.value;

        let visibles = 0;

        filas.forEach(function (fila) {

            const coincideTexto =
                fila.dataset.busqueda.includes(texto);

            const coincideEstado =
                estado === "" ||
                fila.dataset.estado === estado;

            const mostrar =
                coincideTexto &&
                coincideEstado;

            fila.style.display = mostrar ? "" : "none";

            if (mostrar) {
                visibles++;
            }
        });

        totalServicios.textContent = visibles;
    }

    buscador.addEventListener("input", filtrarServicios);
    filtroEstado.addEventListener("change", filtrarServicios);
});
</script>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>