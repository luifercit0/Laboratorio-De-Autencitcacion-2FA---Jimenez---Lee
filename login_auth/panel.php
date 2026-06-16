<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - Sistema Seguro</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="panel-container">
    <h1>¡Bienvenido al Sistema!</h1>
    <h2>Hola, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?> (<?php echo htmlspecialchars($_SESSION['user']); ?>)</h2>
    <p>Has iniciado sesión correctamente usando autenticación de primer factor robusta.</p>
    
    <div style="margin: 30px 0;">
        <a href="configurar_2fa.php" class="btn">Configurar / Verificar Estado de 2FA</a>
    </div>
    
    <hr>

    <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
</div>

</body>
</html>