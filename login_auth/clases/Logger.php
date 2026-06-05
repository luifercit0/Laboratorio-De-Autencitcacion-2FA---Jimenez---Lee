<?php
// clases/Logger.php

class Logger {
    private static $archivo = "auditoria_eventos.log";

    // REQUERIMIENTO ANEXO PARTE 2: Escribir eventos en formato .log
    public static function registrar($usuario, $estado, $detalle) {
        $fecha = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] === '::1' ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        
        // Formato de línea estándar para logs
        $linea = "[$fecha] [IP: $ip] [Usuario: $usuario] [Estado: $estado] - Detalle: $detalle\n";
        
        // Escribe el archivo en la raíz del proyecto de forma persistente (FILE_APPEND)
        file_put_contents(self::$archivo, $linea, FILE_APPEND);
    }
}
?>