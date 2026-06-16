<?php
session_start();
require_once 'clases/AntiCSRF.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
    <h1>Inicio de Sesión</h1>
    <p>Ingresa al sistema de forma segura</p>
    
    <form action="verificar_login.php" method="POST">
        <?php echo AntiCSRF::campoHidden(); ?>
        <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Ingresar</button>
    </form>
    
    <div class="form-footer">
        ¿No tienes cuenta? <a href="Registrese_form.php">Regístrate aquí</a>
    </div>
</div>

</body>
</html>
