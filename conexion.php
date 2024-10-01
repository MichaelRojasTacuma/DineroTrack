<?php
// config.php

$host = 'localhost';         // Servidor de la base de datos
$dbname = 'dinerotrack';  // Nombre de la base de datos
$username = 'root';    // Usuario de la base de datos
$password = ''; // Contraseña del usuario

// Establecer la conexión con la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
