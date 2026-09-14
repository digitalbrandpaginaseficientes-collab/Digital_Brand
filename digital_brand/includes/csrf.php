<?php

function obtenerTokenCsrf()
{
    if (
        !isset($_SESSION['csrf_token']) ||
        empty($_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validarTokenCsrf($token)
{
    if (
        !isset($_SESSION['csrf_token']) ||
        empty($token)
    ) {
        return false;
    }

    return hash_equals(
        $_SESSION['csrf_token'],
        $token
    );
}

function renovarTokenCsrf()
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    return $_SESSION['csrf_token'];
}