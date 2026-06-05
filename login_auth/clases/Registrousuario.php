<?php
// clases/Registrousuario.php

class Registrousuario {
    private $db;
    private $nombre;
    private $apellido;
    private $usuario;
    private $correo;
    private $password;

    // Pasamos el objeto de la base de datos al instanciar la clase
    public function __construct($db_object) {
        $this->db = $db_object;
    }

    // Método para asignar y encriptar (hashear) la contraseña
    public function setDatos($nombre, $apellido, $usuario, $correo, $password) {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->usuario = $usuario;
        $this->correo = $correo;
        
        // Aplicamos el Hashing seguro con BCRYPT (Costo por defecto o 13 recomendado)
        $this->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Ejecuta la inserción en la base de datos usando el arreglo mapeado
    public function guardarRegistro() {
        $tabla = "usuarios";
        
        // Mapeamos los campos exactamente como se llaman en tu tabla de la BD
        $datos = [
            'Nombre'       => $this->nombre,
            'Apellido'     => $this->apellido,
            'Usuario'      => $this->usuario,
            'Correo'       => $this->correo,
            'HashMagic'    => $this->password, // Guardamos el Hash resultante
            'Fechasistema' => date('Y-m-d H:i:s')
        ];

        // Llamamos al método preparado del repositorio
        return $this->db->insertSeguro($tabla, $datos);
    }
}
?>