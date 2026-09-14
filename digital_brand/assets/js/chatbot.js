document.addEventListener("DOMContentLoaded", function () {

    const contenedor =
        document.getElementById("chatbot-contenedor");

    const botonAbrir =
        document.getElementById("chatbot-boton");

    const panel =
        document.getElementById("chatbot-panel");

    const botonCerrar =
        document.getElementById("chatbot-cerrar");

    const mensajes =
        document.getElementById("chatbot-mensajes");

    const formulario =
        document.getElementById("chatbot-formulario");

    const entrada =
        document.getElementById("chatbot-entrada");

    const botonEnviar =
        document.getElementById("chatbot-enviar");

    const tokenCsrf =
        document.getElementById("chatbot-csrf");

    const sugerencias =
        document.querySelectorAll(".chatbot-sugerencia");

    if (
        !contenedor ||
        !botonAbrir ||
        !panel ||
        !botonCerrar ||
        !mensajes ||
        !formulario ||
        !entrada ||
        !botonEnviar ||
        !tokenCsrf
    ) {

        console.error(
            "No fue posible inicializar el chatbot."
        );

        return;
    }

    let historial = [];
    let mensajeInicialMostrado = false;
    let enviando = false;

    function abrirChatbot() {

        panel.classList.add("abierto");

        panel.setAttribute(
            "aria-hidden",
            "false"
        );

        botonAbrir.setAttribute(
            "aria-expanded",
            "true"
        );

        if (!mensajeInicialMostrado) {

            mensajeInicialMostrado = true;

            agregarMensaje(
                "¡Hola! 👋 Soy el asistente virtual de Digital Brand. " +
                "Puedo ayudarte con información sobre planes, precios, " +
                "servicios, solicitudes y medios de contacto.",
                "bot"
            );
        }

        setTimeout(function () {
            entrada.focus();
        }, 100);
    }

    function cerrarChatbot() {

        panel.classList.remove("abierto");

        panel.setAttribute(
            "aria-hidden",
            "true"
        );

        botonAbrir.setAttribute(
            "aria-expanded",
            "false"
        );
    }

    function alternarChatbot() {

        if (panel.classList.contains("abierto")) {
            cerrarChatbot();
        } else {
            abrirChatbot();
        }
    }

    function agregarMensaje(texto, tipo) {

        const elemento =
            document.createElement("div");

        elemento.className =
            "chatbot-mensaje " + tipo;

        elemento.textContent =
            String(texto || "");

        mensajes.appendChild(elemento);

        desplazarAlFinal();

        return elemento;
    }

    function agregarIndicadorEscritura() {

        const indicador =
            document.createElement("div");

        indicador.className =
            "chatbot-mensaje bot escribiendo";

        indicador.setAttribute(
            "aria-label",
            "El asistente está escribiendo"
        );

        for (let indice = 0; indice < 3; indice++) {

            const punto =
                document.createElement("span");

            punto.className =
                "chatbot-punto";

            indicador.appendChild(punto);
        }

        mensajes.appendChild(indicador);

        desplazarAlFinal();

        return indicador;
    }

    function desplazarAlFinal() {

        mensajes.scrollTop =
            mensajes.scrollHeight;
    }

    function ajustarAlturaEntrada() {

        entrada.style.height = "auto";

        entrada.style.height =
            Math.min(
                entrada.scrollHeight,
                105
            ) + "px";
    }

    function bloquearFormulario() {

        enviando = true;

        entrada.disabled = true;
        botonEnviar.disabled = true;
    }

    function desbloquearFormulario() {

        enviando = false;

        entrada.disabled = false;
        botonEnviar.disabled = false;

        setTimeout(function () {
            entrada.focus();
        }, 50);
    }

    function limpiarHistorial() {

        historial = [];
        mensajes.innerHTML = "";
        mensajeInicialMostrado = false;

        abrirChatbot();
    }

    async function enviarMensaje(texto) {

        const mensajeUsuario =
            String(texto || "").trim();

        if (
            mensajeUsuario === "" ||
            enviando
        ) {
            return;
        }

        if (mensajeUsuario.length > 1000) {

            agregarMensaje(
                "El mensaje no puede superar los 1000 caracteres.",
                "bot error"
            );

            return;
        }

        agregarMensaje(
            mensajeUsuario,
            "usuario"
        );

        entrada.value = "";
        ajustarAlturaEntrada();

        bloquearFormulario();

        const indicador =
            agregarIndicadorEscritura();

        try {

            const respuesta = await fetch(
                obtenerRutaControlador(),
                {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/json",
                        "X-Requested-With":
                            "XMLHttpRequest"
                    },
                    body: JSON.stringify({
                        mensaje: mensajeUsuario,
                        historial: historial,
                        csrf_token: tokenCsrf.value
                    })
                }
            );

            const textoRespuesta =
                await respuesta.text();

            let resultado;

            try {

                resultado =
                    JSON.parse(textoRespuesta);

            } catch (errorJson) {

                console.error(
                    "Respuesta no válida del chatbot:",
                    textoRespuesta
                );

                throw new Error(
                    "El servidor devolvió una respuesta no válida."
                );
            }

            indicador.remove();

            if (
                !respuesta.ok ||
                resultado.ok !== true
            ) {

                agregarMensaje(
                    resultado.msg ||
                    "No fue posible obtener una respuesta.",
                    "bot error"
                );

                return;
            }

            const respuestaBot =
                String(
                    resultado.respuesta || ""
                ).trim();

            if (respuestaBot === "") {

                agregarMensaje(
                    "El asistente no generó una respuesta.",
                    "bot error"
                );

                return;
            }

            agregarMensaje(
                respuestaBot,
                "bot"
            );

            historial.push({
                role: "user",
                content: mensajeUsuario
            });

            historial.push({
                role: "assistant",
                content: respuestaBot
            });

            if (historial.length > 16) {

                historial =
                    historial.slice(-16);
            }

        } catch (error) {

            if (
                indicador &&
                indicador.isConnected
            ) {
                indicador.remove();
            }

            console.error(
                "Error comunicándose con el chatbot:",
                error
            );

            agregarMensaje(
                "No fue posible conectar con el asistente. " +
                "Verifica que Ollama esté ejecutándose e inténtalo nuevamente.",
                "bot error"
            );

        } finally {

            desbloquearFormulario();
        }
    }

    function obtenerRutaControlador() {

        const rutaActual =
            window.location.pathname;

        const posicionProyecto =
            rutaActual.indexOf("/digital_brand/");

        if (posicionProyecto !== -1) {

            return (
                window.location.origin +
                "/digital_brand/controllers/procesar_chatbot.php"
            );
        }

        return (
            window.location.origin +
            "/controllers/procesar_chatbot.php"
        );
    }

    botonAbrir.addEventListener(
        "click",
        alternarChatbot
    );

    botonCerrar.addEventListener(
        "click",
        cerrarChatbot
    );

    document.addEventListener(
        "keydown",
        function (evento) {

            if (
                evento.key === "Escape" &&
                panel.classList.contains("abierto")
            ) {
                cerrarChatbot();
            }
        }
    );

    entrada.addEventListener(
        "input",
        ajustarAlturaEntrada
    );

    entrada.addEventListener(
        "keydown",
        function (evento) {

            if (
                evento.key === "Enter" &&
                !evento.shiftKey
            ) {

                evento.preventDefault();

                formulario.requestSubmit();
            }
        }
    );

    formulario.addEventListener(
        "submit",
        function (evento) {

            evento.preventDefault();

            enviarMensaje(
                entrada.value
            );
        }
    );

    sugerencias.forEach(function (boton) {

        boton.addEventListener(
            "click",
            function () {

                const pregunta =
                    boton.dataset.pregunta || "";

                abrirChatbot();

                enviarMensaje(pregunta);
            }
        );
    });

    window.digitalBrandChatbot = {
        abrir: abrirChatbot,
        cerrar: cerrarChatbot,
        limpiar: limpiarHistorial
    };
});