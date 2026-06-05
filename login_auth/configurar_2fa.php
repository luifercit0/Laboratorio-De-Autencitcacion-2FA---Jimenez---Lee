<?php
// configurar_2fa.php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

require_once 'clases/mod_db.PHP';
require_once 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

$db = new mod_db();
$usuarioActual = $_SESSION['user'];

$usuarioData = $db->log($usuarioActual);
$g = new GoogleAuthenticator();

if (empty($usuarioData->secret_2fa)) {
    if (!isset($_SESSION['temp_secret'])) {
        $_SESSION['temp_secret'] = $g->generateSecret();
    }
    $secret = $_SESSION['temp_secret'];
    $mostrarFormularioEnrolamiento = true;
} else {
    $secret = $usuarioData->secret_2fa;
    $mostrarFormEnrollment = false;
    $mostrarFormularioEnrolamiento = false;
}

$qrCodeUrl = GoogleQrUrl::generate($usuarioActual, $secret, 'UTP_Software_VII');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar 2FA - Seguridad</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
    <?php if ($mostrarFormularioEnrolamiento): ?>
        <h1>Activar Autenticación 2FA</h1>
        <p>Escanea el siguiente código QR con la aplicación <strong>Google Authenticator</strong> en tu celular:</p>
        
        <div style="margin: 20px 0;">
            <img src="<?php echo $qrCodeUrl; ?>" alt="Código QR de Verificación">
        </div>
        
        <p>Introduce el código de 6 dígitos para confirmar la activación:</p>
        
        <form action="guardar_2fa.php" method="POST">
            <input type="text" name="codigo_verificacion" placeholder="000000" class="input-code" required>
            <button type="submit">Verificar y Activar 2FA</button>
        </form>
    <?php else: ?>
        <h1>Tu 2FA ya está activo</h1>
        <p style="color: #48bb78; font-weight: 600; font-size: 16px;">✔ Tu cuenta se encuentra completamente protegida con doble factor de autenticación.</p>
        <div style="margin: 20px 0; font-size: 60px;">
            🔒
        </div>
    <?php endif; ?>
    
    <hr>
    <a href="panel.php" class="btn-regresar">Volver al Panel</a>
</div>

</body>
</html>