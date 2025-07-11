<?php
$host = "localhost";
$dbname = "ProyectoFinalCaprichos"; // Reemplaza con el nombre real
$user = "root"; // Usuario de MySQL
$password = ""; // Contraseña de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
