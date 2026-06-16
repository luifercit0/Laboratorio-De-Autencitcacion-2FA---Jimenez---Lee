<?php

class SanitizarEntrada {

    public static function limpiarCadena($cadena) {
        return trim(strip_tags($cadena));
    }

    // Sanitiza y valida el correo electrónico
    public static function limpiarCorreo($correo) {
        $correo = filter_var(trim($correo), FILTER_SANITIZE_EMAIL);
        return filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : '';
    }

    public static function validarSexo($valor) {
        $permitidos = ['M', 'F', 'Otro'];
        $valor = trim(strip_tags($valor));
        return in_array($valor, $permitidos) ? $valor : 'Otro';
    }
}
?>