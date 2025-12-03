<?php
session_start(); // Inicia la sesión del usuario para poder almacenar información del login
require 'database.php'; // Incluye el archivo de conexión a la base de datos (PDO)

// Verifica si el formulario fue enviado mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos ingresados por el usuario
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Verificar si la conexión PDO está funcionando
        if (!$pdo) {
            throw new Exception("Error en la conexión a la base de datos.");
        }

        // Preparar consulta para buscar el email
        $stmt = $pdo->prepare("SELECT * FROM registro WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Obtiene los datos del usuario
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe y si la contraseña es correcta
        // password_verify compara la contraseña ingresada con la que está en la base de datos (hash)
        if ($user && password_verify($password, $user['contraseña'])) { 
            $_SESSION['user'] = $user['id']; // Guarda el ID del usuario
            header("Location: ../index.php");
            exit();
        }
     else {
            $error = "Usuario o contraseña incorrectos.";// Mensaje de error si falla login
        }
    } catch (PDOException $e) {
        $error = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <div class="InicioSesion">
    <h2>Iniciar Sesión</h2>

    <!--MUESTRA ERRORES DE LOGIN SI EXISTEN-->
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <!-- FORMULARIO DE INICO DE SESION-->
    <form action="inicioSesion.php" method="POST">
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <!-- ENLACES DE AYUDA Y REGISTRO-->
    <p><a href="recuperarContrasena.php">¿Olvidaste tu contraseña?</a></p>
    <p>¿No tienes cuenta? <a href="registrarUsuario.php">Regístrate aquí</a></p>
    <p><a href="../index.php">Volver a la página de inicio</a></p>

    </div>
</body>
</html>

