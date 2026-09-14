<?php

require_once __DIR__ . '/csrf.php';

?>

<div
    id="chatbot-contenedor"
    class="chatbot-contenedor"
>

    <button
        type="button"
        id="chatbot-boton"
        class="chatbot-boton"
        aria-label="Abrir asistente virtual"
        aria-expanded="false"
        aria-controls="chatbot-panel"
    >
        <span class="chatbot-boton-icono">
            💬
        </span>

        <span class="chatbot-boton-texto">
            Asistente
        </span>
    </button>

    <section
        id="chatbot-panel"
        class="chatbot-panel"
        aria-hidden="true"
    >

        <div class="chatbot-encabezado">

            <div class="chatbot-identidad">

                <div class="chatbot-avatar">
                    <img src="<?php echo APP_URL; ?>/assets/img/logo.jpeg" 
                         alt="Logo de Digital Brand">
                </div>

                <div>

                    <strong>
                        Asistente Digital Brand
                    </strong>

                    <span>
                        Impulsado por Llama 3.2
                    </span>

                </div>

            </div>

            <button
                type="button"
                id="chatbot-cerrar"
                class="chatbot-cerrar"
                aria-label="Cerrar asistente"
            >
                ×
            </button>

        </div>

        <div
            id="chatbot-mensajes"
            class="chatbot-mensajes"
            role="log"
            aria-live="polite"
            aria-relevant="additions"
        ></div>

        <div
            id="chatbot-sugerencias"
            class="chatbot-sugerencias"
        >

            <button
                type="button"
                class="chatbot-sugerencia"
                data-pregunta="¿Qué planes ofrecen?"
            >
                Planes
            </button>

            <button
                type="button"
                class="chatbot-sugerencia"
                data-pregunta="¿Cuáles son los precios?"
            >
                Precios
            </button>

            <button
                type="button"
                class="chatbot-sugerencia"
                data-pregunta="¿Cómo puedo solicitar un servicio?"
            >
                Solicitar servicio
            </button>

            <button
                type="button"
                class="chatbot-sugerencia"
                data-pregunta="¿Cómo puedo contactar a Digital Brand?"
            >
                Contacto
            </button>

        </div>

        <form
            id="chatbot-formulario"
            class="chatbot-formulario"
            novalidate
        >

            <input
                type="hidden"
                id="chatbot-csrf"
                name="csrf_token"
                value="<?php
                    echo escapar(
                        obtenerTokenCsrf()
                    );
                ?>"
            >

            <label
                for="chatbot-entrada"
                class="sr-only"
            >
                Escribe tu mensaje
            </label>

            <textarea
                id="chatbot-entrada"
                class="chatbot-entrada"
                name="mensaje"
                rows="1"
                maxlength="1000"
                placeholder="Escribe tu pregunta..."
                autocomplete="off"
                required
            ></textarea>

            <button
                type="submit"
                id="chatbot-enviar"
                class="chatbot-enviar"
                aria-label="Enviar mensaje"
            >
                ➤
            </button>

        </form>

        <div class="chatbot-pie">

            <span>
                Las respuestas pueden tardar algunos segundos.
            </span>

        </div>

    </section>

</div>