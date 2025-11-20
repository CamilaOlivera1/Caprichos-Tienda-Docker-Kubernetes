<?php
session_start();
require 'database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header("Location: inicioSesion.php");
    exit();
}


// Obtener el email del usuario desde la sesión
$id = $_SESSION['user'];

// Preparar la consulta para obtener los datos del usuario
try {
    if (!$pdo) {
        throw new Exception("Error en la conexión a la base de datos.");
    }

    // Preparar la consulta para obtener los datos del usuario
$stmt = $pdo->prepare("SELECT * FROM registro WHERE id = :id");
$stmt->bindParam(':id', $id);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "No se encontró el usuario en la base de datos.";
    }
} catch (PDOException $e) {
    $error = "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <div class="Perfil">
    <h2>Perfil de Usuario</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
    <p><strong>Apellido:</strong> <?php echo htmlspecialchars($user['apellido']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>DNI:</strong><?php echo htmlspecialchars($user['dni'])?></p>
    <p><strong>Telefono:</strong><?php echo htmlspecialchars($user['telefono'])?></p>
    <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($user['fecha_registro']); ?></p>

    <p><a href="editarPerfil.php">Editar mi perfil</a></p>
    <p><a href="../index.php">Volver a la página de inicio</a></p>
    </div>
</body>
</html>