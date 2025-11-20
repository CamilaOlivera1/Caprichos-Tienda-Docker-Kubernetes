<?php
session_start();
require_once('../php/database.php'); // Ajusta la ruta si hace falta

// Verificar sesión
if (!isset($_SESSION['user'])) {
    header("Location: inicioSesion.php");
    exit;
}

$usuario_id = $_SESSION['user'];

// Verificar que venga el id de compra
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de compra no válido.");
}
$compra_id = intval($_GET['id']);

// Traer la compra y datos relacionados (envío, medio de pago, cuota)
$sqlCompra = "
    SELECT c.*, 
           e.destino AS envio_destino, e.costo AS envio_costo,
           m.nombre AS medio_nombre,
           q.descripcion AS cuota_descripcion,
           q.cantidad AS cuota_cantidad
    FROM compras c
    LEFT JOIN envios e ON c.envio_id = e.id
    LEFT JOIN medioPago m ON c.medioPago_id = m.id
    LEFT JOIN cuotas q ON c.cuota_id = q.id
    WHERE c.id = ? AND c.usuario_id = ?
    LIMIT 1
";
$stmt = $pdo->prepare($sqlCompra);
$stmt->execute([$compra_id, $usuario_id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    die("Compra no encontrada o no autorizada.");
}

// Traer productos comprados para esa compra
$sqlProductos = "
    SELECT pc.*, p.nombre AS producto_nombre
    FROM productos_comprados pc
    LEFT JOIN productos p ON pc.producto_id = p.id
    WHERE pc.compra_id = ?
";
$stmt2 = $pdo->prepare($sqlProductos);
$stmt2->execute([$compra_id]);
$productos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Traer email / nombre del usuario (opcional)
$sqlUser = "SELECT nombre, apellido, email FROM registro WHERE id = ?";
$stmtU = $pdo->prepare($sqlUser);
$stmtU->execute([$usuario_id]);
$usuario = $stmtU->fetch(PDO::FETCH_ASSOC);

// Formateos
function money($n) {
    return '$' . number_format($n, 2, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Confirmación de compra #<?= htmlspecialchars($compra['id']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<div class="confirmacion">

    <div class="encabezado-confirmacion">
        <h1>¡Gracias por tu compra!</h1>

        <img src="../imagenes/logo caprichos.jpg" alt="Logo Caprichos" class="logo-confirmacion">

    </div>

    <div class="mensaje-exito-simple">
    <p><strong>Tu compra se ha procesado correctamente.</strong></p>
    <p>Esperamos que lo disfrutes y luzcas tus prendas con toda emoción❤️</p>
</div>


    <p class="meta">
        Número de compra: <strong><?= htmlspecialchars($compra['id']) ?></strong> &nbsp;|&nbsp;
        Fecha: <strong><?= htmlspecialchars($compra['fecha']) ?></strong>
    </p>

    <?php if ($usuario): ?>
        <p class="meta">Usuario: <strong><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></strong> — Email: <strong><?= htmlspecialchars($usuario['email']) ?></strong></p>
    <?php endif; ?>

    <h2>Productos comprados</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($productos) === 0): ?>
                <tr><td colspan="4">No se encontraron productos para esta compra.</td></tr>
            <?php else: ?>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['producto_nombre'] ?? "Producto #{$p['producto_id']}") ?></td>
                        <td><?= intval($p['cantidad']) ?></td>
                        <td><?= money($p['precio_unitario']) ?></td>
                        <td><?= money($p['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Resumen de pago</h2>
    <table>
        <tbody>
            <tr>
                <th>Medio de pago</th>
                <td><?= htmlspecialchars($compra['medio_nombre'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <th>Cuotas</th>
                <td>
                    <?php
                        if (!empty($compra['cuota_id']) && !empty($compra['cuota_descripcion'])) {
                            echo htmlspecialchars($compra['cuota_descripcion']) . " ({$compra['cuota_cantidad']} cuotas)";
                        } else {
                            echo '-';
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Subtotal productos</th>
                <td><?= money($compra['subtotal']) ?></td>
            </tr>
            <tr>
                <th>Costo envío (<?= htmlspecialchars($compra['envio_destino'] ?? 'N/A') ?>)</th>
                <td><?= money($compra['envio_costo']) ?></td>
            </tr>
            <tr>
                <th>Interés aplicado</th>
                <td><?= number_format($compra['interes_aplicado'], 2, ',', '.') ?> %</td>
            </tr>
            <tr>
                <th>Total pagado</th>
                <td><strong style="font-size:1.05rem;"><?= money($compra['total_final']) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla "Agregados" (si quieres agrupar extras u otros datos) -->
    <h2>Detalles adicionales</h2>
    <table>
        <thead>
            <tr>
                <th>Campo</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ID compra</td>
                <td><?= htmlspecialchars($compra['id']) ?></td>
            </tr>
            <tr>
                <td>Usuario ID</td>
                <td><?= htmlspecialchars($compra['usuario_id']) ?></td>
            </tr>
            <tr>
                <td>Envío ID</td>
                <td><?= htmlspecialchars($compra['envio_id']) ?></td>
            </tr>
            <tr>
                <td>MedioPago ID</td>
                <td><?= htmlspecialchars($compra['medioPago_id']) ?></td>
            </tr>
            <tr>
                <td>Fecha</td>
                <td><?= htmlspecialchars($compra['fecha']) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="btns">
        <a class="btn" href="../index.php">Volver al inicio</a>
        <a class="btn" href="factura.php?compra_id=<?= urlencode($compra['id']) ?>" style="background:#2b6cb0">Descargar comprobante (PDF)</a>
        <!-- Nota: factura.php es opcional si deseas generar PDF -->
    </div>
</div>
</body>
</html>
