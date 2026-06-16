<?php
// clases/AntiCSRF.php

class AntiCSRF {

    // Nombre de la clave con la que se guarda el token en la sesión
    private static $sessionKey = 'csrf_token';

    /**
     * Genera un token aleatorio criptográficamente seguro y lo guarda en la sesión.
     * Si ya existe un token en la sesión, lo reutiliza (un token por sesión).
     */
    public static function generarToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::$sessionKey];
    }

    public static function campoHidden(): string {
        $token = self::generarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Verifica que el token enviado en el POST coincide con el guardado en sesión.
     * Usa comparación de tiempo constante (hash_equals) para prevenir ataques de timing.
     * Si la verificación falla, detiene la ejecución con un error 403.
     *
     * @return void
     */
    public static function verificar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tokenSesion = $_SESSION[self::$sessionKey] ?? '';
        $tokenPost   = $_POST['csrf_token'] ?? '';

        // hash_equals evita ataques de timing al comparar cadenas
        if (empty($tokenSesion) || empty($tokenPost) || !hash_equals($tokenSesion, $tokenPost)) {
            http_response_code(403);
            die('<h2>Error 403: Solicitud bloqueada.</h2><p>Token CSRF inválido o ausente. Posible ataque CSRF detectado.</p>');
        }
    }

    /**
     * Regenera el token después de una acción sensible (buena práctica opcional).
     * Útil si quieres un token diferente por cada envío de formulario.
     *
     * @return string  El nuevo token
     */
    public static function regenerarToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[self::$sessionKey] = bin2hex(random_bytes(32));
        return $_SESSION[self::$sessionKey];
    }

    /**
     * Verifica el token CSRF sin matar la ejecución.
     * Retorna true si es válido, false si no.
     */
    public static function verificarSilencioso(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tokenSesion = $_SESSION[self::$sessionKey] ?? '';
        $tokenPost   = $_POST['csrf_token'] ?? '';

        return !empty($tokenSesion) && !empty($tokenPost) && hash_equals($tokenSesion, $tokenPost);
    }
}
?>
