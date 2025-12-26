<?php
class PasswordValidator
{
    /**
     * Valida que la contraseña cumpla con los requisitos de seguridad
     * - Al menos 8 caracteres
     * - Al menos una mayúscula
     * - Al menos una minúscula
     * - Al menos un número
     * - Al menos un carácter especial
     */
    public static function validate($password)
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "La contraseña debe contener al menos una letra mayúscula";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "La contraseña debe contener al menos una letra minúscula";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "La contraseña debe contener al menos un número";
        }
        
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            $errors[] = "La contraseña debe contener al menos un carácter especial (!@#$%^&*()_+-=[]{}|;':\",./<>?)";
        }
        
        return $errors;
    }
    
    /**
     * Valida que el email tenga un formato correcto
     */
    public static function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["El formato del email no es válido"];
        }
        return [];
    }
}
?>
