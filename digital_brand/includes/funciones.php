<?php

function limpiarDato($dato)
{
    return trim($dato);
}

function escapar($dato)
{
    return htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
}

function redirigir($ruta)
{
    header("Location: " . $ruta);
    exit;
}

function usuarioAutenticado()
{
    return isset($_SESSION['id_usuario']);
}

function esAdministrador()
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador';
}

function respuestaJson($ok, $mensaje, $datos = [])
{
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        array_merge(
            [
                'ok' => $ok,
                'msg' => $mensaje
            ],
            $datos
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

function validarCorreo($correo)
{
    return filter_var($correo, FILTER_VALIDATE_EMAIL);
}

function validarContrasena($contrasena)
{
    return strlen($contrasena) >= 8;
}

function generarToken()
{
    return bin2hex(random_bytes(32));
}

function formatearPrecio($precio)
{
    return '$' . number_format($precio, 0, ',', '.');
}

function obtenerIniciales($nombre)
{
    $palabras = explode(' ', trim($nombre));
    $iniciales = '';

    foreach ($palabras as $palabra) {
        if ($palabra !== '') {
            $iniciales .= strtoupper(substr($palabra, 0, 1));
        }

        if (strlen($iniciales) === 2) {
            break;
        }
    }

    return $iniciales;
}