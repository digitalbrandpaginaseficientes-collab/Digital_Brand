<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/funciones.php';

function requerirLogin()
{
    if (!usuarioAutenticado()) {
        $_SESSION['mensaje_error'] = 'Debes iniciar sesión para acceder a esta sección.';
        redirigir(APP_URL . '/login.php');
    }
}

function requerirAdministrador()
{
    if (!usuarioAutenticado()) {
        $_SESSION['mensaje_error'] = 'Debes iniciar sesión.';
        redirigir(APP_URL . '/login.php');
    }

    if (!esAdministrador()) {
        $_SESSION['mensaje_error'] = 'No tienes permisos para acceder al panel administrativo.';
        redirigir(APP_URL . '/usuario/index.php');
    }
}

function requerirCliente()
{
    if (!usuarioAutenticado()) {
        $_SESSION['mensaje_error'] = 'Debes iniciar sesión.';
        redirigir(APP_URL . '/login.php');
    }

    if (esAdministrador()) {
        redirigir(APP_URL . '/admin/index.php');
    }
}

function obtenerUsuarioSesion()
{
    if (!usuarioAutenticado()) {
        return null;
    }

    return [
        'id_usuario' => $_SESSION['id_usuario'],
        'nombre' => $_SESSION['nombre'],
        'correo' => $_SESSION['correo'],
        'rol' => $_SESSION['rol']
    ];
}