<?php
session_start();
require 'database.php';

$mensaje = "";

// Si no viene del código verificado, lo sacamos
if (!isset($_SESSION['email_recuperacion'])) {
    header("Location: recuperarContraseña.php");
    exit();
}

$email = $_SESSION['email_recuperacion'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = trim($_POST['pass1']);
    $pass2 = trim($_POST['pass2']);

    if ($pass1 !== $pass2) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Encriptamos la nueva contraseña
        $hash = password_hash($pass1, PASSWORD_DEFAULT);

        // Actualizamos en BD
        $stmt = $pdo->prepare("UPDATE registro 
                               SET contraseña = :pass, codigo_recuperacion = NULL 
                               WHERE email = :email");

        $stmt->bindParam(':pass', $hash);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Obtener ID del usuario para iniciar sesión automáticamente
        $stmt = $pdo->prepare("SELECT id FROM registro WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Iniciar sesión automáticamente
        if ($usuario) {
            $_SESSION['user'] = $usuario['id']; 
        }

        // Limpiar email temporal
        unset($_SESSION['email_recuperacion']);

        // Mensaje + redirección automática
        $mensaje = "<span style='color:green;'>¡Contraseña cambiada correctamente!<br>
                    Serás redirigido al inicio en 5 segundos...</span>";

        echo "
        <script>
            setTimeout(function(){
                window.location.href = '../index.php';
            }, 5000);
        </script>
        ";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<!--<div class="InicioSesion">-->
    <div class="FormCaja">
    <h2>Cambiar contraseña</h2>

    <?php if ($mensaje) echo "<p>$mensaje</p>"; ?>

    <form action="cambiarContraseña.php" method="POST">
        <label>Nueva contraseña:</label>
        <input type="password" name="pass1" required>

        <label>Repetir contraseña:</label>
        <input type="password" name="pass2" required>

        <button type="submit">Guardar nueva contraseña</button>
    </form>

    <br>
    <a href="../index.php">Volver al inicio</a>
</div>
</body>
</html>
