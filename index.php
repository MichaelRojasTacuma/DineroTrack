<?php
// index.php
require 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineroTrack - Panel Principal</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link a tu archivo de estilos CSS -->
</head>
<body>
    <div class="container">
        <h1>DineroTrack - Control Financiero</h1>
        <div class="menu">
            <!-- Menú de navegación -->
            <button onclick="window.location.href='registrar_usuario.php'">➕ Agregar Nuevo Usuario</button>
            <button onclick="window.location.href='registrar_movimiento.php'">📊 Registrar Movimiento</button>
            <button onclick="window.location.href='ver_movimientos.php'">📂 Ver Movimientos</button>
            <button onclick="window.location.href='gestionar_ahorros.php'">💰 Gestionar Ahorros</button>
            <button onclick="window.location.href='estadisticas.php'">📈 Ver Estadísticas</button>
        </div>
    </div>
</body>
</html>

