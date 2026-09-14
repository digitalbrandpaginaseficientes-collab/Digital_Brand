<?php

header('Content-Type: text/plain; charset=utf-8');

echo "PRUEBA DE CONEXIÓN PHP → OLLAMA\n";
echo "================================\n\n";

echo "PHP: " . PHP_VERSION . "\n";
echo "cURL habilitado: " .
    (function_exists('curl_init') ? 'Sí' : 'No') .
    "\n\n";

if (!function_exists('curl_init')) {

    echo "ERROR: La extensión cURL no está habilitada en PHP.\n";
    exit;
}

$url = 'http://127.0.0.1:11434/api/tags';

$curl = curl_init($url);

curl_setopt_array(
    $curl,
    [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_PROXY => '',
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ]
    ]
);

$respuesta = curl_exec($curl);

$numeroError = curl_errno($curl);
$mensajeError = curl_error($curl);
$codigoHttp = curl_getinfo(
    $curl,
    CURLINFO_HTTP_CODE
);

curl_close($curl);

echo "URL consultada: " . $url . "\n";
echo "Código HTTP: " . $codigoHttp . "\n";
echo "Número de error cURL: " . $numeroError . "\n";
echo "Mensaje de error: " .
    ($mensajeError !== '' ? $mensajeError : 'Ninguno') .
    "\n\n";

if ($respuesta === false) {

    echo "RESULTADO: PHP no pudo conectarse con Ollama.\n";
    exit;
}

echo "RESULTADO: CONEXIÓN EXITOSA\n\n";
echo "Respuesta de Ollama:\n";
echo $respuesta;