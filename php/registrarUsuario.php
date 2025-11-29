<?php
session_start();
require_once 'database.php';

$mensaje = "";  // mensaje que se mostrará en el formulario
$tipoMensaje = ""; // success | error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $dni = $_POST['dni']; 
    $telefono = $_POST['telefono'];

    if ($pdo) { 
        try {

            // 1. Verificar si el correo ya existe
            $sqlCheck = "SELECT id FROM registro WHERE email = ?";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute([$email]);

            if ($stmtCheck->rowCount() > 0) {

                // Correo duplicado → mensaje de error
                $mensaje = "Este correo ya está registrado. Por favor, usa otro.";
                $tipoMensaje = "error";

            } else {

                // 2. Encriptar contraseña
                $contraseña_encriptada = password_hash($contraseña, PASSWORD_BCRYPT);

                // 3. Insertar usuario
                $sql = "INSERT INTO registro 
                (nombre, apellido, email, contraseña, dni, telefono)
                VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);

                if ($stmt->execute([$nombre, $apellido, $email, $contraseña_encriptada, $dni, $telefono])) {

                    $_SESSION['user'] = $pdo->lastInsertId();

                    $mensaje = "Usuario registrado exitosamente. Serás redirigido en 3 segundos...";
                    $tipoMensaje = "success";

                    // Redirección después de 3 segundos
                    echo "
                    <meta http-equiv='refresh' content='3;url=../index.php'>
                    ";
                
                } else {
                    $mensaje = "Error al registrar al usuario.";
                    $tipoMensaje = "error";
                }
            }

        } catch (PDOException $e) {
            $mensaje = "Error inesperado. Intenta nuevamente.";
            $tipoMensaje = "error";
        }
    } else {
        $mensaje = "Error al conectar con la base de datos.";
        $tipoMensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
<div class="Registro">
    <h2>Registrarse</h2>

    <!-- Mostrar mensaje dinámico -->
    <?php if (!empty($mensaje)) : ?>
        <div class="mensaje <?= $tipoMensaje == 'success' ? 'mensaje-exito' : 'mensaje-error'; ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form action="registrarUsuario.php" method="POST">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required><br>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" required><br>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required><br>

        <label for="contraseña">Contraseña:</label>
        <input type="password" name="contraseña" required><br>

        <label for="dni">DNI:</label>
        <input type="text" name="dni" required><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" required>

        <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="inicioSesion.php">Inicia sesión aquí</a></p>
    <p><a href="../index.php">Volver a la página de inicio</a></p>

</div>
</body>
</html>
