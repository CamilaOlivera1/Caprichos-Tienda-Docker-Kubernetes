<?php
session_start();
require_once 'database.php';

/* ---------------------------------------------------------
   1) VERIFICAR QUE EL USUARIO ESTÉ LOGUEADO
   Si no está logueado, NO puede agregar productos al carrito.
------------------------------------------------------------ */
if (!isset($_SESSION['user'])) {
    header("Location: ../html/inicioSesion.html");
    exit;
}

/* ---------------------------------------------------------
   2) VALIDAR QUE LLEGUE EL PRODUCTO, TALLE Y CANTIDAD
------------------------------------------------------------ */
if (
    !isset($_POST['producto_id']) ||
    !isset($_POST['talle']) ||
    !isset($_POST['cantidad']) ||
    !is_numeric($_POST['cantidad'])
) {
    header("Location: ../index.php");
    exit;
}

$producto_id = (int) $_POST['producto_id'];
$talle       = $_POST['talle'];
$cantidad    = (int) $_POST['cantidad'];
$categoria   = $_POST['categoria'] ?? null;

/* ---------------------------------------------------------
   3) CONSULTAR EL PRODUCTO PARA OBTENER NOMBRE, PRECIO, ETC.
------------------------------------------------------------ */
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->execute([':id' => $producto_id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "El producto no existe.";
    exit;
}

/* ---------------------------------------------------------
   4) CONSULTAR EL STOCK DEL TALLE SELECCIONADO
------------------------------------------------------------ */
$stmt = $pdo->prepare("
    SELECT stock 
    FROM productos_talles 
    WHERE producto_id = :id AND talle = :talle
");
$stmt->execute([
    ':id'    => $producto_id,
    ':talle' => $talle
]);
$talleData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$talleData) {
    echo "El talle seleccionado no existe.";
    exit;
}

/* ---------------------------------------------------------
   5) VERIFICAR STOCK POR TALLE
------------------------------------------------------------ */
/*if ($talleData['stock'] < $cantidad) {
    echo "No hay suficiente stock para el talle seleccionado.";
    exit;
}*/
if ($talleData['stock'] < $cantidad) {
    header("Location: detallesProducto.php?id=$producto_id&error=stock");
    exit;
}

/* ---------------------------------------------------------
   6) INICIALIZAR CARRITO SI NO EXISTE
------------------------------------------------------------ */
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

//7) GUARDAR CATEGORÍA PARA EL BOTÓN “Seguir comprando”
if ($categoria) {
    $_SESSION['ultima_categoria'] = $categoria;
}

//8) AGREGAR AL CARRITO → CLAVE ÚNICA: PRODUCTO + TALLE
$clave = $producto_id . "_" . $talle;

// Si ya existe ese producto con ese talle, aumentar la cantidad
if (isset($_SESSION['carrito'][$clave])) {

    $nuevaCantidad = $_SESSION['carrito'][$clave]['cantidad'] + $cantidad;

    // Validar stock nuevamente
    /*if ($nuevaCantidad > $talleData['stock']) {
        echo "No podés agregar más cantidad. No hay stock suficiente.";
        exit;
    }*/
    if ($nuevaCantidad > $talleData['stock']) {
    header("Location: detallesProducto.php?id=$producto_id&error=stocklimit");
    exit;
}


    $_SESSION['carrito'][$clave]['cantidad'] = $nuevaCantidad;

} else {
    // Si no existe en el carrito, agregarlo
    $_SESSION['carrito'][$clave] = [
        'producto_id' => $producto['id'],
        'nombre'      => $producto['nombre'],
        'precio'      => $producto['precio'],
        'imagen'      => $producto['imagen'],
        'talle'       => $talle,
        'cantidad'    => $cantidad
    ];
}

//9) REDIRIGIR AL CARRITO
header("Location: carrito.php");
exit;
?>
