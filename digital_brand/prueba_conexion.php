<?php

require_once 'config/database.php';
require_once 'includes/funciones.php';
require_once 'includes/csrf.php';
require_once 'includes/auth.php';

$conexion = Database::conectar();

$totalUsuarios = $conexion->query(
    "SELECT COUNT(*) FROM usuarios"
)->fetchColumn();

$totalServicios = $conexion->query(
    "SELECT COUNT(*) FROM servicios"
)->fetchColumn();

$token = obtenerTokenCsrf();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba general</title>
</head>
<body>

    <h1>Conexión Exitosa</h1>

    <p>Total usuarios: <?php echo $totalUsuarios; ?></p>

    <p>Total servicios: <?php echo $totalServicios; ?></p>

    <p>Precio de prueba: <?php echo formatearPrecio(300000); ?></p>

    <p>Iniciales de prueba: <?php echo obtenerIniciales('Digital Brand'); ?></p>

    <p>Token CSRF generado:</p>

    <textarea rows="3" cols="75" readonly><?php echo escapar($token); ?></textarea>

    <p>
        Usuario autenticado:
        <?php echo usuarioAutenticado() ? 'Sí' : 'No'; ?>
    </p>

    <p>
        Es administrador:
        <?php echo esAdministrador() ? 'Sí' : 'No'; ?>
    </p>

</body>
</html>