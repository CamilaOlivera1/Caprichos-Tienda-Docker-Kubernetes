<?php
session_start();
require_once('../php/database.php');

// Guardar la categoría actual si viene por parámetro
if (isset($_GET['categoria'])) {
    $_SESSION['ultima_categoria'] = $_GET['categoria'];
}

// Inicializar carrito
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$carrito = &$_SESSION['carrito'];


//   ACTUALIZAR CANTIDAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clave'], $_POST['cantidad'])) {

    // Clave del carrito → "ID_TALLE"
    $clave = $_POST['clave'];

    // Verificar que exista en el carrito
    if (!isset($_SESSION['carrito'][$clave])) {
        $_SESSION['error'] = "Producto no encontrado.";
        header("Location: carrito.php");
        exit;
    }

    // Obtener producto del carrito
    list($producto_id, $talle) = explode("_", $clave);

    // Nueva cantidad
    $nueva_cantidad = intval($_POST['cantidad']);

    // Obtener stock desde la base
    $stmt = $pdo->prepare("SELECT stock FROM productos_talles WHERE producto_id = ? AND talle = ?");
    $stmt->execute([$producto_id, $talle]);
    $stockActual = $stmt->fetchColumn();

    // Validaciones de stock y cantidad
    if ($stockActual === false) {
        $_SESSION['error'] = "No se pudo obtener el stock.";
    } elseif ($nueva_cantidad <= 0) {
        $_SESSION['error'] = "La cantidad debe ser al menos 1.";
    } elseif ($nueva_cantidad > $stockActual) {
        $_SESSION['error'] = "Solo hay $stockActual unidades disponibles.";
    } else {
        // Actualizar cantidad correctamente
        $_SESSION['carrito'][$clave]['cantidad'] = $nueva_cantidad;
        $_SESSION['mensaje'] = "Cantidad actualizada correctamente.";
    }

    header("Location: carrito.php");
    exit;
}


//   ELIMINAR PRODUCTO
if (isset($_GET['eliminar'])) {
    $clave = $_GET['eliminar'];
    unset($_SESSION['carrito'][$clave]);// Elimina el item según su clave única
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
            <!-- Carrito vacío -->
            <p>Tu carrito está vacío. <a href="../index.php">Volver a la tienda</a></p>
        <?php else: ?>

            <table>
                <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Talle</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $totalCarrito = 0; // Acumulador del total del carrito

                foreach ($carrito as $clave => $producto):

                    // Obtener ID y talle desde la clave
                    list($producto_id, $talle) = explode("_", $clave);

                    $subtotal = $producto['precio'] * $producto['cantidad'];
                    $totalCarrito += $subtotal;

                    // Obtener stock actualizado
                    $stmt = $pdo->prepare("SELECT stock FROM productos_talles WHERE producto_id = ? AND talle = ?");
                    $stmt->execute([$producto_id, $talle]);
                    $stock = $stmt->fetchColumn() ?: 0;
                ?>

                    <tr>
                        <td><img src="../imagenes/<?= htmlspecialchars($producto['imagen']); ?>" width="80"></td>
                        <td><?= htmlspecialchars($producto['nombre']); ?></td>
                        <td>$<?= number_format($producto['precio'], 0, ',', '.'); ?></td>

                        <td>
                            <!-- Formulario para actualizar cantidad -->
                            <form action="carrito.php" method="post">
                                <input type="hidden" name="clave" value="<?= $producto_id . "_" . $talle; ?>">

                                <input type="number" name="cantidad"
                                    value="<?= $producto['cantidad']; ?>"min="1" max="<?= $stock; ?>" required>

                                <small>Disponibles: <?= $stock; ?></small><br>

                                <button type="submit" class="btn-actualizar">Actualizar</button>
                            </form>
                        </td>

                        <!--AQUII AGREGO LO EDL TALLE PARA QUE SE VEA-->
                        <td><?= htmlspecialchars($producto['talle']); ?></td>

                        <td>$<?= number_format($subtotal, 0, ',', '.'); ?></td>

                        <td>
                            <!-- Eliminar producto -->
                            <a href="carrito.php?eliminar=<?= $producto_id . "_" . $talle; ?>"
                            class="eliminar-link">Eliminar</a>
                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>

                <tfoot>
                <tr>
                    <td colspan="5"><strong>Total</strong></td>
                    <td><strong>$<?= number_format($totalCarrito, 0, ',', '.'); ?></strong></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>


            <?php
            // Determinar a dónde volver
            if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'index.php') !== false) {
                $volverA = "../index.php";
            } elseif (isset($_SESSION['ultima_categoria'])) {
                $volverA = "productos.php?categoria=" . urlencode($_SESSION['ultima_categoria']);
            } else {
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
