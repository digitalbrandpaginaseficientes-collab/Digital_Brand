<?php

require_once __DIR__ . '/config.php';

class Database
{
    private static $conexion = null;

    public static function conectar()
    {
        if (self::$conexion === null) {

            try {

                self::$conexion = new PDO(

                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",

                    DB_USER,

                    DB_PASS

                );

                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {

                die("Error de conexión: " . $e->getMessage());

            }

        }

        return self::$conexion;
    }
}