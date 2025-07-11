<?php
session_start();
require 'database.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe y si la contraseña es correcta

        if ($user && password_verify($password, $user['contraseña'])) { 
            $_SESSION['user'] = $user['id']; // Guarda el ID del usuario
            header("Location: ../index.php");
            exit();
        }
     else {
            $error = "Usuario o contraseña incorrectos.";
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
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form action="inicioSesion.php" method="POST">
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Iniciar Sesión</button>
    </form>
    <p>¿No tienes cuenta? <a href="registrarUsuario.php">Regístrate aquí</a></p>
    </div>
</body>
</html>

