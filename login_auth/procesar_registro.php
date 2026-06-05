<?php
// procesar_registro.php

require_once 'clases/mod_db.PHP';
require_once 'clases/SanitizarEntrada.PHP';
require_once 'clases/Registrousuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Registrese_form.php");
    exit;
}

$nombre   = SanitizarEntrada::limpiarCadena($_POST['nombre']);
$apellido = SanitizarEntrada::limpiarCadena($_POST['apellido']);
$usuario  = SanitizarEntrada::limpiarCadena($_POST['usuario']);
$correo   = SanitizarEntrada::limpiarCadena($_POST['correo']);
$password = $_POST['password'];

$conexionDb = new mod_db();
$pdo = $conexionDb->getConexion();

// REQUERIMIENTO PARTE 2: Comprobar que no exista el correo o usuario
try {
    $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE Usuario = :user OR Correo = :email";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':user' => $usuario, ':email' => $correo]);
    
    if ($stmtCheck->fetchColumn() > 0) {
        echo "<script>
                alert('Error: El usuario o correo ya se encuentran registrados.');
                window.location.href = 'Registrese_form.php';
              </script>";
        exit;
    }
} catch (PDOException $e) {
    die("Error de validación: " . $e->getMessage());
}

// Registro si pasa la validación
$registro = new Registrousuario($conexionDb);
$registro->setDatos($nombre, $apellido, $usuario, $correo, $password);

if ($registro->guardarRegistro()) {
    echo "<script>
            alert('Usuario registrado exitosamente.');
            window.location.href = 'login.html';
          </script>";
} else {
    echo "<h3>Hubo un error al procesar el registro.</h3>";
}

$conexionDb->disconnect();
?>