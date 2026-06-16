<?php
// clases/mod_db.php

class mod_db
{
    private $conexion; 
    private $perpage = 5; 
    private $total;
    private $pagecut_query;
    private $debug = false; 
    //SHOW GRANTS FOR 'root'@'localhost';
    public function __construct()
    {
        $sql_host = "localhost";
        $sql_name = "company_info";
        $sql_user = "root"; 
        $sql_pass = "luifer123";

        $dsn = "mysql:host=$sql_host;dbname=$sql_name;charset=utf8mb4";
        try {
            $this->conexion = new PDO($dsn, $sql_user, $sql_pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($this->debug) {
                echo "Conexión exitosa a la base de datos<br>";
            }
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }

    public function getConexion (){
        return $this->conexion;
    }

    public function disconnect()
    {
        $this->conexion = null; 
    }

    public function insert($tb_name, $cols, $val)
    {
        $cols = $cols ? "($cols)" : "";
        $sql = "INSERT INTO $tb_name $cols VALUES ($val)";
        try {
            $this->conexion->exec($sql);
        } catch (PDOException $e) {
            echo "Error al insertar: " . $e->getMessage();
        }
    }

    // Registrar usuarios e intentos
    public function insertSeguro($tb_name, $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO $tb_name ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->conexion->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error en INSERT Seguro: " . $e->getMessage();
            return false;
        }
    }

    public function query($string)
    {
        return $this->executeQuery($string);
    }

    // Buscar un usuario
    public function log($Usuario){
         try {
             $sql = "SELECT * FROM usuarios WHERE Usuario = :User";
             $stmt = $this->conexion->prepare($sql);
             $stmt->bindParam(':User', $Usuario, PDO::PARAM_STR);

             $stmt->execute();
             return $stmt->fetchObject(); 
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    } 

    public function nums($string = "", $stmt = null)
    {
        if ($string) {
            $stmt = $this->query($string);
        }
        $this->total = $stmt ? $stmt->rowCount() : 0; 
        return $this->total;
    }

    public function objects($string = "", $stmt = null)
    {
        if ($string) {
            $stmt = $this->query($string);
        }
        return $stmt ? $stmt->fetch(PDO::FETCH_OBJ) : null; 
    }

    public function insert_id()
    {
        return $this->conexion->lastInsertId(); 
    }

    public function page_cut($string, $nowpage = 0)
    {
        $start = $nowpage ? ($nowpage - 1) * $this->perpage : 0; 
        $this->pagecut_query = "$string LIMIT $start, $this->perpage";
        return $this->pagecut_query;
    }

    public function show_page_cut($string = "", $num = "", $url = "")
    {
        $nowpage = isset($_REQUEST['nowpage']) ? $_REQUEST['nowpage'] : 1; 
        $this->total = $string ? $this->nums($string) : $num; 
        $pages = ceil($this->total / $this->perpage); 
        $pagecut = "";

        for ($i = 1; $i <= $pages; $i++) {
            if ($nowpage == $i) {
                $pagecut .= $i; 
            } else {
                $pagecut .= "<a href='$url&nowpage=$i'><font color='336600' style='font-size:10pt'>$i</font></a>";
            }
        }
        return $pagecut; 
    }

    private function executeQuery($sql)
{
        try {
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            
            $mensajeLog = "[" . date('Y-m-d H:i:s') . "] Query ejecutada con éxito: " . $sql . "\n";
            error_log($mensajeLog, 3, "auditoria_eventos.log");
            
            return $stmt;
        } catch (PDOException $e) {
            $mensajeError = "[" . date('Y-m-d H:i:s') . "] ERROR SQL: " . $e->getMessage() . "\n";
            error_log($mensajeError, 3, "auditoria_eventos.log");
            
            echo "Error en la consulta: " . $e->getMessage();
            return null;
        }
}
}
?>