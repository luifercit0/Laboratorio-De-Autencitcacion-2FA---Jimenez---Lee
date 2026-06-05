<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user']) || !isset($_SESSION['temp_secret'])) {
    header("Location: configurar_2fa.php");
    exit;
}

require_once 'clases/mod_db.PHP';
require_once 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$db = new mod_db();
$g = new GoogleAuthenticator();

$usuario = $_SESSION['user'];
$secretTemporal = $_SESSION['temp_secret'];
$codigoIntroducido = trim($_POST['codigo_verificacion']);

// Verificamos si el código ingresado por el usuario es correcto para ese secreto
if ($g->checkCode($secretTemporal, $codigoIntroducido)) {
    
    // El código es válido, procedemos a guardar el secreto en la base de datos de manera definitiva
    try {
        $pdo = $db->getConexion();
        $sql = "UPDATE usuarios SET secret_2fa = :secret WHERE Usuario = :user";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':secret' => $secretTemporal,
            ':user'   => $usuario
        ]);
        
        // Limpiamos la variable temporal de la sesión
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
    // Si el código falló o expiró (recuerda que cambia cada 30 segundos)
    echo "<script>
            alert('Código inválido o expirado. Por favor, vuelve a intentarlo asegurándote de que la hora de tu PC y celular estén sincronizados.');
            window.location.href = 'configurar_2fa.php';
          </script>";
    exit;
}
?>