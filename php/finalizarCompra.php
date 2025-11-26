<?php
session_start();
require_once('../php/database.php');

if (!isset($_SESSION['user'])) {
    header("Location: inicioSesion.php");
    exit;
}

$usuario_id = $_SESSION['user'];

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    header("Location: carrito.php");
    exit;
}

$totalCompra = 0;
foreach ($carrito as $producto) {
    $totalCompra += $producto['precio'] * $producto['cantidad'];
}

$envios = $pdo->query("SELECT * FROM envios")->fetchAll(PDO::FETCH_ASSOC);
$mediosPago = $pdo->query("SELECT * FROM medioPago")->fetchAll(PDO::FETCH_ASSOC);
$cuotas = $pdo->query("SELECT * FROM cuotas WHERE medioPago_id = 3")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['finalizar'])) {

    $envio_id = $_POST['envio_id'];
    $medioPago_id = $_POST['medioPago_id'];
    $cuota_id = $_POST['cuota_id'] ?: null;

    $subtotal = floatval($_POST['subtotal']);
    $costo_envio = floatval($_POST['costo_envio']);
    $interes_aplicado = floatval($_POST['interes_aplicado']);
    $total_final = floatval($_POST['total_final']);

    // Guardar en tabla compras
    $stmt = $pdo->prepare("INSERT INTO compras 
        (usuario_id, envio_id, medioPago_id, cuota_id, subtotal, envio_costo, interes_aplicado, total_final)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $usuario_id,
        $envio_id,
        $medioPago_id,
        $cuota_id,
        $subtotal,
        $costo_envio,
        $interes_aplicado,
        $total_final
    ]);

    $compra_id = $pdo->lastInsertId();

    // Guardar productos comprados
    foreach ($carrito as $id => $producto) {

        $stmt2 = $pdo->prepare("INSERT INTO productos_comprados
            (compra_id, producto_id, talle, cantidad, precio_unitario, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt2->execute([
            $compra_id,
            $id,
            $producto['talle'],
            $producto['cantidad'],
            $producto['precio'],
            $producto['precio'] * $producto['cantidad']
        ]);

        // Descontar stock
        //$stmt3 = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        //$stmt3->execute([$producto['cantidad'], $id]);

        // Descontar stock por talle
$stmtCheck = $pdo->prepare("
    SELECT stock FROM productos_talles
    WHERE producto_id = ? AND talle = ?
");
$stmtCheck->execute([$id, $producto['talle']]);
$stockActual = $stmtCheck->fetchColumn();

if ($stockActual === false) {
    die("Error: El talle seleccionado no existe para este producto.");
}

if ($stockActual < $producto['cantidad']) {
    die("Error: No hay stock suficiente del talle " . $producto['talle']);
}

$stmtUpdate = $pdo->prepare("
    UPDATE productos_talles
    SET stock = stock - ?
    WHERE producto_id = ? AND talle = ?
");
$stmtUpdate->execute([
    $producto['cantidad'],
    $id,
    $producto['talle']
]);

    }

    unset($_SESSION['carrito']);

    header("Location: confirmacionCompra.php?id=$compra_id");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Compra</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="finalizar-compra-container">

    <h1>Finalizar Compra</h1>

    <h3>Subtotal productos:</h3>
    <p id="total-base" data-total="<?= $totalCompra ?>">
        $<?= number_format($totalCompra, 2, ',', '.') ?>
    </p>

    <form method="post" id="formCompra">

        <!-- ENVÍO -->
        <h3>Tipo de envío</h3>

        <select name="envio_id" id="envio_id" required>
        <!--<select name="envio_id" id="envio_id" required onchange="actualizarEnvio()">-->
            <option value="">Seleccione una opción</option>
            <?php foreach ($envios as $envio): ?>
                <option value="<?= $envio['id'] ?>" data-costo="<?= $envio['costo'] ?>">
                    <?= $envio['destino'] ?> - $<?= $envio['costo'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p id="costo-envio">$0</p>


        <!-- MEDIO DE PAGO -->
        <h3>Medio de pago</h3>
        <select name="medioPago_id" id="medioPago_id" required>
        <!--<select name="medioPago_id" id="medioPago_id" required onchange="actualizarMedioPago()">-->
            <option value="">Seleccione una opción</option>
            <?php foreach ($mediosPago as $medio): ?>
                <option value="<?= $medio['id'] ?>"><?= $medio['nombre'] ?></option>
            <?php endforeach; ?>
        </select>

        <p id="mensaje-efectivo"></p>

        <!-- FORMULARIO TARJETA -->
        <div id="form-tarjeta" style="display:none;">
            <h3>Datos de tarjeta</h3>

            <label>Nombre completo:</label>
            <input type="text" name="nombre_titular" id="nombre_titular">

            <label>DNI:</label>
            <input type="text" name="dni_titular" id="dni_titular">

            <label>Email:</label>
            <input type="text" name="email_titular" id="email_titular">

            <label>Teléfono:</label>
            <input type="text" name="tel_titular" id="tel_titular">

            <label>Número de tarjeta:</label>
            <input type="text" name="numero_tarjeta" id="numero_tarjeta">

            <label>Expiración (MM/AAAA):</label>
            <input type="text" name="fecha_expiracion" id="fecha_expiracion" placeholder="MM/AAAA">

            <label>CVV:</label>
            <input type="text" name="cvv" id="cvv">
        </div>

        <!-- CUOTAS -->
        <div id="cuotas-container" style="display:none;">
            <h3>Cuotas</h3>
            <select name="cuota_id" id="cuota_id">
            <!--<select name="cuota_id" id="cuota_id" onchange="actualizarCuotas()">-->
                <option value="">Seleccione una opción</option>
                <?php foreach ($cuotas as $cuota): ?>
                    <option value="<?= $cuota['id'] ?>" data-interes="<?= $cuota['interes'] ?>">
                        <?= $cuota['descripcion'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p id="interes-info">Interés: 0%</p>
        </div>


        <h3>Total a pagar</h3>
        <p id="total-pagar">$<?= number_format($totalCompra, 2, ',', '.') ?></p>
        
        <a href="carrito.php" class="btn-volver">Volver al carrito</a>
        
        <button type="submit" id="btn-finalizar" name="finalizar">Finalizar Compra</button>

        <input type="hidden" name="subtotal" id="subtotal" value="<?= $totalCompra ?>">
        <input type="hidden" name="costo_envio" id="costo_envio" value="0">
        <input type="hidden" name="interes_aplicado" id="interes_aplicado" value="0">
        <input type="hidden" name="total_final" id="total_final" value="<?= $totalCompra ?>">

    </form>
</div>

<script src="../js/script.js"></script>

</body>
</html>
