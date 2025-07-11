<?php
session_start();

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    echo json_encode($_SESSION['carrito']);
} else {
    echo json_encode([]); // Devuelve un array vacío si no hay productos
}
exit;
?>
