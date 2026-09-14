<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirAdministrador();

$conexion = Database::conectar();

$mensajes = $conexion->query("
    SELECT
        id,
        id_usuario,
        nombre,
        correo,
        telefono,
        asunto,
        mensaje,
        estado,
        fecha_envio
    FROM contacto
    ORDER BY
        CASE estado
            WHEN 'nuevo' THEN 1
            WHEN 'leído' THEN 2
            WHEN 'respondido' THEN 3
            ELSE 4
        END,
        fecha_envio DESC
")->fetchAll();

$tituloPagina = 'Digital Brand - Mensajes';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Administración
                </span>

                <h1>Mensajes de contacto</h1>

                <p>
                    Consulta los mensajes enviados desde el formulario
                    de contacto y actualiza su estado.
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
                        id="buscar-mensaje"
                        class="buscar"
                        placeholder="Buscar por nombre, correo, asunto o mensaje..."
                    >

                    <select
                        id="filtro-estado"
                        class="filtro"
                    >

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="nuevo">
                            Nuevos
                        </option>

                        <option value="leído">
                            Leídos
                        </option>

                        <option value="respondido">
                            Respondidos
                        </option>

                    </select>

                </div>

                <div class="toolbar-right">

                    <span>
                        Total:
                        <strong id="total-mensajes">
                            <?php echo count($mensajes); ?>
                        </strong>
                    </span>

                </div>

            </div>

            <div
                id="mensaje-respuesta"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display:none;"
            ></div>

            <?php if (empty($mensajes)): ?>

                <div class="estado-vacio">

                    <div class="estado-vacio-icono">
                        ✉️
                    </div>

                    <h2>No existen mensajes registrados</h2>

                    <p>
                        Los mensajes enviados desde la página de contacto
                        aparecerán aquí.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Remitente</th>
                                <th>Asunto</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($mensajes as $item): ?>

                                <?php

                                $busqueda = mb_strtolower(
                                    $item['nombre'] . ' ' .
                                    $item['correo'] . ' ' .
                                    $item['asunto'] . ' ' .
                                    $item['mensaje'],
                                    'UTF-8'
                                );

                                $estadoNormalizado = mb_strtolower(
                                    trim($item['estado']),
                                    'UTF-8'
                                );

                                switch ($estadoNormalizado) {

                                    case 'nuevo':
                                        $claseBadge = 'badge-pendiente';
                                        break;

                                    case 'leído':
                                    case 'leido':
                                        $claseBadge = 'badge-revision';
                                        break;

                                    case 'respondido':
                                        $claseBadge = 'badge-atendido';
                                        break;

                                    default:
                                        $claseBadge = 'badge-pendiente';
                                        break;
                                }

                                ?>

                                <tr
                                    data-fila-mensaje
                                    data-busqueda="<?php
                                        echo escapar($busqueda);
                                    ?>"
                                    data-estado="<?php
                                        echo escapar($estadoNormalizado);
                                    ?>"
                                >

                                    <td data-label="Remitente">

                                        <strong>
                                            <?php echo escapar(
                                                $item['nombre']
                                            ); ?>
                                        </strong>

                                        <br>

                                        <small>
                                            <?php echo escapar(
                                                $item['correo']
                                            ); ?>
                                        </small>

                                        <?php if (!empty($item['telefono'])): ?>

                                            <br>

                                            <small>
                                                <?php echo escapar(
                                                    $item['telefono']
                                                ); ?>
                                            </small>

                                        <?php endif; ?>

                                    </td>

                                    <td data-label="Asunto">

                                        <?php echo escapar(
                                            $item['asunto']
                                        ); ?>

                                    </td>

                                    <td data-label="Mensaje">

                                        <?php echo escapar(
                                            mb_substr(
                                                $item['mensaje'],
                                                0,
                                                120
                                            )
                                        ); ?>

                                        <?php if (
                                            mb_strlen($item['mensaje']) > 120
                                        ): ?>
                                            ...
                                        <?php endif; ?>

                                    </td>

                                    <td data-label="Fecha">

                                        <?php echo date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $item['fecha_envio']
                                            )
                                        ); ?>

                                    </td>

                                    <td data-label="Estado">

                                        <span
                                            class="badge <?php
                                                echo escapar($claseBadge);
                                            ?>"
                                        >
                                            <?php echo escapar(
                                                ucfirst($estadoNormalizado)
                                            ); ?>
                                        </span>

                                    </td>

                                    <td data-label="Acciones">

                                        <div
                                            style="
                                                display:flex;
                                                gap:8px;
                                                flex-wrap:wrap;
                                            "
                                        >

                                            <button
                                                type="button"
                                                class="btn-icon btn-ver ver-mensaje"
                                                data-mensaje='<?php
                                                    echo escapar(
                                                        json_encode(
                                                            $item,
                                                            JSON_UNESCAPED_UNICODE
                                                        )
                                                    );
                                                ?>'
                                                title="Ver mensaje completo"
                                            >
                                                👁️
                                            </button>

                                            <?php if (
                                                $estadoNormalizado === 'nuevo'
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="btn-icon btn-editar cambiar-estado-mensaje"
                                                    data-id="<?php
                                                        echo (int) $item['id'];
                                                    ?>"
                                                    data-estado="leído"
                                                    title="Marcar como leído"
                                                >
                                                    📖
                                                </button>

                                            <?php endif; ?>

                                            <?php if (
                                                in_array(
                                                    $estadoNormalizado,
                                                    ['nuevo', 'leído', 'leido'],
                                                    true
                                                )
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="btn-icon btn-ver cambiar-estado-mensaje"
                                                    data-id="<?php
                                                        echo (int) $item['id'];
                                                    ?>"
                                                    data-estado="respondido"
                                                    title="Marcar como respondido"
                                                >
                                                    ✅
                                                </button>

                                            <?php endif; ?>

                                            <button
                                                type="button"
                                                class="btn-icon btn-eliminar eliminar-mensaje"
                                                data-id="<?php
                                                    echo (int) $item['id'];
                                                ?>"
                                                data-nombre="<?php
                                                    echo escapar(
                                                        $item['nombre']
                                                    );
                                                ?>"
                                                title="Eliminar mensaje"
                                            >
                                                🗑️
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

<div
    id="modal-mensaje"
    class="modal"
    aria-hidden="true"
>

    <div
        class="modal-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-mensaje"
    >

        <h2 id="titulo-modal-mensaje">
            Mensaje recibido
        </h2>

        <div class="perfil-grid">

            <div class="perfil-item">

                <label>Nombre</label>

                <strong id="detalle-nombre"></strong>

            </div>

            <div class="perfil-item">

                <label>Correo</label>

                <strong id="detalle-correo"></strong>

            </div>

            <div class="perfil-item">

                <label>Teléfono</label>

                <strong id="detalle-telefono"></strong>

            </div>

            <div class="perfil-item">

                <label>Fecha</label>

                <strong id="detalle-fecha"></strong>

            </div>

        </div>

        <div class="grupo-campo" style="margin-top:20px;">

            <label>Asunto</label>

            <input
                type="text"
                id="detalle-asunto"
                readonly
            >

        </div>

        <div class="grupo-campo">

            <label>Mensaje</label>

            <textarea
                id="detalle-mensaje"
                rows="8"
                readonly
            ></textarea>

        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">

            <a
                href="#"
                id="responder-correo"
                class="boton"
            >
                Responder por correo
            </a>

            <button
                type="button"
                id="cerrar-modal-mensaje"
                class="boton boton-secundario"
            >
                Cerrar
            </button>

        </div>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const tokenCsrf =
        "<?php echo escapar(obtenerTokenCsrf()); ?>";

    const buscador =
        document.getElementById("buscar-mensaje");

    const filtroEstado =
        document.getElementById("filtro-estado");

    const totalMensajes =
        document.getElementById("total-mensajes");

    const mensajeRespuesta =
        document.getElementById("mensaje-respuesta");

    const modal =
        document.getElementById("modal-mensaje");

    const cerrarModal =
        document.getElementById("cerrar-modal-mensaje");

    const detalleNombre =
        document.getElementById("detalle-nombre");

    const detalleCorreo =
        document.getElementById("detalle-correo");

    const detalleTelefono =
        document.getElementById("detalle-telefono");

    const detalleFecha =
        document.getElementById("detalle-fecha");

    const detalleAsunto =
        document.getElementById("detalle-asunto");

    const detalleMensaje =
        document.getElementById("detalle-mensaje");

    const responderCorreo =
        document.getElementById("responder-correo");

    function mostrarMensaje(texto, exitoso) {

        if (!mensajeRespuesta) {
            alert(texto);
            return;
        }

        mensajeRespuesta.style.display = "block";

        mensajeRespuesta.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensajeRespuesta.textContent = texto;

        mensajeRespuesta.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }

    function normalizarTexto(texto) {

        return String(texto || "")
            .toLocaleLowerCase("es")
            .trim();
    }

    function filtrarMensajes() {

        const texto = buscador
            ? normalizarTexto(buscador.value)
            : "";

        const estado = filtroEstado
            ? normalizarTexto(filtroEstado.value)
            : "";

        const filas = document.querySelectorAll(
            "[data-fila-mensaje]"
        );

        let visibles = 0;

        filas.forEach(function (fila) {

            const busquedaFila = normalizarTexto(
                fila.dataset.busqueda
            );

            const estadoFila = normalizarTexto(
                fila.dataset.estado
            );

            const coincideTexto =
                busquedaFila.includes(texto);

            const coincideEstado =
                estado === "" ||
                estadoFila === estado;

            const mostrar =
                coincideTexto &&
                coincideEstado;

            fila.style.display = mostrar ? "" : "none";

            if (mostrar) {
                visibles++;
            }
        });

        if (totalMensajes) {
            totalMensajes.textContent = visibles;
        }
    }

    if (buscador) {

        buscador.addEventListener(
            "input",
            filtrarMensajes
        );
    }

    if (filtroEstado) {

        filtroEstado.addEventListener(
            "change",
            filtrarMensajes
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCIONES MEDIANTE DELEGACIÓN DE EVENTOS
    |--------------------------------------------------------------------------
    */

    document.addEventListener("click", async function (evento) {

        const botonVer =
            evento.target.closest(".ver-mensaje");

        const botonCambiarEstado =
            evento.target.closest(
                ".cambiar-estado-mensaje"
            );

        const botonEliminar =
            evento.target.closest(
                ".eliminar-mensaje"
            );

        /*
        |--------------------------------------------------------------------------
        | Ver mensaje completo
        |--------------------------------------------------------------------------
        */

        if (botonVer) {

            try {

                const item = JSON.parse(
                    botonVer.dataset.mensaje
                );

                if (detalleNombre) {
                    detalleNombre.textContent =
                        item.nombre || "No registrado";
                }

                if (detalleCorreo) {
                    detalleCorreo.textContent =
                        item.correo || "No registrado";
                }

                if (detalleTelefono) {
                    detalleTelefono.textContent =
                        item.telefono || "No registrado";
                }

                if (detalleFecha) {
                    detalleFecha.textContent =
                        item.fecha_envio || "";
                }

                if (detalleAsunto) {
                    detalleAsunto.value =
                        item.asunto || "";
                }

                if (detalleMensaje) {
                    detalleMensaje.value =
                        item.mensaje || "";
                }

                if (responderCorreo) {

                    responderCorreo.href =
                        "mailto:" +
                        encodeURIComponent(
                            item.correo || ""
                        ) +
                        "?subject=" +
                        encodeURIComponent(
                            "Respuesta de Digital Brand: " +
                            (item.asunto || "")
                        );
                }

                if (modal) {

                    modal.classList.add("show");

                    modal.setAttribute(
                        "aria-hidden",
                        "false"
                    );
                }

            } catch (error) {

                console.error(
                    "Error leyendo el mensaje:",
                    error
                );

                mostrarMensaje(
                    "No fue posible abrir el mensaje.",
                    false
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Cambiar estado
        |--------------------------------------------------------------------------
        */

        if (botonCambiarEstado) {

            const idMensaje =
                botonCambiarEstado.dataset.id;

            const nuevoEstado =
                botonCambiarEstado.dataset.estado;

            const confirmar = window.confirm(
                '¿Deseas marcar este mensaje como "' +
                nuevoEstado +
                '"?'
            );

            if (!confirmar) {
                return;
            }

            botonCambiarEstado.disabled = true;

            const correcto = await enviarAccion(
                idMensaje,
                "cambiar_estado",
                nuevoEstado
            );

            if (!correcto) {
                botonCambiarEstado.disabled = false;
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Eliminar mensaje
        |--------------------------------------------------------------------------
        */

        if (botonEliminar) {

            const idMensaje =
                botonEliminar.dataset.id;

            const nombre =
                botonEliminar.dataset.nombre || "";

            const confirmar = window.confirm(
                "¿Deseas eliminar el mensaje de " +
                nombre +
                "?"
            );

            if (!confirmar) {
                return;
            }

            botonEliminar.disabled = true;

            const correcto = await enviarAccion(
                idMensaje,
                "eliminar",
                ""
            );

            if (!correcto) {
                botonEliminar.disabled = false;
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Cerrar modal
    |--------------------------------------------------------------------------
    */

    if (cerrarModal && modal) {

        cerrarModal.addEventListener(
            "click",
            function () {

                modal.classList.remove("show");

                modal.setAttribute(
                    "aria-hidden",
                    "true"
                );
            }
        );
    }

    if (modal) {

        modal.addEventListener(
            "click",
            function (evento) {

                if (evento.target === modal) {

                    modal.classList.remove("show");

                    modal.setAttribute(
                        "aria-hidden",
                        "true"
                    );
                }
            }
        );
    }

    document.addEventListener(
        "keydown",
        function (evento) {

            if (
                evento.key === "Escape" &&
                modal &&
                modal.classList.contains("show")
            ) {

                modal.classList.remove("show");

                modal.setAttribute(
                    "aria-hidden",
                    "true"
                );
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Enviar acción al servidor
    |--------------------------------------------------------------------------
    */

    async function enviarAccion(
        idMensaje,
        accion,
        estado
    ) {

        const datos = new FormData();

        datos.append(
            "csrf_token",
            tokenCsrf
        );

        datos.append(
            "id_mensaje",
            idMensaje
        );

        datos.append(
            "accion",
            accion
        );

        datos.append(
            "estado",
            estado
        );

        try {

            const respuesta = await fetch(
                "<?php echo APP_URL; ?>/controllers/gestionar_mensaje.php",
                {
                    method: "POST",
                    body: datos,
                    cache: "no-store",
                    headers: {
                        "X-Requested-With":
                            "XMLHttpRequest"
                    }
                }
            );

            const texto = await respuesta.text();

            console.log(
                "Respuesta gestionar mensaje:",
                texto
            );

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

                return false;
            }

            mostrarMensaje(
                resultado.msg || "Respuesta recibida.",
                resultado.ok === true
            );

            if (resultado.ok === true) {

                setTimeout(function () {

                    window.location.href =
                        "<?php echo APP_URL; ?>/admin/mensajes.php?actualizado=" +
                        Date.now();

                }, 1000);

                return true;
            }

            return false;

        } catch (error) {

            console.error(
                "Error gestionando mensaje:",
                error
            );

            mostrarMensaje(
                "No fue posible comunicarse con el servidor.",
                false
            );

            return false;
        }
    }
});
</script>
<?php

require_once __DIR__ . '/../includes/footer.php';

?>