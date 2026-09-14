<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirAdministrador();

$conexion = Database::conectar();

$solicitudes = $conexion->query("
    SELECT
        sp.id,
        sp.id_usuario,
        sp.id_servicio,
        sp.mensaje,
        sp.estado,
        sp.fecha_solicitud,
        sp.fecha_atencion,
        u.nombre AS usuario,
        u.correo,
        u.telefono,
        u.empresa,
        s.nombre AS servicio,
        s.descripcion,
        s.precio
    FROM solicitudes_planes sp
    INNER JOIN usuarios u
        ON u.id_usuario = sp.id_usuario
    INNER JOIN servicios s
        ON s.id_servicio = sp.id_servicio
    ORDER BY
        CASE sp.estado
            WHEN 'pendiente' THEN 1
            WHEN 'en revisión' THEN 2
            WHEN 'aprobada' THEN 3
            WHEN 'atendida' THEN 4
            WHEN 'rechazada' THEN 5
            ELSE 6
        END,
        sp.fecha_solicitud DESC
")->fetchAll();

$tituloPagina = 'Digital Brand - Solicitudes';

require_once __DIR__ . '/../includes/header.php';

?>

<section class="seccion">

    <div class="contenedor">

        <div class="panel-titulo">

            <div>

                <span class="etiqueta-superior">
                    Administración
                </span>

                <h1>Gestión de solicitudes</h1>

                <p>
                    Revisa las solicitudes de los clientes, actualiza su
                    estado y asigna servicios aprobados.
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
                        id="buscar-solicitud"
                        class="buscar"
                        placeholder="Buscar cliente, correo, empresa o servicio..."
                    >

                    <select
                        id="filtro-estado"
                        class="filtro"
                    >

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="pendiente">
                            Pendiente
                        </option>

                        <option value="en revisión">
                            En revisión
                        </option>

                        <option value="aprobada">
                            Aprobada
                        </option>

                        <option value="atendida">
                            Atendida
                        </option>

                        <option value="rechazada">
                            Rechazada
                        </option>

                    </select>

                </div>

                <div class="toolbar-right">

                    <span>
                        Total:
                        <strong id="total-solicitudes">
                            <?php echo count($solicitudes); ?>
                        </strong>
                    </span>

                </div>

            </div>

            <div
                id="mensaje-solicitudes"
                class="alerta"
                role="alert"
                aria-live="polite"
                style="display: none;"
            ></div>

            <?php if (empty($solicitudes)): ?>

                <div class="estado-vacio">

                    <div class="estado-vacio-icono">
                        📋
                    </div>

                    <h2>No existen solicitudes registradas</h2>

                    <p>
                        Cuando los clientes soliciten un plan, aparecerá aquí.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($solicitudes as $solicitud): ?>

                                <?php

                                $busqueda = mb_strtolower(
                                    $solicitud['usuario'] . ' ' .
                                    $solicitud['correo'] . ' ' .
                                    ($solicitud['empresa'] ?? '') . ' ' .
                                    $solicitud['servicio'],
                                    'UTF-8'
                                );

                                $estadoNormalizado = mb_strtolower(
                                    trim($solicitud['estado']),
                                    'UTF-8'
                                );

                                switch ($estadoNormalizado) {

                                    case 'pendiente':
                                        $claseBadge = 'badge-pendiente';
                                        break;

                                    case 'en revisión':
                                    case 'en revision':
                                        $claseBadge = 'badge-revision';
                                        break;

                                    case 'aprobada':
                                        $claseBadge = 'badge-aprobada';
                                        break;

                                    case 'atendida':
                                        $claseBadge = 'badge-atendido';
                                        break;

                                    case 'rechazada':
                                        $claseBadge = 'badge-cancelado';
                                        break;

                                    default:
                                        $claseBadge = 'badge-pendiente';
                                        break;
                                }

                                ?>

                                <tr
                                    data-fila-solicitud
                                    data-busqueda="<?php
                                        echo escapar($busqueda);
                                    ?>"
                                    data-estado="<?php
                                        echo escapar($estadoNormalizado);
                                    ?>"
                                >

                                    <td data-label="Cliente">

                                        <strong>
                                            <?php echo escapar(
                                                $solicitud['usuario']
                                            ); ?>
                                        </strong>

                                        <br>

                                        <small>
                                            <?php echo escapar(
                                                $solicitud['correo']
                                            ); ?>
                                        </small>

                                        <?php if (!empty($solicitud['telefono'])): ?>

                                            <br>

                                            <small>
                                                Tel:
                                                <?php echo escapar(
                                                    $solicitud['telefono']
                                                ); ?>
                                            </small>

                                        <?php endif; ?>

                                        <?php if (!empty($solicitud['empresa'])): ?>

                                            <br>

                                            <small>
                                                <?php echo escapar(
                                                    $solicitud['empresa']
                                                ); ?>
                                            </small>

                                        <?php endif; ?>

                                    </td>

                                    <td data-label="Servicio">

                                        <strong>
                                            <?php echo escapar(
                                                $solicitud['servicio']
                                            ); ?>
                                        </strong>

                                        <br>

                                        <small>
                                            <?php echo formatearPrecio(
                                                $solicitud['precio']
                                            ); ?>
                                        </small>

                                    </td>

                                    <td data-label="Mensaje">

                                        <?php echo escapar(
                                            !empty($solicitud['mensaje'])
                                                ? $solicitud['mensaje']
                                                : 'Sin mensaje'
                                        ); ?>

                                    </td>

                                    <td data-label="Fecha">

                                        <?php echo date(
                                            'd/m/Y H:i',
                                            strtotime(
                                                $solicitud['fecha_solicitud']
                                            )
                                        ); ?>

                                        <?php if (
                                            !empty($solicitud['fecha_atencion'])
                                        ): ?>

                                            <br>

                                            <small>
                                                Atendida:
                                                <?php echo date(
                                                    'd/m/Y H:i',
                                                    strtotime(
                                                        $solicitud[
                                                            'fecha_atencion'
                                                        ]
                                                    )
                                                ); ?>
                                            </small>

                                        <?php endif; ?>

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
                                                display: flex;
                                                gap: 8px;
                                                flex-wrap: wrap;
                                                align-items: center;
                                            "
                                        >

                                            <?php if (
                                                $estadoNormalizado ===
                                                'pendiente'
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="btn-icon btn-editar cambiar-estado"
                                                    data-id="<?php
                                                        echo (int) $solicitud['id'];
                                                    ?>"
                                                    data-estado="en revisión"
                                                    title="Pasar a revisión"
                                                    aria-label="Pasar a revisión"
                                                >
                                                    🔎
                                                </button>

                                            <?php endif; ?>

                                            <?php if (
                                                in_array(
                                                    $estadoNormalizado,
                                                    [
                                                        'pendiente',
                                                        'en revisión',
                                                        'en revision'
                                                    ],
                                                    true
                                                )
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="btn-icon btn-ver cambiar-estado"
                                                    data-id="<?php
                                                        echo (int) $solicitud['id'];
                                                    ?>"
                                                    data-estado="aprobada"
                                                    title="Aprobar solicitud"
                                                    aria-label="Aprobar solicitud"
                                                >
                                                    ✅
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn-icon btn-eliminar cambiar-estado"
                                                    data-id="<?php
                                                        echo (int) $solicitud['id'];
                                                    ?>"
                                                    data-estado="rechazada"
                                                    title="Rechazar solicitud"
                                                    aria-label="Rechazar solicitud"
                                                >
                                                    ❌
                                                </button>

                                            <?php endif; ?>

                                            <?php if (
                                                $estadoNormalizado ===
                                                'aprobada'
                                            ): ?>

                                                <button
                                                    type="button"
                                                    class="boton boton-pequeno atender-solicitud"
                                                    data-id="<?php
                                                        echo (int) $solicitud['id'];
                                                    ?>"
                                                >
                                                    Asignar servicio
                                                </button>

                                            <?php endif; ?>

                                            <?php if (
                                                $estadoNormalizado ===
                                                'atendida'
                                            ): ?>

                                                <span class="badge badge-atendido">
                                                    Atendida
                                                </span>

                                            <?php endif; ?>

                                            <?php if (
                                                $estadoNormalizado ===
                                                'rechazada'
                                            ): ?>

                                                <span class="badge badge-cancelado">
                                                    Rechazada
                                                </span>

                                            <?php endif; ?>

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

<script>
document.addEventListener("DOMContentLoaded", function () {

    const tokenCsrf =
        "<?php echo escapar(obtenerTokenCsrf()); ?>";

    const buscador =
        document.getElementById("buscar-solicitud");

    const filtroEstado =
        document.getElementById("filtro-estado");

    const totalSolicitudes =
        document.getElementById("total-solicitudes");

    const mensaje =
        document.getElementById("mensaje-solicitudes");

    const filas =
        document.querySelectorAll("[data-fila-solicitud]");

    function mostrarMensaje(texto, exitoso) {

        if (!mensaje) {
            return;
        }

        mensaje.style.display = "block";

        mensaje.className = exitoso
            ? "alerta alerta-exito"
            : "alerta alerta-error";

        mensaje.textContent = texto;
    }

    function normalizarTexto(texto) {

        return texto
            .toLocaleLowerCase("es")
            .trim();
    }

    function filtrarSolicitudes() {

        const texto = buscador
            ? normalizarTexto(buscador.value)
            : "";

        const estado = filtroEstado
            ? normalizarTexto(filtroEstado.value)
            : "";

        let visibles = 0;

        filas.forEach(function (fila) {

            const busquedaFila = normalizarTexto(
                fila.dataset.busqueda || ""
            );

            const estadoFila = normalizarTexto(
                fila.dataset.estado || ""
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

        if (totalSolicitudes) {
            totalSolicitudes.textContent = visibles;
        }
    }

    if (buscador) {
        buscador.addEventListener(
            "input",
            filtrarSolicitudes
        );
    }

    if (filtroEstado) {
        filtroEstado.addEventListener(
            "change",
            filtrarSolicitudes
        );
    }

    document
        .querySelectorAll(".cambiar-estado")
        .forEach(function (boton) {

            boton.addEventListener(
                "click",
                async function () {

                    const nuevoEstado =
                        boton.dataset.estado;

                    const confirmar = window.confirm(
                        '¿Deseas cambiar la solicitud al estado "' +
                        nuevoEstado +
                        '"?'
                    );

                    if (!confirmar) {
                        return;
                    }

                    boton.disabled = true;

                    const resultado = await enviarAccion(
                        boton.dataset.id,
                        "cambiar_estado",
                        nuevoEstado
                    );

                    if (!resultado) {
                        boton.disabled = false;
                    }
                }
            );
        });

    document
        .querySelectorAll(".atender-solicitud")
        .forEach(function (boton) {

            boton.addEventListener(
                "click",
                async function () {

                    const confirmar = window.confirm(
                        "¿Deseas asignar este servicio al cliente?"
                    );

                    if (!confirmar) {
                        return;
                    }

                    boton.disabled = true;
                    boton.textContent = "Asignando...";

                    const resultado = await enviarAccion(
                        boton.dataset.id,
                        "atender",
                        ""
                    );

                    if (!resultado) {
                        boton.disabled = false;
                        boton.textContent = "Asignar servicio";
                    }
                }
            );
        });

    async function enviarAccion(
        idSolicitud,
        accion,
        estado
    ) {

        const datos = new FormData();

        datos.append(
            "csrf_token",
            tokenCsrf
        );

        datos.append(
            "id_solicitud",
            idSolicitud
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
                "<?php echo APP_URL; ?>/controllers/gestionar_solicitud.php",
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

                return false;
            }

            mostrarMensaje(
                resultado.msg || "Respuesta recibida.",
                resultado.ok === true
            );

            if (resultado.ok === true) {

                setTimeout(function () {

                    window.location.href =
                        "<?php echo APP_URL; ?>/admin/solicitudes.php?actualizado=" +
                        Date.now();

                }, 1000);

                return true;
            }

            return false;

        } catch (error) {

            console.error(
                "Error gestionando la solicitud:",
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