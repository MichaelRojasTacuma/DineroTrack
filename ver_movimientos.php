<?php
// Incluir la conexión a la base de datos
require 'conexion.php';

// Inicializar variables
$movimientos = [];
$nombre_usuario = '';
$total_ingresos = 0;
$total_gastos = 0;
$total_ahorros = 0;
$total_retiros = 0;
$total_global = 0;

if (isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];

    // Obtener el nombre del usuario seleccionado
    $stmt_usuario = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = :usuario_id");
    $stmt_usuario->execute(['usuario_id' => $usuario_id]);
    $usuario = $stmt_usuario->fetch();

    if ($usuario) {
        $nombre_usuario = $usuario['nombre'];

        // Realizar la consulta para obtener los movimientos del usuario
        $stmt = $pdo->prepare("SELECT cf.*, tm.nombre AS tipo_nombre, cg.nombre_categoria AS categoria_nombre 
                                FROM control_financiero cf
                                LEFT JOIN tipos_movimientos tm ON cf.tipo_id = tm.id
                                LEFT JOIN categorias_gastos cg ON cf.categoria_id = cg.id
                                WHERE cf.usuario_id = :usuario_id
                                ORDER BY cf.fecha DESC");
        $stmt->execute(['usuario_id' => $usuario_id]);
        $movimientos = $stmt->fetchAll();

        // Calcular total de ingresos
        $total_ingresos_stmt = $pdo->prepare("SELECT SUM(cantidad) AS total FROM control_financiero WHERE usuario_id = :usuario_id AND tipo_id = 1"); // 1 para ingresos
        $total_ingresos_stmt->execute(['usuario_id' => $usuario_id]);
        $total_ingresos = $total_ingresos_stmt->fetchColumn();

        // Calcular total de gastos
        $total_gastos_stmt = $pdo->prepare("SELECT SUM(cantidad) AS total FROM control_financiero WHERE usuario_id = :usuario_id AND tipo_id = 2"); // 2 para gastos
        $total_gastos_stmt->execute(['usuario_id' => $usuario_id]);
        $total_gastos = $total_gastos_stmt->fetchColumn();

        // Calcular total de ahorros
        $total_ahorros_stmt = $pdo->prepare("SELECT SUM(cantidad) AS total FROM control_financiero WHERE usuario_id = :usuario_id AND tipo_id = 3"); // 3 para ahorros
        $total_ahorros_stmt->execute(['usuario_id' => $usuario_id]);
        $total_ahorros = $total_ahorros_stmt->fetchColumn();

        // Calcular total de retiros
        $total_retiros_stmt = $pdo->prepare("SELECT SUM(cantidad) AS total FROM control_financiero WHERE usuario_id = :usuario_id AND tipo_id = 4"); // 4 para retiros
        $total_retiros_stmt->execute(['usuario_id' => $usuario_id]);
        $total_retiros = $total_retiros_stmt->fetchColumn();
        
        // Calcular el monto total global
        $total_global = ($total_ingresos ?? 0) + ($total_ahorros ?? 0);
    } else {
        echo "Usuario no encontrado.";
    }
}

// Registrar un nuevo ahorro o retiro
if (isset($_POST['accion'])) {
    $cantidad = $_POST['cantidad'];
    $descripcion = $_POST['descripcion'];
    
    if ($_POST['accion'] === 'ahorrar') {
        // Registrar ahorro
        $tipo_id = 3; // 3 para ahorros
        $stmt = $pdo->prepare("INSERT INTO control_financiero (usuario_id, tipo_id, cantidad, descripcion, fecha) VALUES (:usuario_id, :tipo_id, :cantidad, :descripcion, NOW())");
        $stmt->execute(['usuario_id' => $usuario_id, 'tipo_id' => $tipo_id, 'cantidad' => $cantidad, 'descripcion' => $descripcion]);
        echo "Ahorro registrado con éxito.";
    } elseif ($_POST['accion'] === 'retirar') {
        // Registrar retiro
        $tipo_id = 4; // 4 para retiros
        $stmt = $pdo->prepare("INSERT INTO control_financiero (usuario_id, tipo_id, cantidad, descripcion, fecha) VALUES (:usuario_id, :tipo_id, :cantidad, :descripcion, NOW())");
        $stmt->execute(['usuario_id' => $usuario_id, 'tipo_id' => $tipo_id, 'cantidad' => -$cantidad, 'descripcion' => $descripcion]); // Resta la cantidad
        echo "Retiro registrado con éxito.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Movimientos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Ver Movimientos</h2>
    <form method="POST" action="">
        <label for="usuario_id">Seleccionar Usuario:</label>
        <select name="usuario_id" required>
            <option value="">Seleccionar usuario</option>
            <?php
            // Obtener los usuarios desde la base de datos
            $stmt = $pdo->query("SELECT id, nombre FROM usuarios");
            while ($usuario = $stmt->fetch()) {
                echo "<option value=\"{$usuario['id']}\">{$usuario['nombre']}</option>";
            }
            ?>
        </select>
        <input type="submit" value="Ver Movimientos">
    </form>

    <?php if (!empty($movimientos)): ?>
        <h3>Movimientos de <?php echo htmlspecialchars($nombre_usuario); ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($movimiento['tipo_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['categoria_nombre'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Total de Ingresos: <?php echo htmlspecialchars($total_ingresos ?? 0); ?></h4>
        <h4>Total de Gastos: <?php echo htmlspecialchars($total_gastos ?? 0); ?></h4>
        <h4>Total de Ahorros: <?php echo htmlspecialchars($total_ahorros ?? 0); ?></h4>
        <h4>Total Global: <?php echo htmlspecialchars($total_global ?? 0); ?></h4>
        <h4>Resultado (Total Global - Total Retiros): <?php echo htmlspecialchars(($total_global ?? 0) - ($total_retiros ?? 0)); ?></h4>
        
        <!-- Formularios para gestionar ahorros -->
        <h3>Registrar Ahorro</h3>
        <form method="POST" action="">
            <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario_id); ?>">
            <input type="hidden" name="accion" value="ahorrar">
            <label for="cantidad">Monto a ahorrar:</label>
            <input type="number" name="cantidad" required>
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" required>
            <input type="submit" value="Registrar Ahorro">
        </form>

        <h3>Retirar Ahorro</h3>
        <form method="POST" action="">
            <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario_id); ?>">
            <input type="hidden" name="accion" value="retirar">
            <label for="cantidad">Monto a retirar:</label>
            <input type="number" name="cantidad" required>
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" required>
            <input type="submit" value="Registrar Retiro">
        </form>
    <?php else: ?>
        <?php if (isset($usuario)): ?>
            <p>No hay movimientos registrados para este usuario.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
