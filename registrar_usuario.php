<?php

require 'conexion.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre) VALUES (:nombre)");
    $stmt->execute(['nombre' => $nombre]);

    echo " usuario registrado con exito. ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>registrar Usuarios</title>
</head>
<body>
    <h2>registrar nuevo usuario</h2>
    <form method="POST" action="">
        <label for="nombre" placeholder="ingrese el nombre">Nombre</label>
        <input type="text"name="nombre" required><br>

        <input type="submit" value="Registrar Usuario">
    </form>
<!-- mostrar los usuarios registrados existentes -->
 <?php
//obtener los usuarios
$stmt= $pdo->query("SELECT * FROM usuarios");
while ($usuario = $stmt->fetch()){
    echo "<p>{$usuario['nombre']}</p>";
}
 ?>
</body>
</html>