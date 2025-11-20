<?php
session_start();
require 'database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header("Location: inicioSesion.php");
    exit();
}

// Obtener el ID del usuario desde la sesión
$id = $_SESSION['user'];

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $nuevaEmail = $_POST['email'];
    $nuevoDNI = $_POST['DNI'];
    $telefono = $_POST['telefono'];

    try {
        // Actualizar los datos del usuario en la base de datos
        $stmt = $pdo->prepare("UPDATE registro SET nombre = :nombre, apellido = :apellido, email = :email, DNI = :DNI, telefono = :telefono WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':email', $nuevaEmail);
        $stmt->bindParam(':DNI', $nuevoDNI);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            // Redirigir a la página de inicio
            header("Location: ../index.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error al actualizar el perfil: " . $e->getMessage();
    }
}

// Obtener los datos actuales del usuario
$stmt = $pdo->prepare("SELECT * FROM registro WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
    <div class="PerfilEditar">
    <h2>Editar Perfil</h2>

    <!-- Formulario para actualizar los datos del perfil -->
    <form method="POST" action="editarPerfil.php">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required><br>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($user['apellido']); ?>" required><br>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <label for="DNI"> DNI:</label>
        <input type="text" name="DNI" value="<?php echo htmlspecialchars($user['dni']); ?>" required><br>

        <label for="telefono">Telefono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>" required><br>

        <button type="submit">Guardar Cambios</button>
    </form>

    <!-- Enlace para volver al perfil -->
    <p><a href="perfil.php">Volver a mi perfil</a></p>
    </div>
</body>
</html>
