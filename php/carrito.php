<?php
session_start();
require_once('../php/database.php');

// Guardar la categoría actual si viene por parámetro
if (isset($_GET['categoria'])) {
    $_SESSION['ultima_categoria'] = $_GET['categoria'];
}

// Inicializar carrito desde sesión
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$carrito = &$_SESSION['carrito'];

// Actualizar cantidad de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'], $_POST['cantidad'])) {
    $producto_id = intval($_POST['producto_id']);
    $nueva_cantidad = intval($_POST['cantidad']);

    // Consultar el stock actual desde la base de datos
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $stockActual = $stmt->fetchColumn();

    if ($stockActual !== false && isset($carrito[$producto_id])) {
        if ($nueva_cantidad <= 0) {
            $_SESSION['error'] = "La cantidad debe ser al menos 1.";
        } elseif ($nueva_cantidad > $stockActual) {
            $_SESSION['error'] = "Solo hay $stockActual unidades disponibles.";
        } else {
            $_SESSION['carrito'][$producto_id]['cantidad'] = $nueva_cantidad;
            $_SESSION['mensaje'] = "Cantidad actualizada correctamente.";
        }
    }
    header("Location: carrito.php");
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $producto_id = intval($_GET['eliminar']);
    unset($carrito[$producto_id]);
    $_SESSION['mensaje'] = "Producto eliminado correctamente.";
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
        <h1>Carrito de Compras 🛒</h1>
    </header>

    <!-- Mensajes de éxito o error -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alerta-exito"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alerta-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

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
                        $stock = $stmt->fetchColumn() ?: 1;
                    ?>
                    <tr>
                        <td><img src="../imagenes/<?= htmlspecialchars($producto['imagen']); ?>" width="80"></td>
                        <td><?= htmlspecialchars($producto['nombre']); ?></td>
                        <td>$<?= number_format($producto['precio'], 0, ',', '.'); ?></td>
                        <td>
                            <form action="carrito.php" method="post">
                                <input type="hidden" name="producto_id" value="<?= $id; ?>">
                                <input type="number" name="cantidad" value="<?= $producto['cantidad']; ?>" min="1" max="<?= $stock; ?>" required>
                                <small>Disponibles: <?= $stock; ?></small><br>
                                <button type="submit" class="btn-actualizar">Actualizar</button>
                            </form>
                        </td>
                        <td>$<?= number_format($subtotal, 0, ',', '.'); ?></td>
                        <td><a href="carrito.php?eliminar=<?= $id; ?>" class="eliminar-link">Eliminar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong>$<?= number_format($totalCarrito, 0, ',', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <?php
            // Si el usuario llegó al carrito desde el inicio o sin categoría, ir al inicio
            if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'index.php') !== false) {
                $volverA = "../index.php";
            }
            // Si hay una categoría guardada, volver a esa categoría
            elseif (isset($_SESSION['ultima_categoria'])) {
                $volverA = "productos.php?categoria=" . urlencode($_SESSION['ultima_categoria']);
            }
            // En cualquier otro caso, volver al inicio
            else {
                $volverA = "../index.php";
            }
            ?>
            <a href="<?= $volverA; ?>" class="btn-volver">Seguir Comprando</a>
            
            <a href="finalizarCompra.php" class="btn-finalizar">Finalizar Compra</a>

        <?php endif; ?>
    </section>
</div>
</body>
</html>
