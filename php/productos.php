<?php
session_start();

// Conexión a la base de datos
include 'database.php'; // Archivo que contiene la conexión

// Si llega una categoría, filtramos. Si no, mostramos todos los productos
$categoria = $_GET['categoria'] ?? null;

if ($categoria) {
    // Consulta con categoría
    $query = "
        SELECT p.*
        FROM productos p
        JOIN categorias c ON p.categoria_id = c.id
        WHERE c.nombre = :categoria
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
} else {
    // Consulta SIN categoría → trae todos los productos
    $query = "SELECT * FROM productos";
    $stmt = $pdo->prepare($query);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoria); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <!-- Muestra la categoría como título. Si no hay, "Todos los productos" -->
<h1>
    <?php echo $categoria ? htmlspecialchars($categoria) : "Todos los productos"; ?>
</h1>
    </header>

    <section class="productos-grid">
        <?php if (!empty($result)): ?>
            <?php foreach ($result as $row): ?>
                <div class="producto">
                    <!-- Imagen del producto -->
                    <img src="../imagenes/<?php echo htmlspecialchars($row['imagen']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                    <!-- Nombre del producto -->
                    <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                    <p class="precio">$<?php echo number_format($row['precio'], 0, ',', '.'); ?></p>
                    <!-- Enlace para ver los detalles del producto -->
                    <a href="detallesProducto.php?id=<?php echo $row['id']; ?>" class="btn-detalles">Ver Detalles</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay productos disponibles en esta categoría.</p>
        <?php endif; ?>
    </section>

    
    <!-- Botón para volver al inicio -->
    <a href="../index.php" class="btn-volver">Volver al inicio</a>
</body>
</html>
