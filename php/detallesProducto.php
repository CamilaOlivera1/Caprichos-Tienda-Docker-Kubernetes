<?php 
session_start();

include 'database.php'; 

$sesionIniciada = isset($_SESSION['user']); // Verificar sesión activa

// Verificar si se recibe el ID del producto
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $producto_id = $_GET['id'];
} else {
    header("Location: ../index.php"); // Redirigir si no hay ID válido
    exit;
}

// Consulta para obtener los detalles del producto
$query = "
    SELECT p.*, c.nombre AS categoria_nombre
    FROM productos p
    JOIN categorias c ON p.categoria_id = c.id
    WHERE p.id = :producto_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar si se encontró el producto
if (!$producto) {
    header("Location: ../index.php"); // Redirigir si no existe el producto
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Detalles del Producto</h1>
    </header>

    <section class="producto-detalle">
        <div class="producto-imagen">
            <img src="../imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
        </div>
        <div class="producto-info">
            <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
            <p><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 0, ',', '.'); ?></p>
            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
            <p><strong>Stock:</strong> <?php echo $producto['stock']; ?></p>

            <?php if ($sesionIniciada): ?>
                <!-- Formulario para agregar al carrito -->
                <?php if ($producto['stock'] > 0): ?>
                    <form action="agregarCarrito.php" method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto['id']); ?>">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" name="cantidad" id="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" required>
                        <button type="submit" class="btn-agregarCarrito">Agregar al Carrito</button>
                    </form>
                <?php else: ?>
                    <p class="sin-stock">Este producto no está disponible en este momento.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Debes <a href="inicioSesion.php">iniciar sesión</a> para agregar productos al carrito.</p>
            <?php endif; ?>

            <!-- Botón para volver a la categoría -->
            <a href="productos.php?categoria=<?php echo urlencode($producto['categoria_nombre']); ?>" class="btn-volver">Volver a la categoría</a>

        </div>
    </section>
</body>
</html>
