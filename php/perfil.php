<?php
session_start();
require 'database.php';

// Verificar si el usuario está logueado
// Si no hay usuario en sesión, redirige al login
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

// Traer compras del usuario
$sql = "
    SELECT id, fecha, total_final, medioPago_id 
    FROM compras 
    WHERE usuario_id = ? 
    ORDER BY fecha DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Sección de datos personales del usuario -->
    <div class="perfil-datos">
        <h2>Perfil de Usuario</h2>

        <p><strong>Nombre:</strong> <?= htmlspecialchars($user['nombre']) ?></p>
        <p><strong>Apellido:</strong> <?= htmlspecialchars($user['apellido']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>DNI:</strong> <?= htmlspecialchars($user['dni']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($user['telefono']) ?></p>
        <p><strong>Fecha de Registro:</strong> <?= htmlspecialchars($user['fecha_registro']) ?></p>

        <p><a href="editarPerfil.php">Editar mi perfil</a></p>
        <p><a href="../index.php">Volver al inicio</a></p>
    </div>

    <!-- Sección de compras del usuario -->
    <div class="perfil-compras">
        <h2>Mis compras</h2>

        <?php if (count($compras) === 0): ?>
            <!-- Mensaje si no hay compras -->
            <p>Aún no realizaste compras.</p>
        <?php else: ?>
            <!-- Tabla con las compras realizadas -->
            <table class="tabla-compras">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compras as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['id']) ?></td>
                            <td><?= htmlspecialchars($c['fecha']) ?></td>
                            <td>$<?= number_format($c['total_final'], 2, ',', '.') ?></td>
                            <td>
                                <!-- Link para ver detalle de cada compra -->
                                <a href="../php/confirmacionCompra.php?id=<?= urlencode($c['id']) ?>" class="btn-ver-detalle">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

</div>
