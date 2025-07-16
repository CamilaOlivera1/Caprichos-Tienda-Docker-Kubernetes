<?php
$host = 'tienda-mysql';  // nombre del contenedor de MySQL (NO localhost)
$port = '3306';          // puerto interno de MySQL dentro del contenedor
$dbname = 'tienda_db';
$user = 'tienda_user';
$password = 'tienda_pass';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
