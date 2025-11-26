<?php
session_start();
unset($_SESSION['email_recuperacion']);
require 'database.php';

$mensaje = "";
$mostrarFormularioCodigo = false;

// Si se envió el correo:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_correo'])) {
    
    $email = trim($_POST['email']);

    // Buscar usuario por email
    $stmt = $pdo->prepare("SELECT * FROM registro WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // Generar código y guardarlo
        $codigo = rand(100000, 999999);

        $stmt = $pdo->prepare("UPDATE registro 
                               SET codigo_recuperacion = :codigo 
                               WHERE email = :email");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Guardar email en sesión para usar en los próximos pasos
        $_SESSION['email_recuperacion'] = $email;

        $mensaje = "Tu código de recuperación es: <b>$codigo</b>";

        // Activar formulario del código
        $mostrarFormularioCodigo = true;

    } else {
        $mensaje = "<span style='color:red;'>No existe una cuenta con ese correo.</span>";
    }
}

// Si ya se generó un código antes (tras enviar correo) mostramos el form del código:
if (isset($_SESSION['email_recuperacion']) && !isset($_POST['enviar_correo'])) {
    $mostrarFormularioCodigo = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Recuperar contraseña</title>
</head>
<body>

<!--<div class="InicioSesion">-->
<div class="FormCaja">
    <h2>Recuperar contraseña</h2>

    <?php if ($mensaje) echo "<p>$mensaje</p>"; ?>

    <?php if (!$mostrarFormularioCodigo): ?>
        <!-- *** FORMULARIO 1: INGRESAR CORREO *** -->
        <form action="recuperarContrasena.php" method="POST">
            <label>Ingresa tu correo:</label>
            <input type="email" name="email" required>
            <button type="submit" name="enviar_correo">Enviar código</button>
        </form>

    <?php else: ?>
        <!-- *** FORMULARIO 2: INGRESAR CÓDIGO *** -->
        <form action="verificarCodigo.php" method="POST">
            <label>Ingresa el código:</label>
            <input type="text" name="codigo" required minlength="6" maxlength="6">
            <button type="submit">Verificar código</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="../index.php">Volver al inicio</a>

</div>

</body>
</html>
