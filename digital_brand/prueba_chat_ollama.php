<?php

header('Content-Type: text/plain; charset=utf-8');

$url = 'http://127.0.0.1:11434/api/chat';

$payload = json_encode(
    [
        'model' => 'llama3.2:latest',
        'messages' => [
            [
                'role' => 'user',
                'content' => 'Responde únicamente: conexión correcta'
            ]
        ],
        'stream' => false
    ],
    JSON_UNESCAPED_UNICODE |
    JSON_UNESCAPED_SLASHES
);

$curl = curl_init($url);

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

$respuesta = curl_exec($curl);

$numeroError = curl_errno($curl);
$error = curl_error($curl);

$codigoHttp = (int) curl_getinfo(
    $curl,
    CURLINFO_HTTP_CODE
);

curl_close($curl);

echo "PRUEBA DEL CHAT PHP → OLLAMA\n";
echo "============================\n\n";

echo "HTTP: " . $codigoHttp . "\n";
echo "cURL: " . $numeroError . "\n";
echo "Error: " . ($error !== '' ? $error : 'Ninguno') . "\n\n";

echo "Respuesta:\n";

if ($respuesta === false) {
    echo "No se recibió respuesta.";
} else {
    echo $respuesta;
}