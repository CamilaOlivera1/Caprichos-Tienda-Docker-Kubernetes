<?php
$host = 'tienda-mysql';
$port = '3306';
$dbname = 'tienda_db';
$user = 'tienda_user';
$password = 'tienda_pass';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $password);
    echo '✅ Conexión exitosa a MySQL desde el contenedor PHP!';
} catch (PDOException $e) {
    echo '❌ Error de conexión: ' . $e->getMessage();
}
?>