<?php
require_once('PasswordValidator.php');

class UserRegistration
{
    private $db_connection = null;
    public $errors = [];
    public $messages = [];

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $this->doRegistrationWithPostData();
        }
    }

    private function doRegistrationWithPostData()
    {
        // Validar campos requeridos
        if (empty($_POST['user_name'])) {
            $this->errors[] = "El campo nombre de usuario está vacío.";
        }
        
        if (empty($_POST['user_email'])) {
            $this->errors[] = "El campo email está vacío.";
        }
        
        if (empty($_POST['firstname'])) {
            $this->errors[] = "El campo nombre está vacío.";
        }
        
        if (empty($_POST['user_password'])) {
            $this->errors[] = "El campo contraseña está vacío.";
        }
        
        if (empty($_POST['user_password_repeat'])) {
            $this->errors[] = "Debe confirmar la contraseña.";
        }

        // Si hay errores básicos, no continuar
        if (!empty($this->errors)) {
            return;
        }

        $user_name = trim($_POST['user_name']);
        $user_email = trim($_POST['user_email']);
        $firstname = trim($_POST['firstname']);
        $user_password = $_POST['user_password'];
        $user_password_repeat = $_POST['user_password_repeat'];

        // Validar email
        $email_errors = PasswordValidator::validateEmail($user_email);
        if (!empty($email_errors)) {
            $this->errors = array_merge($this->errors, $email_errors);
        }

        // Validar contraseña
        $password_errors = PasswordValidator::validate($user_password);
        if (!empty($password_errors)) {
            $this->errors = array_merge($this->errors, $password_errors);
        }

        // Verificar que las contraseñas coincidan
        if ($user_password !== $user_password_repeat) {
            $this->errors[] = "Las contraseñas no coinciden.";
        }

        // Si hay errores de validación, no continuar
        if (!empty($this->errors)) {
            return;
        }

        // Conectar a la base de datos
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db_connection->connect_errno) {
            $this->errors[] = "Error en la conexión a la base de datos.";
            return;
        }

        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = "Error al establecer la codificación.";
            return;
        }

        // Verificar si el usuario o email ya existen
        $stmt = $this->db_connection->prepare("SELECT user_id FROM users WHERE user_name = ? OR user_email = ? LIMIT 1");
        $stmt->bind_param("ss", $user_name, $user_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $this->errors[] = "El nombre de usuario o email ya están registrados.";
            $stmt->close();
            $this->db_connection->close();
            return;
        }
        $stmt->close();

        // Crear hash de la contraseña
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario
        $stmt = $this->db_connection->prepare("INSERT INTO users (user_name, user_email, firstname, user_password_hash, date_added) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $user_name, $user_email, $firstname, $user_password_hash);

        if ($stmt->execute()) {
            $this->messages[] = "Usuario registrado exitosamente. Ya puedes iniciar sesión.";
        } else {
            $this->errors[] = "Error al registrar el usuario: " . $stmt->error;
        }

        $stmt->close();
        $this->db_connection->close();
    }
}
?>
