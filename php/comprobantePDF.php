<?php
session_start();
require 'fpdf/fpdf.php';

// Verificar datos recibidos
if (!isset($_GET['monto']) || !isset($_GET['idCompra'])) {
    die("Faltan datos para generar el comprobante.");
}

$monto = $_GET['monto'];
$idCompra = $_GET['idCompra'];

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logo de Caprichos
$pdf->Image('../imagenes/logo caprichos.jpg', 150, 10, 40);

// Título
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 20, utf8_decode('Comprobante de Compra - Caprichos'), 0, 1, 'C');

$pdf->Ln(10);

// Datos principales
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode("Número de compra: $idCompra"), 0, 1);
$pdf->Cell(0, 10, utf8_decode("Monto total: $$monto"), 0, 1);

$pdf->Ln(5);

$pdf->MultiCell(0, 8, utf8_decode(
    "Tu compra se ha procesado correctamente.\n".
    "¡Muchas gracias por elegir Caprichos!\n".
    "Esperamos que disfrutes tus prendas con toda emoción."
));

$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, utf8_decode('Este comprobante fue generado automáticamente.'), 0, 1, 'C');

// Descargar PDF
$pdf->Output('I', 'comprobante_compra.pdf');
