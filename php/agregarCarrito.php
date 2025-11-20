<?php
session_start();
require_once 'database.php';

// Verificar datos del formulario
if (!isset($_POST['producto_id']) || !isset($_POST['cantidad']) || !is_numeric($_POST['cantidad'])) {
    header("Location: ../index.php");
    exit;
}

$producto_id = (int) $_POST['producto_id'];
$cantidad = (int) $_POST['cantidad'];
$categoria = isset($_POST['categoria']) ? $_POST['categoria']: null; //recibe la categoria

// Consultar el producto en la base de datos
$query = "SELECT * FROM productos WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $producto_id, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($producto) {
    // Verificar si hay suficiente stock
    if ($producto['stock'] >= $cantidad) {
        // Inicializar el carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Guardar también la categoría actual en la sesión (para el botón "Seguir comprando")
        if ($categoria) {
            $_SESSION['ultima_categoria'] = $categoria;
        }

        // Si el producto ya está en el carrito, aumentar la cantidad
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
        } else {
            // Agregar un nuevo producto al carrito
            $_SESSION['carrito'][$producto_id] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad,
                'imagen' => $producto['imagen']
            ];
        }
        header("Location: carrito.php"); // Redirigir al carrito
        exit;
    } else {
        echo "No hay suficiente stock disponible.";
    }
} else {
    echo "El producto no existe.";
}

header("Location: ../index.php");
exit;
?>
