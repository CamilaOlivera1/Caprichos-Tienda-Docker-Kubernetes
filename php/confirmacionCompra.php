<?php
session_start();
require_once('../php/database.php');  // Conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user'])) {
    header("Location: inicioSesion.php");  // Redirigir al login si no está logueado
    exit;
}

// Obtener el id del usuario desde la sesión
$usuario_id = $_SESSION['user'];  

// Consultar el correo electrónico del usuario con su ID
$queryUsuario = "SELECT email FROM registro WHERE id = ?";
$stmtUsuario = $pdo->prepare($queryUsuario);
$stmtUsuario->execute([$usuario_id]);

if ($stmtUsuario->rowCount() === 0) {
    die("Error: Usuario no encontrado.");
}

$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
$usuario_email = $usuario['email'];  // Obtener el correo electrónico del usuario

// Obtener la última compra realizada por el usuario
$queryCompra = "SELECT * FROM compras WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 1";
$stmtCompra = $pdo->prepare($queryCompra);
$stmtCompra->execute([$usuario_id]);

// Verificar si se encontró la compra
$compra = $stmtCompra->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    die("Error: No se pudo encontrar la compra.");
}

// Obtener detalles de los productos comprados
$queryProductos = "SELECT p.nombre, pc.cantidad, pc.precio_unitario, pc.subtotal FROM productos_comprados pc JOIN productos p ON pc.producto_id = p.id WHERE pc.compra_id = ?";
$stmtProductos = $pdo->prepare($queryProductos);
$stmtProductos->execute([$compra['id']]);
$productosComprados = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="confirmacion-compra">
        <h1>¡Gracias por tu compra!</h1>
        <p>Tu compra se ha procesado correctamente.</p>
        <p>Esperamos que lo disfrutes y luzcas tus prendas con toda emoción.</p>

        <p>El número de tu compra es: <strong><?php echo $compra['id']; ?></strong></p>

        <h2>Detalles de tu compra:</h2>
        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($productosComprados as $producto): ?>
            <tr>
                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                <td><?php echo $producto['cantidad']; ?></td>
                <td>$<?php echo number_format($producto['precio_unitario'], 0, ',', '.'); ?></td>
                <td>$<?php echo number_format($producto['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p>¿Te gustaría volver a la <a href="../index.php">página de inicio?</a></p>
    </div>
</body>
</html>
