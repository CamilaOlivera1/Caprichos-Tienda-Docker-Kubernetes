<?php
session_start();

if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
    header("Location: ../index.php");
    exit;
}

$producto_id = (int) $_POST['producto_id'];
$cantidad = (int) $_POST['cantidad'];

// Verificar si el producto existe en el carrito
if (isset($_SESSION['carrito'][$producto_id])) {
    if ($cantidad > 0) {
        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
    } else {
        // Si la cantidad es 0 o negativa, eliminar el producto
        unset($_SESSION['carrito'][$producto_id]);
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
