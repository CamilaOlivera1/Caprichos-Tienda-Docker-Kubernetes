<?php
session_start();
require_once('../php/database.php');

// Inicializar carrito desde sesión
$carrito = &$_SESSION['carrito'];
if (!is_array($carrito)) {
    $carrito = [];
}

// Procesar actualización de cantidad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'], $_POST['cantidad'])) {
    $producto_id = intval($_POST['producto_id']);
    $nueva_cantidad = intval($_POST['cantidad']);

    // Consultar el stock actual desde la base de datos
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $stockActual = $stmt->fetchColumn();

    if (isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
    $producto_id = $_POST['producto_id'];
    $nueva_cantidad = intval($_POST['cantidad']);

    // Obtener stock actual desde la base de datos
    require_once 'database.php';
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto && isset($_SESSION['carrito'][$producto_id])) {
        $stockActual = $producto['stock'];

        if ($nueva_cantidad <= 0) {
            $_SESSION['error'] = "La cantidad debe ser al menos 1.";
        } elseif ($nueva_cantidad > $stockActual) {
            $_SESSION['error'] = "Solo hay $stockActual unidades disponibles.";
        } else {
            $_SESSION['carrito'][$producto_id]['cantidad'] = $nueva_cantidad;
        }
    }
    header("Location: carrito.php");
    exit;
}

}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $producto_id = intval($_GET['eliminar']);
    unset($carrito[$producto_id]);
    header("Location: carrito.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="carrito-container">
    <header>
        <h1>Carrito de Compras</h1>
    </header>

    <section class="carrito">
        <?php if (empty($carrito)): ?>
            <p>Tu carrito está vacío. <a href="../index.php">Volver a la tienda</a></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalCarrito = 0;
                    foreach ($carrito as $id => $producto):
                        $subtotal = $producto['precio'] * $producto['cantidad'];
                        $totalCarrito += $subtotal;

                        // Obtener stock actualizado
                        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
                        $stmt->execute([$id]);
                        $stock = $stmt->fetchColumn();
                        if ($stock === false) $stock = 1; // fallback
                    ?>
                    <tr>
                        <td><img src="../imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>" width="80"></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                        <td>
                            <form action="carrito.php" method="post">
                                <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" min="1" max="<?php echo $stock; ?>" required>
                                <small>Disponibles: <?php echo $stock; ?></small><br>
                                <button type="submit" class="btn-actualizar">Actualizar</button>

                                <!--<button type="submit">Actualizar</button>-->
                            </form>
                        </td>
                        <td>$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td><a href="carrito.php?eliminar=<?php echo $id; ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>$<?php echo number_format($totalCarrito, 0, ',', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <a href="finalizarCompra.php" class="btn-finalizar">Finalizar Compra</a>
            <a href="../index.php" class="btn-volver">Seguir Comprando</a>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
