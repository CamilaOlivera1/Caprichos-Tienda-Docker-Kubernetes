<?php
session_start(); 
// Inicia la sesión para poder acceder y luego borrar sus datos
session_unset(); 
// Elimina todas las variables de sesión activas (limpia $_SESSION)
session_destroy(); 
// Destruye completamente la sesión en el servidor
header("Location: ../index.php");  
// Redirige al usuario al inicio después de cerrar sesión
exit();
// Finaliza el script para evitar que siga ejecutándose
?>
