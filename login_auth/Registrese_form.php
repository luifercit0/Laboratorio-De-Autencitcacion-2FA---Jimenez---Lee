<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Registro - Laboratorio #1</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
    <h1>Registro de Usuario</h1>
    <p>Crea una cuenta nueva completando los campos</p>
    
    <form action="procesar_registro.php" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>
        </div>
        <div class="form-group">
            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>
        </div>
        <div class="form-group">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Registrar Cuenta</button>
    </form>
    
    <div class="form-footer">
        <a href="login.html" class="btn-regresar">Volver al Login</a>
    </div>
</div>

</body>
</html>