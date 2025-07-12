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

// Obtener los productos del carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

if (empty($carrito)) {
    header("Location: carrito.php");  // Si el carrito está vacío, redirigir al carrito
    exit;
}

// Calcular el total de la compra
$totalCompra = 0;
foreach ($carrito as $producto) {
    $totalCompra += $producto['precio'] * $producto['cantidad'];
}

// Asignar un costo fijo de envío de $3000
$costoEnvio = 3000;
$queryEnvios = "SELECT * FROM envios";
$stmtEnvios = $pdo->query($queryEnvios);
$envios = $stmtEnvios->fetchAll(PDO::FETCH_ASSOC);

// Consultar los medios de pago
$queryMediosPago = "SELECT * FROM medioPago";
$stmtMediosPago = $pdo->query($queryMediosPago);

// Procesar la compra cuando se presiona el botón "Comprar"
if (isset($_POST['finalizar'])) {
    $envio_id = $_POST['envio_id'];
    $medioPago_id = $_POST['medioPago_id'];
    
    // Insertar la compra en la base de datos
    $query = "INSERT INTO compras (usuario_id, envio_id, medioPago_id, total) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$usuario_id, $envio_id, $medioPago_id, $totalCompra]);

    // Obtener el ID de la compra insertada
    $compra_id = $pdo->lastInsertId();

    // Insertar los productos comprados
    foreach ($carrito as $id => $producto) {
        $queryProducto = "INSERT INTO productos_comprados (compra_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmtProducto = $pdo->prepare($queryProducto);
        $stmtProducto->execute([$compra_id, $id, $producto['cantidad'], $producto['precio'], $producto['precio'] * $producto['cantidad']]);
    }

    // Vaciar el carrito
    unset($_SESSION['carrito']);

    // Redirigir a la página de confirmación de compra
    header("Location: confirmacionCompra.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
    function mostrarFormularioPago() {
        const metodoPago = parseInt(document.getElementById("medioPago_id").value);
        const tarjetaForm = document.getElementById("form-tarjeta");

        if (metodoPago === 1 || metodoPago === 2) {
            tarjetaForm.style.display = "block";
        } else {
            tarjetaForm.style.display = "none";
        }
    }

    // Ejecutar al cargar la página (por si ya hay uno seleccionado)
    window.addEventListener('DOMContentLoaded', mostrarFormularioPago);
</script>
</head>
<body>
    <div class="finalizar-compra-container">
        <header>
            <h1>Finalizar Compra</h1>
        </header>
        <section>
            <h2>Total de la Compra</h2>
            <p>Subtotal de los productos: $<?php echo number_format($totalCompra, 0, ',', '.'); ?></p>

            <form action="finalizarCompra.php" method="post">
                <h3>Elige el tipo de envío</h3>

                <select name="envio_id" required>
    <?php foreach ($envios as $envio): ?>
        <option value="<?php echo $envio['id']; ?>">
            <?php echo htmlspecialchars($envio['destino']) . " - $" . number_format($envio['costo'], 0, ',', '.'); ?>
        </option>
    <?php endforeach; ?>
</select>

                
                

                <h3>Elige el medio de pago</h3>
                <select name="medioPago_id" id="medioPago_id" onchange="mostrarFormularioPago()" required>
                    <?php while ($medioPago = $stmtMediosPago->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $medioPago['id']; ?>"><?php echo htmlspecialchars($medioPago['nombre']); ?></option>
                    <?php endwhile; ?>
                </select>

                <h3>Costo de envío</h3>
                <p>$<?php echo number_format($costoEnvio, 0, ',', '.'); ?></p>

                <h3>Total a Pagar</h3>
                <p>$<?php echo number_format($totalCompra + $costoEnvio, 0, ',', '.'); ?></p>

                <!-- Formulario de tarjeta de crédito/débito -->
                <div id="form-tarjeta" style="display:none;">
                    <h3>Datos de la tarjeta</h3>
                    <label for="numero_tarjeta">Número de tarjeta:</label>
                    <input type="text" id="numero_tarjeta" name="numero_tarjeta" placeholder="1234 5678 9012 3456" required>
                    <label for="fecha_expiracion">Fecha de expiración:</label>
                    <input type="month" id="fecha_expiracion" name="fecha_expiracion" required>
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" required>
                </div>

                <button type="submit" name="finalizar">Comprar</button>
            </form>
        </section>
    </div>
</body>
</html>
