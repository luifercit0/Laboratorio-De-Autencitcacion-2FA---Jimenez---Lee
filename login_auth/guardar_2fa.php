<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once 'clases/AntiCSRF.php';

if (!AntiCSRF::verificarSilencioso()) {
    $_SESSION['error_2fa'] = 'Token no válido. Por favor vuelve a escanear el QR.';
    header("Location: configurar_2fa.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['temp_secret'])) {
    header("Location: configurar_2fa.php");
    exit;
}

require_once 'clases/mod_db.PHP';
require_once 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$db = new mod_db();
$g = new GoogleAuthenticator();

$usuario        = $_SESSION['user'];
$secretTemporal = $_SESSION['temp_secret'];
$codigoIntroducido = trim($_POST['codigo_verificacion']);

if ($g->checkCode($secretTemporal, $codigoIntroducido)) {
    try {
        $pdo = $db->getConexion();
        $sql = "UPDATE usuarios SET secret_2fa = :secret WHERE Usuario = :user";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':secret' => $secretTemporal, ':user' => $usuario]);

        unset($_SESSION['temp_secret']);

        echo "<script>
                alert('¡2FA activado con éxito en tu cuenta!');
                window.location.href = 'configurar_2fa.php';
              </script>";
        exit;
    } catch (PDOException $e) {
        echo "Error al guardar el segundo factor: " . $e->getMessage();
    }
} else {
    echo "<script>
            alert('Código inválido o expirado. Asegúrate de que la hora de tu PC y celular estén sincronizados.');
            window.location.href = 'configurar_2fa.php';
          </script>";
    exit;
}
?>