<?php
session_start();
require 'database.php';

$mensaje = "";
$nuevo_codigo_mostrado = null; // variable para mostrar el nuevo código

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoIngresado = trim($_POST['codigo']);
    $email = $_SESSION['email_recuperacion'] ?? null;

    if (!$email) {
        header("Location: recuperarContraseña.php");
        exit();
    }

    // Obtener código actual guardado
    $stmt = $pdo->prepare("SELECT codigo_recuperacion FROM registro WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['codigo_recuperacion'] == $codigoIngresado) {
        // Código correcto → dejar pasar al formulario final
        header("Location: cambiarContraseña.php");
        exit();
    } else {
        // CÓDIGO INCORRECTO → GENERAR UNO NUEVO
        $error = "Código incorrecto. Se envió un nuevo código.";

        $nuevoCodigo = rand(100000, 999999); // nuevo código

        // Guardarlo en la BD
        $stmt = $pdo->prepare("UPDATE registro SET codigo_recuperacion = :codigo WHERE email = :email");
        $stmt->bindParam(':codigo', $nuevoCodigo);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Mostrarlo en pantalla
        $nuevo_codigo_mostrado = $nuevoCodigo;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar código</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="FormCaja">
    <h2>Verificar código</h2>

    <?php if (isset($error)) : ?>
        <div class="ErrorMensaje"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($nuevo_codigo_mostrado) : ?>
        <p><strong>Nuevo código enviado:</strong> <?= $nuevo_codigo_mostrado ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Ingresa el código:</label>
        <input type="text" name="codigo" required>
        <button type="submit">Verificar</button>
    </form>

    <a href="../index.php">Volver al inicio</a>
</div>
</body>
</html>
