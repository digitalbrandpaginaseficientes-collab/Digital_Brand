<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirAdministrador();

$conexion = Database::conectar();

$consulta = $conexion->query("
    SELECT
        us.id,
        us.id_usuario,
        us.id_servicio,
        us.estado,
        us.progreso,
        us.fecha_compra,
        us.fecha_inicio,
        us.fecha_entrega_estimada,
        us.fecha_finalizacion,
        us.fecha_vencimiento,
        us.observaciones,
        u.nombre AS cliente,
        u.correo,
        u.telefono,
        u.empresa,
        s.nombre AS servicio,
        s.descripcion,
        s.precio
    FROM usuario_servicios us
    INNER JOIN usuarios u
        ON u.id_usuario = us.id_usuario
    INNER JOIN servicios s
        ON s.id_servicio = us.id_servicio
    ORDER BY
        CASE us.estado
            WHEN 'pendiente' THEN 1
            WHEN 'en desarrollo' THEN 2
            WHEN 'en revisión' THEN 3
            WHEN 'activo' THEN 4
            WHEN 'finalizado' THEN 5
            WHEN 'cancelado' THEN 6
            ELSE 7
        END,
        us.fecha_compra DESC,
        us.id DESC
");

$serviciosAsignados = $consulta->fetchAll();

$totalServicios = count($serviciosAsignados);
$totalPendientes = 0;
$totalDesarrollo = 0;
$totalRevision = 0;
$totalFinalizados = 0;

foreach ($serviciosAsignados as $item) {

    $estado = mb_strtolower(
        trim($item['estado']),
        'UTF-8'
    );

    if ($estado === 'pendiente') {
        $totalPendientes++;
    }

    if (
        $estado === 'en desarrollo' ||
        $estado === 'activo'
    ) {
        $totalDesarrollo++;
    }

    if (
        $estado === 'en revisión' ||
        $estado === 'en revision'
    ) {
        $totalRevision++;
    }

    if ($estado === 'finalizado') {
        $totalFinalizados++;
    }
}

$tituloPagina = 'Digital Brand - Servicios asignados';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Administración
                </span>

                <h1>Servicios asignados</h1>

                <p>
                    Actualiza el estado, avance, fechas y observaciones
                    de los proyectos contratados por los clientes.
                </p>

            </div>

            <a
                href="<?php echo APP_URL; ?>/admin/index.php"
                class="boton boton-secundario"
            >
                Volver al panel
            </a>

        </div>

        <div class="resumen-grid resumen-servicios-admin">

            <article class="resumen-card">

                <div class="resumen-icono">
                    🌐
                </div>

                <div>

                    <strong>
                        <?php echo $totalServicios; ?>
                    </strong>

                    <span>
                        Servicios asignados
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    ⏳
                </div>

                <div>

                    <strong>
                        <?php echo $totalPendientes; ?>
                    </strong>

                    <span>
                        Pendientes
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    🛠️
                </div>

                <div>

                    <strong>
                        <?php echo $totalDesarrollo; ?>
                    </strong>

                    <span>
                        En desarrollo
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    🔎
                </div>

                <div>

                    <strong>
                        <?php echo $totalRevision; ?>
                    </strong>

                    <span>
                        En revisión
                    </span>

                </div>

            </article>

            <article class="resumen-card">

                <div class="resumen-icono">
                    ✅
                </div>

                <div>

                    <strong>
                        <?php echo $totalFinalizados; ?>
                    </strong>

                    <span>
                        Finalizados
                    </span>

                </div>

            </article>

        </div>

        <section class="panel">

            <div class="toolbar">

                <div class="toolbar-left">

                    <input
                        type="search"
                        id="buscar-servicio-cliente"
                        class="buscar"
                        placeholder="Buscar cliente, correo, empresa o servicio..."
                    >

                    <select
                        id="filtro-estado-servicio"
                        class="filtro"
                    >

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="pendiente">
                            Pendiente
                        </option>

                        <option value="en desarrollo">
                            En desarrollo
                        </option>

                        <option value="en revisión">
                            En revisión
                        </option>

                        <option value="activo">
                            Activo
                        </option>

                        <option value="finalizado">
                            Finalizado
                        </option>

                        <option value="cancelado">
                            Cancelado
                        </option>

                    </select>

                </div>

                <div class="toolbar-right">

                    <span>
                        Total visible:
                        <strong id="total-servicios-visible">
                            <?php echo $totalServicios; ?>
                        </strong>
                    </span>

                </div>

            </div>

            <div
                id="mensaje-servicios-clientes"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display: none;"
            ></div>

            <?php if (empty($serviciosAsignados)): ?>

                <div class="estado-vacio">

                    <div class="estado-vacio-icono">
                        🧾
                    </div>

                    <h2>No existen servicios asignados</h2>

                    <p>
                        Cuando se atienda una solicitud aprobada,
                        el servicio aparecerá en esta sección.
                    </p>

                    <a
                        href="<?php echo APP_URL; ?>/admin/solicitudes.php"
                        class="boton"
                    >
                        Ver solicitudes
                    </a>

                </div>

            <?php else: ?>

                <div class="servicios-admin-grid">

                    <?php foreach (
                        $serviciosAsignados as $servicio
                    ): ?>

                        <?php

                        $estadoNormalizado = mb_strtolower(
                            trim($servicio['estado']),
                            'UTF-8'
                        );

                        $progreso = (int) $servicio['progreso'];

                        if ($progreso < 0) {
                            $progreso = 0;
                        }

                        if ($progreso > 100) {
                            $progreso = 100;
                        }

                        switch ($estadoNormalizado) {

                            case 'pendiente':
                                $claseBadge = 'badge-pendiente';
                                $nombreEstado = 'Pendiente';
                                break;

                            case 'en desarrollo':
                                $claseBadge = 'badge-desarrollo';
                                $nombreEstado = 'En desarrollo';
                                break;

                            case 'en revisión':
                            case 'en revision':
                                $claseBadge = 'badge-revision';
                                $nombreEstado = 'En revisión';
                                break;

                            case 'activo':
                                $claseBadge = 'badge-activo';
                                $nombreEstado = 'Activo';
                                break;

                            case 'finalizado':
                                $claseBadge = 'badge-aprobada';
                                $nombreEstado = 'Finalizado';
                                break;

                            case 'cancelado':
                                $claseBadge = 'badge-cancelado';
                                $nombreEstado = 'Cancelado';
                                break;

                            default:
                                $claseBadge = 'badge-pendiente';
                                $nombreEstado = ucfirst(
                                    $estadoNormalizado
                                );
                                break;
                        }

                        $datosBusqueda = mb_strtolower(
                            $servicio['cliente'] . ' ' .
                            $servicio['correo'] . ' ' .
                            ($servicio['empresa'] ?? '') . ' ' .
                            $servicio['servicio'],
                            'UTF-8'
                        );

                        ?>

                        <article
                            class="servicio-admin-card"
                            data-fila-servicio-cliente
                            data-busqueda="<?php
                                echo escapar($datosBusqueda);
                            ?>"
                            data-estado="<?php
                                echo escapar($estadoNormalizado);
                            ?>"
                        >

                            <div class="servicio-admin-cabecera">

                                <div>

                                    <span
                                        class="badge <?php
                                            echo escapar($claseBadge);
                                        ?>"
                                    >
                                        <?php echo escapar($nombreEstado); ?>
                                    </span>

                                    <h2>
                                        <?php echo escapar(
                                            $servicio['servicio']
                                        ); ?>
                                    </h2>

                                    <p>
                                        <?php echo escapar(
                                            $servicio['descripcion']
                                        ); ?>
                                    </p>

                                </div>

                                <button
                                    type="button"
                                    class="boton editar-servicio-cliente"
                                    data-servicio="<?php
                                        echo escapar(
                                            json_encode(
                                                $servicio,
                                                JSON_UNESCAPED_UNICODE |
                                                JSON_UNESCAPED_SLASHES
                                            )
                                        );
                                    ?>"
                                >
                                    Actualizar seguimiento
                                </button>

                            </div>

                            <div class="servicio-admin-cliente">

                                <div class="servicio-admin-avatar">

                                    <?php echo escapar(
                                        obtenerIniciales(
                                            $servicio['cliente']
                                        )
                                    ); ?>

                                </div>

                                <div>

                                    <strong>
                                        <?php echo escapar(
                                            $servicio['cliente']
                                        ); ?>
                                    </strong>

                                    <span>
                                        <?php echo escapar(
                                            $servicio['correo']
                                        ); ?>
                                    </span>

                                    <?php if (
                                        !empty($servicio['empresa'])
                                    ): ?>

                                        <span>
                                            <?php echo escapar(
                                                $servicio['empresa']
                                            ); ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="servicio-admin-progreso">

                                <div>

                                    <span>Progreso actual</span>

                                    <strong>
                                        <?php echo $progreso; ?> %
                                    </strong>

                                </div>

                                <div class="servicio-progreso-barra">

                                    <div
                                        class="servicio-progreso-valor"
                                        style="width: <?php
                                            echo $progreso;
                                        ?>%;"
                                    ></div>

                                </div>

                            </div>

                            <div class="servicio-admin-datos">

                                <div>

                                    <small>Fecha de inicio</small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio['fecha_inicio']
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_inicio'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            Sin definir

                                        <?php endif; ?>

                                    </strong>

                                </div>

                                <div>

                                    <small>Entrega estimada</small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio[
                                                    'fecha_entrega_estimada'
                                                ]
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_entrega_estimada'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            Sin definir

                                        <?php endif; ?>

                                    </strong>

                                </div>

                                <div>

                                    <small>Finalización</small>

                                    <strong>

                                        <?php if (
                                            !empty(
                                                $servicio[
                                                    'fecha_finalizacion'
                                                ]
                                            )
                                        ): ?>

                                            <?php echo date(
                                                'd/m/Y',
                                                strtotime(
                                                    $servicio[
                                                        'fecha_finalizacion'
                                                    ]
                                                )
                                            ); ?>

                                        <?php else: ?>

                                            No finalizado

                                        <?php endif; ?>

                                    </strong>

                                </div>

                                <div>

                                    <small>Valor</small>

                                    <strong>
                                        <?php echo formatearPrecio(
                                            $servicio['precio']
                                        ); ?>
                                    </strong>

                                </div>

                            </div>

                            <div class="servicio-admin-observaciones">

                                <strong>
                                    Observaciones para el cliente
                                </strong>

                                <p>
                                    <?php echo !empty(
                                        trim(
                                            $servicio[
                                                'observaciones'
                                            ] ?? ''
                                        )
                                    )
                                        ? nl2br(
                                            escapar(
                                                $servicio[
                                                    'observaciones'
                                                ]
                                            )
                                        )
                                        : 'No se han agregado observaciones.';
                                    ?>
                                </p>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>

</section>

<div
    id="modal-servicio-cliente"
    class="modal"
    aria-hidden="true"
>

    <div
        class="modal-card modal-seguimiento-card"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-modal-seguimiento"
    >

        <h2 id="titulo-modal-seguimiento">
            Actualizar servicio
        </h2>

        <p id="descripcion-modal-seguimiento">
            Modifica la información que podrá consultar el cliente.
        </p>

        <form
            id="form-servicio-cliente"
            novalidate
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo escapar(obtenerTokenCsrf()); ?>"
            >

            <input
                type="hidden"
                name="id"
                id="servicio-cliente-id"
                value=""
            >

            <div class="formulario-grid-dos">

                <div class="grupo-campo">

                    <label for="servicio-cliente-estado">
                        Estado
                    </label>

                    <select
                        id="servicio-cliente-estado"
                        name="estado"
                        required
                    >

                        <option value="pendiente">
                            Pendiente
                        </option>

                        <option value="en desarrollo">
                            En desarrollo
                        </option>

                        <option value="en revisión">
                            En revisión
                        </option>

                        <option value="activo">
                            Activo
                        </option>

                        <option value="finalizado">
                            Finalizado
                        </option>

                        <option value="cancelado">
                            Cancelado
                        </option>

                    </select>

                </div>

                <div class="grupo-campo">

                    <label for="servicio-cliente-progreso">
                        Progreso
                    </label>

                    <div class="campo-progreso-admin">

                        <input
                            type="range"
                            id="servicio-cliente-progreso"
                            name="progreso"
                            min="0"
                            max="100"
                            step="5"
                            value="0"
                        >

                        <strong id="valor-progreso-admin">
                            0 %
                        </strong>

                    </div>

                </div>

                <div class="grupo-campo">

                    <label for="servicio-cliente-inicio">
                        Fecha de inicio
                    </label>

                    <input
                        type="date"
                        id="servicio-cliente-inicio"
                        name="fecha_inicio"
                    >

                </div>

                <div class="grupo-campo">

                    <label for="servicio-cliente-entrega">
                        Fecha estimada de entrega
                    </label>

                    <input
                        type="date"
                        id="servicio-cliente-entrega"
                        name="fecha_entrega_estimada"
                    >

                </div>

                <div class="grupo-campo">

                    <label for="servicio-cliente-finalizacion">
                        Fecha de finalización
                    </label>

                    <input
                        type="date"
                        id="servicio-cliente-finalizacion"
                        name="fecha_finalizacion"
                    >

                </div>

                <div class="grupo-campo">

                    <label for="servicio-cliente-vencimiento">
                        Fecha de vencimiento
                    </label>

                    <input
                        type="date"
                        id="servicio-cliente-vencimiento"
                        name="fecha_vencimiento"
                    >

                </div>

            </div>

            <div class="grupo-campo">

                <label for="servicio-cliente-observaciones">
                    Observaciones para el cliente
                </label>

                <textarea
                    id="servicio-cliente-observaciones"
                    name="observaciones"
                    rows="7"
                    maxlength="3000"
                    placeholder="Describe los avances, tareas realizadas, novedades o información importante..."
                ></textarea>

                <small class="ayuda-campo">
                    Estas observaciones serán visibles para el cliente.
                </small>

            </div>

            <div
                id="mensaje-form-servicio-cliente"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display: none;"
            ></div>

            <div class="acciones-modal-seguimiento">

                <button
                    type="submit"
                    class="boton"
                    id="guardar-servicio-cliente"
                >
                    Guardar cambios
                </button>

                <button
                    type="button"
                    class="boton boton-secundario"
                    id="cerrar-modal-servicio-cliente"
                >
                    Cancelar
                </button>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const modal =
        document.getElementById("modal-servicio-cliente");

    const formulario =
        document.getElementById("form-servicio-cliente");

    const cerrarModal =
        document.getElementById(
            "cerrar-modal-servicio-cliente"
        );

    const campoId =
        document.getElementById("servicio-cliente-id");

    const campoEstado =
        document.getElementById("servicio-cliente-estado");

    const campoProgreso =
        document.getElementById("servicio-cliente-progreso");

    const valorProgreso =
        document.getElementById("valor-progreso-admin");

    const campoInicio =
        document.getElementById("servicio-cliente-inicio");

    const campoEntrega =
        document.getElementById("servicio-cliente-entrega");

    const campoFinalizacion =
        document.getElementById(
            "servicio-cliente-finalizacion"
        );

    const campoVencimiento =
        document.getElementById(
            "servicio-cliente-vencimiento"
        );

    const campoObservaciones =
        document.getElementById(
            "servicio-cliente-observaciones"
        );

    const mensajeFormulario =
        document.getElementById(
            "mensaje-form-servicio-cliente"
        );

    const mensajeGeneral =
        document.getElementById(
            "mensaje-servicios-clientes"
        );

    const botonGuardar =
        document.getElementById(
            "guardar-servicio-cliente"
        );

    const buscador =
        document.getElementById(
            "buscar-servicio-cliente"
        );

    const filtroEstado =
        document.getElementById(
            "filtro-estado-servicio"
        );

    const totalVisible =
        document.getElementById(
            "total-servicios-visible"
        );

    const filas =
        document.querySelectorAll(
            "[data-fila-servicio-cliente]"
        );

    function mostrarMensaje(
        elemento,
        texto,
        exitoso
    ) {

        elemento.style.display = "block";

        elemento.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        elemento.textContent = texto;
    }

    function limpiarMensajeFormulario() {

        mensajeFormulario.style.display = "none";
        mensajeFormulario.className = "alerta";
        mensajeFormulario.textContent = "";
    }

    function cerrarFormulario() {

        modal.classList.remove("show");

        modal.setAttribute(
            "aria-hidden",
            "true"
        );

        document.body.style.overflow = "";

        formulario.reset();
        limpiarMensajeFormulario();
    }

    campoProgreso.addEventListener(
        "input",
        function () {

            valorProgreso.textContent =
                campoProgreso.value + " %";
        }
    );

    campoEstado.addEventListener(
        "change",
        function () {

            if (campoEstado.value === "finalizado") {

                campoProgreso.value = 100;
                valorProgreso.textContent = "100 %";

                if (campoFinalizacion.value === "") {

                    const hoy =
                        new Date()
                            .toISOString()
                            .split("T")[0];

                    campoFinalizacion.value = hoy;
                }
            }

            if (
                campoEstado.value === "cancelado" &&
                campoFinalizacion.value === ""
            ) {

                const hoy =
                    new Date()
                        .toISOString()
                        .split("T")[0];

                campoFinalizacion.value = hoy;
            }
        }
    );

    document
        .querySelectorAll(".editar-servicio-cliente")
        .forEach(function (boton) {

            boton.addEventListener(
                "click",
                function () {

                    let servicio;

                    try {

                        servicio = JSON.parse(
                            boton.dataset.servicio
                        );

                    } catch (error) {

                        console.error(
                            "No fue posible leer el servicio:",
                            error
                        );

                        return;
                    }

                    campoId.value =
                        servicio.id || "";

                    campoEstado.value =
                        servicio.estado || "pendiente";

                    campoProgreso.value =
                        servicio.progreso || 0;

                    valorProgreso.textContent =
                        (servicio.progreso || 0) + " %";

                    campoInicio.value =
                        servicio.fecha_inicio || "";

                    campoEntrega.value =
                        servicio.fecha_entrega_estimada || "";

                    campoFinalizacion.value =
                        servicio.fecha_finalizacion || "";

                    campoVencimiento.value =
                        servicio.fecha_vencimiento || "";

                    campoObservaciones.value =
                        servicio.observaciones || "";

                    limpiarMensajeFormulario();

                    modal.classList.add("show");

                    modal.setAttribute(
                        "aria-hidden",
                        "false"
                    );

                    document.body.style.overflow = "hidden";
                }
            );
        });

    cerrarModal.addEventListener(
        "click",
        cerrarFormulario
    );

    modal.addEventListener(
        "click",
        function (evento) {

            if (evento.target === modal) {
                cerrarFormulario();
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
                cerrarFormulario();
            }
        }
    );

    formulario.addEventListener(
        "submit",
        async function (evento) {

            evento.preventDefault();

            limpiarMensajeFormulario();

            botonGuardar.disabled = true;
            botonGuardar.textContent = "Guardando...";

            try {

                const respuesta = await fetch(
                    "<?php echo APP_URL; ?>/controllers/gestionar_servicio_cliente.php",
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
                        mensajeFormulario,
                        "El servidor devolvió una respuesta no válida.",
                        false
                    );

                    return;
                }

                mostrarMensaje(
                    mensajeFormulario,
                    resultado.msg || "Respuesta recibida.",
                    resultado.ok === true
                );

                if (resultado.ok === true) {

                    setTimeout(function () {

                        window.location.href =
                            "<?php echo APP_URL; ?>/admin/servicios_clientes.php?actualizado=" +
                            Date.now();

                    }, 1000);
                }

            } catch (error) {

                console.error(
                    "Error actualizando el servicio:",
                    error
                );

                mostrarMensaje(
                    mensajeFormulario,
                    "No fue posible comunicarse con el servidor.",
                    false
                );

            } finally {

                botonGuardar.disabled = false;
                botonGuardar.textContent = "Guardar cambios";
            }
        }
    );

    function normalizarTexto(texto) {

        return String(texto || "")
            .toLocaleLowerCase("es")
            .trim();
    }

    function filtrarServicios() {

        const texto = buscador
            ? normalizarTexto(buscador.value)
            : "";

        const estado = filtroEstado
            ? normalizarTexto(filtroEstado.value)
            : "";

        let visibles = 0;

        filas.forEach(function (fila) {

            const busquedaFila =
                normalizarTexto(
                    fila.dataset.busqueda
                );

            const estadoFila =
                normalizarTexto(
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

            fila.style.display =
                mostrar ? "" : "none";

            if (mostrar) {
                visibles++;
            }
        });

        if (totalVisible) {
            totalVisible.textContent = visibles;
        }
    }

    if (buscador) {
        buscador.addEventListener(
            "input",
            filtrarServicios
        );
    }

    if (filtroEstado) {
        filtroEstado.addEventListener(
            "change",
            filtrarServicios
        );
    }
});
</script>

<?php

require_once __DIR__ . '/../includes/footer.php';

?>