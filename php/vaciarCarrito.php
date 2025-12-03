<?php
// Inicia la sesión para acceder al carrito del usuario
session_start();

// Verifica que se haya enviado el ID del producto por POST
if (!isset($_POST['producto_id'])) {
    // Si no se recibe el ID, redirige al inicio
    header("Location: ../index.php");
    exit;
}

// Convierte el ID recibido a entero para mayor seguridad
$producto_id = (int) $_POST['producto_id'];

// Eliminar el producto del carrito
if (isset($_SESSION['carrito'][$producto_id])) {
    unset($_SESSION['carrito'][$producto_id]);
}

// Redirige a la página desde donde se envió la solicitud
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
