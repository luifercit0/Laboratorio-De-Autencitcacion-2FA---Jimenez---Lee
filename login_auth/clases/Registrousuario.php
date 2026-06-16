<?php
//Registrousuario.php
class Registrousuario {
    private $db;
    private $nombre;
    private $apellido;
    private $usuario;
    private $correo;
    private $password;
    private $sexo;

    public function __construct($db_object) {
        $this->db = $db_object;
    }

    // Método para asignar y encriptar la contraseña
    public function setDatos($nombre, $apellido, $usuario, $correo, $password, $sexo = 'Otro') {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->usuario = $usuario;
        $this->correo = $correo;
        
        // Aplicamos el Hashing seguro con BCRYPT (Costo por defecto o 13 recomendado)
        $this->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->sexo = $sexo;
    }

    // Ejecuta la inserción en la base de datos usando el arreglo mapeado
    public function guardarRegistro() {
        $tabla = "usuarios";
        
        $datos = [
            'Nombre'       => $this->nombre,
            'Apellido'     => $this->apellido,
            'Usuario'      => $this->usuario,
            'Correo'       => $this->correo,
            'HashMagic'    => $this->password, // Guardamos el Hash resultante
            'Sexo'         => $this->sexo,
            'Fechasistema' => date('Y-m-d H:i:s')
        ];

        return $this->db->insertSeguro($tabla, $datos);
    }
}
?>