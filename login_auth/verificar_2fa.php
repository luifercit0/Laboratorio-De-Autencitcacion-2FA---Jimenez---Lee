<?php
// verificar_2fa.php
session_start();

if (!isset($_SESSION['auth_usuario_temporal'])) {
    header("Location: login.php");
    exit;
}

require_once 'clases/AntiCSRF.php';
require_once 'clases/mod_db.PHP';
require_once 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AntiCSRF::verificar();
    $db = new mod_db();
    $g = new GoogleAuthenticator();
    
    $usuario = $_SESSION['auth_usuario_temporal'];
    $codigoIntroducido = trim($_POST['codigo_totp']);
    
    $usuarioData = $db->log($usuario);
    $secretGuardado = $usuarioData->secret_2fa;
    
    if ($g->checkCode($secretGuardado, $codigoIntroducido)) {
        $_SESSION['user'] = $usuarioData->Usuario;
        $_SESSION['nombre_completo'] = $usuarioData->Nombre . " " . $usuarioData->Apellido;
        unset($_SESSION['auth_usuario_temporal']);
        header("Location: panel.php");
        exit;
    } else {
        $mensaje = "<p style='color: #e53e3e; font-weight: bold; margin-bottom: 15px;'>Código incorrecto o expirado. Inténtalo de nuevo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Segundo Factor (2FA)</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
    <h1>Verificación de Seguridad</h1>
    <p>Introduce el código temporal de 6 dígitos de tu aplicación <strong>Google Authenticator</strong> para completar el acceso:</p>
    
    <?php echo $mensaje; ?>
    
    <form action="verificar_2fa.php" method="POST">
        <?php echo AntiCSRF::campoHidden(); ?>
        <input type="text" name="codigo_totp" placeholder="000000" class="input-code" required autocomplete="off">
        <button type="submit">Verificar e Ingresar</button>
    </form>
    
    <div class="form-footer">
        <a href="logout.php" class="btn-regresar">Cancelar inicio de sesión</a>
    </div>
</div>

</body>
</html>