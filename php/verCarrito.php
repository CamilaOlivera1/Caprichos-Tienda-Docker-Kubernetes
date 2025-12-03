<?php
// Inicia la sesión para poder acceder a los datos del carrito
session_start();

// Verifica si existe el carrito y si no está vacío
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    // Devuelve los productos del carrito en formato JSON
    echo json_encode($_SESSION['carrito']);
} else {
    // Si no hay productos, devuelve un array vacío
    echo json_encode([]);
}
exit;
?>
