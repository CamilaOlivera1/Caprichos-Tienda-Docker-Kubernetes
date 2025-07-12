<?php
session_start();

if (!isset($_POST['producto_id'])) {
    header("Location: ../index.php");
    exit;
}

$producto_id = (int) $_POST['producto_id'];

// Eliminar el producto del carrito
if (isset($_SESSION['carrito'][$producto_id])) {
    unset($_SESSION['carrito'][$producto_id]);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
