<?php
// Incluir la conexión a la base de datos
require 'conexion.php';


// Verificar si el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $tipo_movimiento_id = $_POST['tipo_movimiento_id'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];

    // Verificar si la categoría fue enviada, si no, dejarla como null
    $categoria_id = isset($_POST['categoria_id']) ? $_POST['categoria_id'] : null;

    // Si el tipo de movimiento es 'Ingreso' (id 1) o 'Ahorro' (id 3), no se necesita categoría
    if ($tipo_movimiento_id == 1 || $tipo_movimiento_id == 3) {
        $categoria_id = null; // No enviar la categoría
    }

    // Insertar el movimiento en la tabla control_financiero
    $stmt = $pdo->prepare("INSERT INTO control_financiero (usuario_id, tipo_id, cantidad, descripcion, fecha, categoria_id) 
                           VALUES (:usuario_id, :tipo_movimiento_id, :monto, :descripcion, NOW(), :categoria_id)");
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'tipo_movimiento_id' => $tipo_movimiento_id,  // Cambiar 'tipo_id' en el array a 'tipo_movimiento_id'
        'monto' => $monto,  // Cambiar el nombre 'cantidad' en el array a 'monto'
        'descripcion' => $descripcion,
        'categoria_id' => $categoria_id
    ]);

    echo "Movimiento registrado con éxito.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Movimiento</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Registrar Movimiento</h2>
    <form method="POST" action="">
        <!-- Selección del usuario -->
        <label for="usuario_id">Usuario:</label>
        <select name="usuario_id" required>
            <option value="">Seleccionar usuario</option>
            <?php
            // Obtener los usuarios desde la base de datos
            $stmt = $pdo->query("SELECT id, nombre FROM usuarios");
            while ($usuario = $stmt->fetch()) {
                echo "<option value=\"{$usuario['id']}\">{$usuario['nombre']}</option>";
            }
            ?>
        </select><br>

        <!-- Selección del tipo de movimiento (Ingreso, Gasto, Ahorro) -->
        <label for="tipo_movimiento_id">Tipo de Movimiento:</label>
        <select name="tipo_movimiento_id" required>
            <option value="">Seleccionar tipo</option>
            <?php
            // Obtener los tipos de movimientos desde la base de datos
            $stmt = $pdo->query("SELECT id, nombre FROM tipos_movimientos");
            while ($tipo = $stmt->fetch()) {
                echo "<option value=\"{$tipo['id']}\">{$tipo['nombre']}</option>";
            }
            ?>
        </select><br>

        <!-- Selección de la categoría de gasto -->
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" required>
            <option value="">Seleccionar categoría</option>
            <?php
            // Obtener las categorías desde la base de datos
            $stmt = $pdo->query("SELECT id, nombre_categoria FROM categorias_gastos");
            while ($categoria = $stmt->fetch()) {
                echo "<option value=\"{$categoria['id']}\">{$categoria['nombre_categoria']}</option>";
            }
            ?>
        </select><br>

        <!-- Campo para el monto -->
        <label for="monto">Monto:</label>
        <input type="number" name="monto" step="0.01" required><br>

        <!-- Campo opcional para la descripción -->
        <label for="descripcion">Descripción (opcional):</label>
        <input type="text" name="descripcion"><br>

        <input type="submit" value="Registrar Movimiento">
    </form>
</body>
</html>
