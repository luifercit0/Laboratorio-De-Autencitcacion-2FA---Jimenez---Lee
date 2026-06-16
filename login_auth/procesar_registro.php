<?php
// procesar_registro.php
session_start();

require_once 'clases/AntiCSRF.php';
require_once 'clases/mod_db.PHP';
require_once 'clases/SanitizarEntrada.PHP';
require_once 'clases/Registrousuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Registrese_form.php");
    exit;
}

AntiCSRF::verificar();

$nombre   = SanitizarEntrada::limpiarCadena($_POST['nombre']);
$apellido = SanitizarEntrada::limpiarCadena($_POST['apellido']);
$usuario  = SanitizarEntrada::limpiarCadena($_POST['usuario']);
$correo   = SanitizarEntrada::limpiarCadena($_POST['correo']);
$sexo     = SanitizarEntrada::validarSexo($_POST['sexo'] ?? '');
$password = $_POST['password'];

$conexionDb = new mod_db();
$pdo = $conexionDb->getConexion();

$errores = [];

// Validar campos vacíos
if (empty($nombre))   $errores['nombre']   = 'El nombre es obligatorio.';
if (empty($apellido)) $errores['apellido'] = 'El apellido es obligatorio.';
if (empty($usuario))  $errores['usuario']  = 'El nombre de usuario es obligatorio.';
if (empty($correo))   $errores['correo']   = 'El correo es obligatorio.';
if (empty($password)) $errores['password'] = 'La contraseña es obligatoria.';

// Validar duplicados en BD
if (empty($errores)) {
    try {
        $sqlCheck = "SELECT Usuario, Correo FROM usuarios WHERE Usuario = :user OR Correo = :email";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([':user' => $usuario, ':email' => $correo]);
        $filas = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

        foreach ($filas as $fila) {
            if ($fila['Usuario'] === $usuario) {
                $errores['usuario'] = 'Este nombre de usuario ya está registrado.';
            }
            if ($fila['Correo'] === $correo) {
                $errores['correo'] = 'Este correo electrónico ya está registrado.';
            }
        }
    } catch (PDOException $e) {
        die("Error de validación: " . $e->getMessage());
    }
}

// Si hay errores, volver al form con los errores y los valores anteriores
if (!empty($errores)) {
    $_SESSION['errores_registro'] = $errores;
    $_SESSION['datos_registro'] = [
        'nombre'   => $nombre,
        'apellido' => $apellido,
        'usuario'  => $usuario,
        'correo'   => $correo,
        'sexo'     => $sexo,
    ];
    header("Location: Registrese_form.php");
    exit;
}

// Registro si pasa la validación
$registro = new Registrousuario($conexionDb);
$registro->setDatos($nombre, $apellido, $usuario, $correo, $password, $sexo);

if ($registro->guardarRegistro()) {
    echo "<script>
            alert('Usuario registrado exitosamente.');
            window.location.href = 'login.php';
          </script>";
} else {
    echo "<h3>Hubo un error al procesar el registro.</h3>";
}

$conexionDb->disconnect();
?>