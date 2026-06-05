<?php
// clases/SanitizarEntrada.php

class SanitizarEntrada {
    
    // Sanitiza una cadena eliminando espacios y etiquetas HTML peligrosas
    public static function limpiarCadena($cadena) {
        return trim(strip_tags($cadena));
    }
}
?>