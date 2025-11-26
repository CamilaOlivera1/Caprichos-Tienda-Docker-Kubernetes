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
//consulta de talles
//$idProducto = $_GET['id']; // o como lo recibas

require '../php/database.php';

/*$stmt = $conn->prepare("SELECT talle, stock FROM productos_talles WHERE producto_id = ?");
$stmt->bind_param("i", $idProducto);
$stmt->execute();
$resultTalles = $stmt->get_result();*/

// Obtener los talles disponibles
$stmtTalles = $pdo->prepare("SELECT talle, stock FROM productos_talles WHERE producto_id = ?");
$stmtTalles->execute([$producto_id]);
$resultTalles = $stmtTalles->fetchAll(PDO::FETCH_ASSOC);

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
<!--PARA CUANDO NO HAY STOCK-->
    <?php if (isset($_GET['error'])): ?>
    <div class="alerta-error">
        <?php if ($_GET['error'] === 'stock'): ?>
            <p>No hay suficiente stock para la cantidad seleccionada.</p>
        <?php elseif ($_GET['error'] === 'stocklimit'): ?>
            <p>No podés agregar más cantidad. Ya alcanzaste el límite de stock.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <section class="producto-detalle">
        <div class="producto-imagen">
            <img src="../imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
        </div>
        <div class="producto-info">
            <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['descripcion']); ?></p>
            <p><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 0, ',', '.'); ?></p>
            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>

            <?php if ($sesionIniciada): ?>
                <!-- Formulario para agregar al carrito -->
                <?php if (!empty($resultTalles)): ?>
                    <!--<form action="../php/agregarCarrito.php" method="POST">-->
                        <form action="agregarCarrito.php" method="POST">

                        <input type="hidden" name="producto_id" value="<?= $producto_id; ?>">
                    <input type="hidden" name="categoria" value="<?= htmlspecialchars($producto['categoria_nombre']); ?>">

                    <label for="talle"><strong>Talle:</strong></label>
                    <select name="talle" id="talle" required>
                        <option value="">Elegí un talle</option>
                        <?php foreach ($resultTalles as $t): ?>
                            <?php if ($t['stock'] > 0): ?>
                                <option 
                                value="<?= $t['talle']; ?>" 
                                data-stock="<?= $t['stock']; ?>">
                                <?= $t['talle']; ?> (<?= $t['stock']; ?> disponibles)
                            </option>
                            <?php else: ?>
                                <option value="<?= $t['talle']; ?>" disabled>
                                    <?= $t['talle']; ?> (Sin stock)
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            
                            <label for="cantidad"><strong>Cantidad:</strong></label>
                            <input
                            type="number" 
                            name="cantidad" 
                            id="cantidad"
                            value="1"
                            min="1"
                            required
                            >
                            <script>

                            // Cuando cambia el talle, se actualiza el máximo de cantidad permitida
                            document.getElementById('talle').addEventListener('change', function() {
                                let stock = this.options[this.selectedIndex].dataset.stock;
                                document.getElementById('cantidad').max = stock;
                                });
                                </script>

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
