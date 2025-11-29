<?php
session_start();
require_once('database.php');
require_once('fpdf/fpdf.php');

if (!isset($_SESSION['user'])) {
    die("Acceso denegado.");
}

$usuario_id = $_SESSION['user'];

// Validación del ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido");
}

$compra_id = intval($_GET['id']);

function money($n) {
    return '$' . number_format($n, 2, ',', '.');
}

/* 1. TRAER DATOS DE LA COMPRA */
$sql = "
    SELECT c.*, 
           e.destino AS envio_destino, e.costo AS envio_costo,
           m.nombre AS medio_nombre,
           q.descripcion AS cuota_descripcion, q.cantidad AS cuota_cantidad
    FROM compras c
    LEFT JOIN envios e ON c.envio_id = e.id
    LEFT JOIN medioPago m ON c.medioPago_id = m.id
    LEFT JOIN cuotas q ON c.cuota_id = q.id
    WHERE c.id = ? AND c.usuario_id = ?
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$compra_id, $usuario_id]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    die("Compra no encontrada.");
}

/* 2. TRAER PRODUCTOS */
$stmtP = $pdo->prepare("
    SELECT pc.*, p.nombre AS producto_nombre
    FROM productos_comprados pc
    LEFT JOIN productos p ON pc.producto_id = p.id
    WHERE pc.compra_id = ?
");
$stmtP->execute([$compra_id]);
$productos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

/* 3. TRAER DATOS DEL USUARIO */
$stmtU = $pdo->prepare("SELECT nombre, apellido, email FROM registro WHERE id = ?");
$stmtU->execute([$usuario_id]);
$usuario = $stmtU->fetch(PDO::FETCH_ASSOC);


/* 4. ARMAR PDF */
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

/*** HEADER ***/
$pdf->Cell(0,10, utf8_decode('Comprobante de Compra - Caprichos'), 0,1,'L');
$pdf->Image('../imagenes/logo caprichos.jpg', 160, 10, 35);

/*** INFORMACIÓN GENERAL ***/
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,8, utf8_decode("Número de compra: ") . $compra['id'],0,1);
$pdf->Cell(0,8, utf8_decode("Fecha: ") . utf8_decode($compra['fecha']),0,1);

$pdf->Ln(4);
$pdf->Cell(0,8, utf8_decode("Cliente: ") . utf8_decode($usuario['nombre'] . ' ' . $usuario['apellido']),0,1);
$pdf->Cell(0,8, utf8_decode("Email: ") . utf8_decode($usuario['email']),0,1);

$pdf->Ln(10);

/*** PRODUCTOS ***/
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10, utf8_decode('Productos comprados'),0,1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,8, utf8_decode('Producto'),1);
$pdf->Cell(20,8, utf8_decode('Talle'),1);
$pdf->Cell(20,8, utf8_decode('Cant.'),1);
$pdf->Cell(35,8, utf8_decode('Precio'),1);
$pdf->Cell(35,8, utf8_decode('Subtotal'),1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);

foreach ($productos as $p) {
    $pdf->Cell(60,8, utf8_decode($p['producto_nombre']),1);
    $pdf->Cell(20,8, utf8_decode($p['talle']),1);
    $pdf->Cell(20,8, $p['cantidad'],1);
    $pdf->Cell(35,8, money($p['precio_unitario']),1);
    $pdf->Cell(35,8, money($p['subtotal']),1);
    $pdf->Ln();
}

$pdf->Ln(10);

/*** RESUMEN DE PAGO ***/
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10, utf8_decode('Resumen de pago'),0,1);

$pdf->SetFont('Arial','',12);

$pdf->Cell(80,8, utf8_decode('Medio de pago:'),0,0);
$pdf->Cell(50,8, utf8_decode($compra['medio_nombre']),0,1);

$pdf->Cell(80,8, utf8_decode('Cuotas:'),0,0);
$pdf->Cell(50,8, utf8_decode($compra['cuota_descripcion'] ?: '-'),0,1);

$pdf->Cell(80,8, utf8_decode('Subtotal productos:'),0,0);
$pdf->Cell(50,8, money($compra['subtotal']),0,1);

$pdf->Cell(80,8, utf8_decode('Costo del envío (' . $compra['envio_destino'] . '):'),0,0);
$pdf->Cell(50,8, money($compra['envio_costo']),0,1);

$pdf->Cell(80,8, utf8_decode('Interés aplicado:'),0,0);
$pdf->Cell(50,8, utf8_decode($compra['interes_aplicado'] . ' %'),0,1);

$pdf->Cell(80,8, utf8_decode('Total final:'),0,0);
$pdf->Cell(50,8, money($compra['total_final']),0,1);

$pdf->Ln(15);

/*** MENSAJE FINAL ***/
$pdf->SetFont('Arial','I',11);
$pdf->MultiCell(0,7, utf8_decode(
    "Tu compra se ha procesado correctamente.\n".
    "¡Gracias por elegir Caprichos!\n".
    "Esperamos que disfrutes tus prendas con toda emoción."
));

$pdf->Output('I', 'Comprobante_Compra_'.$compra_id.'.pdf');
?>
