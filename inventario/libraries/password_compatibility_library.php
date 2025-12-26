<?php
/**
 * Compatibilidad con API de password hashing (PHP < 5.5)
 * Actualizado a estándares modernos (PHP 7+)
 *
 * @author Anthony Ferrara <ircmaxell@php.net>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */

if (!defined('PASSWORD_DEFAULT')) {

    define('PASSWORD_BCRYPT', 1);
    define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);

    function password_hash($password, $algo, array $options = array()) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt debe estar habilitado para que password_hash funcione", E_USER_WARNING);
            return null;
        }
        if (!is_string($password)) {
            trigger_error("password_hash(): La contraseña debe ser una cadena", E_USER_WARNING);
            return null;
        }
        if (!is_int($algo)) {
            trigger_error("password_hash() espera el parámetro 2 como entero, dado: " . gettype($algo), E_USER_WARNING);
            return null;
        }

        switch ($algo) {
            case PASSWORD_BCRYPT:
                $cost = isset($options['cost']) ? (int)$options['cost'] : 10;
                if ($cost < 4 || $cost > 31) {
                    trigger_error(sprintf("password_hash(): Coste inválido: %d", $cost), E_USER_WARNING);
                    return null;
                }
                $raw_salt_len = 16;
                $required_salt_len = 22;
                $hash_format = sprintf("$2y$%02d$", $cost);
                break;
            default:
                trigger_error(sprintf("password_hash(): Algoritmo desconocido: %s", $algo), E_USER_WARNING);
                return null;
        }

        // Manejo del salt
        if (isset($options['salt'])) {
            $salt = (string)$options['salt'];
            if (strlen($salt) < $required_salt_len) {
                trigger_error(sprintf("password_hash(): Salt demasiado corto: %d, esperado %d", strlen($salt), $required_salt_len), E_USER_WARNING);
                return null;
            } elseif (!preg_match('#^[a-zA-Z0-9./]+$#D', $salt)) {
                $salt = str_replace('+', '.', base64_encode($salt));
            }
        } else {
            try {
                // Método moderno y seguro
                $buffer = random_bytes($raw_salt_len);
            } catch (Exception $e) {
                trigger_error("password_hash(): No se pudo generar salt seguro", E_USER_WARNING);
                return null;
            }
            $salt = str_replace('+', '.', base64_encode($buffer));
        }

        $salt = substr($salt, 0, $required_salt_len);
        $hash = $hash_format . $salt;
        $ret = crypt($password, $hash);

        if (!is_string($ret) || strlen($ret) <= 13) {
            return false;
        }
        return $ret;
    }

    function password_get_info($hash) {
        $return = array(
            'algo' => 0,
            'algoName' => 'unknown',
            'options' => array(),
        );
        if (substr($hash, 0, 4) == '$2y$' && strlen($hash) == 60) {
            $return['algo'] = PASSWORD_BCRYPT;
            $return['algoName'] = 'bcrypt';
            list($cost) = sscanf($hash, "$2y$%d$");
            $return['options']['cost'] = $cost;
        }
        return $return;
    }

    function password_needs_rehash($hash, $algo, array $options = array()) {
        $info = password_get_info($hash);
        if ($info['algo'] != $algo) {
            return true;
        }
        switch ($algo) {
            case PASSWORD_BCRYPT:
                $cost = isset($options['cost']) ? $options['cost'] : 10;
                if ($cost != $info['options']['cost']) {
                    return true;
                }
                break;
        }
        return false;
    }

    function password_verify($password, $hash) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt debe estar habilitado para que password_verify funcione", E_USER_WARNING);
            return false;
        }
        $ret = crypt($password, $hash);
        if (!is_string($ret) || strlen($ret) != strlen($hash) || strlen($ret) <= 13) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < strlen($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }
        return $status === 0;
    }
}
