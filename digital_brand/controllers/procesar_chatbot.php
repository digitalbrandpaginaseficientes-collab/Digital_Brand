<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJson(false, 'Método no permitido.');
}

/*
|--------------------------------------------------------------------------
| Leer solicitud JSON
|--------------------------------------------------------------------------
*/

$contenidoRecibido = file_get_contents('php://input');

$datos = json_decode(
    $contenidoRecibido,
    true
);

if (!is_array($datos)) {
    respuestaJson(
        false,
        'La solicitud enviada no es válida.'
    );
}

/*
|--------------------------------------------------------------------------
| Validar token CSRF
|--------------------------------------------------------------------------
*/

$tokenCsrf = $datos['csrf_token'] ?? '';

if (!validarTokenCsrf($tokenCsrf)) {
    respuestaJson(
        false,
        'La sesión expiró. Recarga la página e inténtalo nuevamente.'
    );
}

/*
|--------------------------------------------------------------------------
| Validar mensaje
|--------------------------------------------------------------------------
*/

$mensaje = trim(
    (string) ($datos['mensaje'] ?? '')
);

$historialRecibido = $datos['historial'] ?? [];

if ($mensaje === '') {
    respuestaJson(
        false,
        'Escribe un mensaje.'
    );
}

if (mb_strlen($mensaje, 'UTF-8') > 1000) {
    respuestaJson(
        false,
        'El mensaje no puede superar los 1000 caracteres.'
    );
}

if (!is_array($historialRecibido)) {
    $historialRecibido = [];
}

/*
|--------------------------------------------------------------------------
| Configuración de Ollama
|--------------------------------------------------------------------------
*/

$ollamaUrl = 'http://127.0.0.1:11434/api/chat';

$modelo = 'llama3.2:latest';

/*
|--------------------------------------------------------------------------
| Consultar servicios activos
|--------------------------------------------------------------------------
*/

$conexion = null;
$contextoServicios = '';

try {

    $conexion = Database::conectar();

    $consultaServicios = $conexion->query("
        SELECT
            nombre,
            descripcion,
            precio,
            caracteristicas
        FROM servicios
        WHERE estado = 'activo'
        ORDER BY precio ASC
    ");

    $servicios = $consultaServicios->fetchAll();

    $lineasServicios = [];

    foreach ($servicios as $servicio) {

        $caracteristicas = array_filter(
            array_map(
                'trim',
                explode(
                    '|',
                    $servicio['caracteristicas'] ?? ''
                )
            )
        );

        $lineasServicios[] =
            'Plan: ' .
            $servicio['nombre'] .
            '. Precio: ' .
            formatearPrecio($servicio['precio']) .
            '. Descripción: ' .
            $servicio['descripcion'] .
            '. Incluye: ' .
            implode(', ', $caracteristicas) .
            '.';
    }

    if (!empty($lineasServicios)) {

        $contextoServicios = implode(
            "\n",
            $lineasServicios
        );

    } else {

        $contextoServicios =
            'Actualmente no hay planes activos registrados.';
    }

} catch (Throwable $error) {

    error_log(
        'Error consultando servicios para el chatbot: ' .
        $error->getMessage()
    );

    $contextoServicios =
        'Los planes y precios pueden consultarse en la página Servicios.';
}

/*
|--------------------------------------------------------------------------
| Información oficial del asistente
|--------------------------------------------------------------------------
*/

$systemPrompt = <<<PROMPT
Eres el asistente virtual de Digital Brand.

Digital Brand es un proyecto de estudiantes de grado 11 del programa Técnico en Programación de Software, desarrollado mediante la media técnica con el SENA en Copacabana, Antioquia.

Ayudas a visitantes y clientes con información sobre:

- planes y precios;
- diseño y desarrollo de páginas web;
- registro e inicio de sesión;
- solicitud de planes;
- servicios contratados;
- avance de proyectos;
- portafolio;
- contacto y WhatsApp.

Información oficial:

Municipio: Copacabana, Antioquia.
WhatsApp: +57 333 284 3241.
Correo: DigitalBrandpaginaseficientes@gmail.com.
Eslogan: Páginas eficientes para empresas que rinden.

Planes activos:

{$contextoServicios}

Instrucciones:

1. Responde siempre en español.
2. Sé cordial, claro y breve.
3. No inventes precios, servicios ni características.
4. Usa únicamente la información disponible en este contexto.
5. Para solicitar un plan, indica que el usuario debe registrarse, iniciar sesión, ingresar a Servicios y seleccionar Solicitar plan.
6. Para consultar el avance de un proyecto, indica que debe iniciar sesión y entrar a Mis servicios contratados.
7. Para hablar con una persona, recomienda la página Contacto o WhatsApp.
8. Nunca solicites contraseñas, documentos, datos bancarios ni información privada.
9. Si preguntan algo que no está relacionado con Digital Brand, explica amablemente que solo puedes orientar sobre el proyecto y sus servicios.
10. No digas que eres ChatGPT. Eres el asistente virtual de Digital Brand.
11. Evita respuestas demasiado largas.
PROMPT;

/*
|--------------------------------------------------------------------------
| Limpiar historial
|--------------------------------------------------------------------------
*/

$historialLimpio = [];

foreach ($historialRecibido as $elemento) {

    if (!is_array($elemento)) {
        continue;
    }

    $rol = trim(
        (string) ($elemento['role'] ?? '')
    );

    $contenido = trim(
        (string) ($elemento['content'] ?? '')
    );

    if (
        !in_array(
            $rol,
            ['user', 'assistant'],
            true
        )
    ) {
        continue;
    }

    if ($contenido === '') {
        continue;
    }

    if (mb_strlen($contenido, 'UTF-8') > 1500) {

        $contenido = mb_substr(
            $contenido,
            0,
            1500,
            'UTF-8'
        );
    }

    $historialLimpio[] = [
        'role' => $rol,
        'content' => $contenido
    ];
}

if (count($historialLimpio) > 12) {

    $historialLimpio = array_slice(
        $historialLimpio,
        -12
    );
}

/*
|--------------------------------------------------------------------------
| Construir mensajes para Ollama
|--------------------------------------------------------------------------
*/

$mensajesOllama = [
    [
        'role' => 'system',
        'content' => $systemPrompt
    ]
];

foreach ($historialLimpio as $elemento) {

    $mensajesOllama[] = $elemento;
}

$mensajesOllama[] = [
    'role' => 'user',
    'content' => $mensaje
];

/*
|--------------------------------------------------------------------------
| Crear contenido JSON
|--------------------------------------------------------------------------
*/

$payload = json_encode(
    [
        'model' => $modelo,
        'messages' => $mensajesOllama,
        'stream' => false,
        'options' => [
            'temperature' => 0.2,
            'top_p' => 0.9,
            'num_predict' => 300
        ]
    ],
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

if ($payload === false) {
    respuestaJson(
        false,
        'No fue posible preparar la consulta para el asistente.'
    );
}

/*
|--------------------------------------------------------------------------
| Validar cURL
|--------------------------------------------------------------------------
*/

if (!function_exists('curl_init')) {

    respuestaJson(
        false,
        'La extensión cURL no está habilitada en PHP.'
    );
}

/*
|--------------------------------------------------------------------------
| Conectar con Ollama
|--------------------------------------------------------------------------
*/

$curl = curl_init($ollamaUrl);

curl_setopt_array(
    $curl,
    [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_PROXY => '',
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
    ]
);

$respuestaOllama = curl_exec($curl);

$numeroErrorCurl = curl_errno($curl);

$errorCurl = curl_error($curl);

$codigoHttp = (int) curl_getinfo(
    $curl,
    CURLINFO_HTTP_CODE
);

curl_close($curl);

/*
|--------------------------------------------------------------------------
| Comprobar conexión
|--------------------------------------------------------------------------
*/

if ($respuestaOllama === false) {

    error_log(
        'Error de conexión con Ollama. Código: ' .
        $numeroErrorCurl .
        '. Detalle: ' .
        $errorCurl
    );

    respuestaJson(
        false,
        'No fue posible conectar con el asistente. Detalle: ' .
        (
            $errorCurl !== ''
                ? $errorCurl
                : 'Error desconocido.'
        )
    );
}

if (
    $codigoHttp < 200 ||
    $codigoHttp >= 300
) {

    error_log(
        'Ollama respondió HTTP ' .
        $codigoHttp .
        ': ' .
        $respuestaOllama
    );

    respuestaJson(
        false,
        'El asistente devolvió un error HTTP ' .
        $codigoHttp .
        '.'
    );
}

/*
|--------------------------------------------------------------------------
| Interpretar la respuesta
|--------------------------------------------------------------------------
*/

$resultadoOllama = json_decode(
    $respuestaOllama,
    true
);

if (!is_array($resultadoOllama)) {

    error_log(
        'Respuesta no JSON de Ollama: ' .
        $respuestaOllama
    );

    respuestaJson(
        false,
        'El asistente devolvió una respuesta no válida.'
    );
}

if (
    !isset(
        $resultadoOllama['message']['content']
    )
) {

    error_log(
        'Respuesta incompleta de Ollama: ' .
        $respuestaOllama
    );

    respuestaJson(
        false,
        'El asistente no generó el contenido esperado.'
    );
}

$respuestaBot = trim(
    (string) $resultadoOllama['message']['content']
);

if ($respuestaBot === '') {

    respuestaJson(
        false,
        'El asistente generó una respuesta vacía.'
    );
}

if (mb_strlen($respuestaBot, 'UTF-8') > 4000) {

    $respuestaBot = mb_substr(
        $respuestaBot,
        0,
        4000,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| Guardar pregunta y respuesta
|--------------------------------------------------------------------------
*/

try {

    if (!$conexion instanceof PDO) {
        $conexion = Database::conectar();
    }

    $idUsuario = isset($_SESSION['id_usuario'])
        ? (int) $_SESSION['id_usuario']
        : null;

    $guardarConversacion = $conexion->prepare("
        INSERT INTO chatbot_mensajes (
            id_usuario,
            mensaje,
            respuesta
        )
        VALUES (?, ?, ?)
    ");

    $guardarConversacion->execute([
        $idUsuario,
        $mensaje,
        $respuestaBot
    ]);

} catch (Throwable $error) {

    error_log(
        'Error guardando conversación del chatbot: ' .
        $error->getMessage()
    );
}

/*
|--------------------------------------------------------------------------
| Respuesta final
|--------------------------------------------------------------------------
*/

header('Content-Type: application/json; charset=utf-8');

echo json_encode(
    [
        'ok' => true,
        'msg' => 'Respuesta generada correctamente.',
        'respuesta' => $respuestaBot,
        'modelo' => $modelo
    ],
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

exit;