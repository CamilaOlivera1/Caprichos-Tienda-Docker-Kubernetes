<?php
session_start(); 
// Incluir la conexión a la base de datos
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $dni = $_POST['DNI'];
    $telefono = $_POST['telefono'];

    // Validar que los campos no excedan el tamaño máximo de las columnas
    if (strlen($email) > 255) {
        echo "El correo electrónico no puede tener más de 255 caracteres.";
        exit;
    }
    if (strlen($contraseña) > 20) {
        echo "La contraseña no puede tener más de 20 caracteres.";
        exit;
    }

    // Encriptar la contraseña
    $contraseña_encriptada = password_hash($contraseña, PASSWORD_BCRYPT);

    // Verificar si la conexión a la base de datos fue exitosa
    if ($pdo) { 
        try {
            // Preparar la consulta SQL para insertar el nuevo usuario
            $sql = "INSERT INTO registro (nombre, apellido, email, contraseña, dni, telefono) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

// Ejecutar la consulta
            if ($stmt->execute([$nombre, $apellido, $email, $contraseña_encriptada, $dni, $telefono])) {
                // Iniciar sesión automáticamente
                $_SESSION['user'] = $pdo->lastInsertId();

                // Mostrar mensaje y redirigir sin usar header()
                echo '
                <html>
                <head>
                    <meta http-equiv="refresh" content="3;url=../index.php" />
                </head>
                <body>
                    <p>Usuario registrado exitosamente. Serás redirigido en 3 segundos...</p>
                </body>
                </html>';
                exit;


            } else {
                echo "Error al registrar al usuario.";
            }
        } catch (PDOException $e) {
            // Mostrar error si ocurre una excepción
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error al conectar con la base de datos.";
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

        <label for="telfono">Telefono:</label>
        <input type="text" name="telefono" required><br>

        <button type="submit">Registrar</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="inicioSesion.php">Inicia sesión aquí</a></p>
    <p><a href="../index.php">Volver a la página de inicio</a></p>

    </div>
</body>
</html>

