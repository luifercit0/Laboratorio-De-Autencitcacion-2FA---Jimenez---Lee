<?php
session_start();
require_once 'clases/AntiCSRF.php';

$errores = $_SESSION['errores_registro'] ?? [];
$datos   = $_SESSION['datos_registro'] ?? [];

unset($_SESSION['errores_registro'], $_SESSION['datos_registro']);

function val($datos, $campo) {
    return htmlspecialchars($datos[$campo] ?? '', ENT_QUOTES, 'UTF-8');
}
function err($errores, $campo) {
    if (!empty($errores[$campo])) {
        echo '<span class="error-campo">' . htmlspecialchars($errores[$campo], ENT_QUOTES, 'UTF-8') . '</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro - Laboratorio #1</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .error-campo { color: #e53e3e; font-size: 13px; display: block; margin-top: 4px; }
        input.campo-error, select.campo-error { border: 1px solid #e53e3e; }
    </style>
</head>
<body>

<div class="container">
    <h1>Registro de Usuario</h1>
    <p>Crea una cuenta nueva completando los campos</p>

    <form action="procesar_registro.php" method="POST">
        <?php echo AntiCSRF::campoHidden(); ?>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo val($datos, 'nombre'); ?>" class="<?php echo !empty($errores['nombre']) ? 'campo-error' : ''; ?>" required>
            <?php err($errores, 'nombre'); ?>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" value="<?php echo val($datos, 'apellido'); ?>" class="<?php echo !empty($errores['apellido']) ? 'campo-error' : ''; ?>" required>
            <?php err($errores, 'apellido'); ?>
        </div>

        <div class="form-group">
            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" id="usuario" name="usuario" value="<?php echo val($datos, 'usuario'); ?>" class="<?php echo !empty($errores['usuario']) ? 'campo-error' : ''; ?>" required>
            <?php err($errores, 'usuario'); ?>
        </div>

        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" value="<?php echo val($datos, 'correo'); ?>" class="<?php echo !empty($errores['correo']) ? 'campo-error' : ''; ?>" required>
            <?php err($errores, 'correo'); ?>
        </div>

        <div class="form-group">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" class="<?php echo !empty($errores['sexo']) ? 'campo-error' : ''; ?>" required>
                <option value="" disabled <?php echo empty($datos['sexo']) ? 'selected' : ''; ?>>-- Selecciona --</option>
                <option value="M" <?php echo val($datos, 'sexo') === 'M' ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?php echo val($datos, 'sexo') === 'F' ? 'selected' : ''; ?>>Femenino</option>
                <option value="Otro" <?php echo val($datos, 'sexo') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
            </select>
            <?php err($errores, 'sexo'); ?>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" class="<?php echo !empty($errores['password']) ? 'campo-error' : ''; ?>" required>
            <?php err($errores, 'password'); ?>
        </div>

        <button type="submit">Registrar Cuenta</button>
    </form>

    <div class="form-footer">
        <a href="login.php" class="btn-regresar">Volver al Login</a>
    </div>
</div>

</body>
</html>