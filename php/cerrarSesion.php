/*
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: login.php");
exit();*/

<?php
session_start();
session_unset();
session_destroy();
header("Location: ../index.php");  // Asegúrate de que esta ruta sea correcta
exit();
?>
