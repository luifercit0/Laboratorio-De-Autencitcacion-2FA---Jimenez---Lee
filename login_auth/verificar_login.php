<?php
session_start();

require_once 'clases/AntiCSRF.php';
require_once 'clases/Logger.php';
require_once 'clases/mod_db.PHP'; 
require_once 'clases/SanitizarEntrada.PHP';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

AntiCSRF::verificar();

$userInput = SanitizarEntrada::limpiarCadena($_POST['usuario']);
$passInput = $_POST['password'];

$db = new mod_db();

$ip_acceso = $_SERVER['REMOTE_ADDR'];
if ($ip_acceso === '::1') { $ip_acceso = '127.0.0.1'; } 

$usuarioData = $db->log($userInput);

if ($usuarioData) {
    if (password_verify($passInput, $usuarioData->HashMagic)) {
        
        // LOGIN EXITOSO
        $logData = [
            'Usuario'      => $userInput,
            'IP_Acceso'    => $ip_acceso,
            'Estado'       => 'EXITOSO',
            'Detalle'      => 'Acceso correcto al sistema',
            'Fechasistema' => date('Y-m-d H:i:s')
        ];
        $db->insertSeguro('intentos_login', $logData);

        $_SESSION['user'] = $usuarioData->Usuario;
        $_SESSION['nombre_completo'] = $usuarioData->Nombre . " " . $usuarioData->Apellido;

        header("Location: panel.php");
        exit;
    } else {
        // Contraseña incorrecta
        $logData = [
            'Usuario'      => $userInput,
            'IP_Acceso'    => $ip_acceso,
            'Estado'       => 'FALLIDO',
            'Detalle'      => 'Contraseña incorrecta',
            'Fechasistema' => date('Y-m-d H:i:s')
        ];
        $db->insertSeguro('intentos_login', $logData);

        echo "<script>alert('Credenciales incorrectas.'); window.location.href='login.php';</script>";
        exit;
    }
} else {
    // Usuario no existe
    $logData = [
        'Usuario'      => $userInput,
        'IP_Acceso'    => $ip_acceso,
        'Estado'       => 'FALLIDO',
        'Detalle'      => 'El nombre de usuario no existe',
        'Fechasistema' => date('Y-m-d H:i:s')
    ];
    $db->insertSeguro('intentos_login', $logData);

    echo "<script>alert('Credenciales incorrectas.'); window.location.href='login.php';</script>";
    exit;
}
?>