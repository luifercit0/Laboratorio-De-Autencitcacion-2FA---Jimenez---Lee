<?php
// logout.php
session_start();
session_unset();
session_destroy(); // Destruye todos los datos registrados de la sesión
header("Location: login.php");
exit;
?>