<?php
class Login
{
    private $db_connection = null;
    public $errors = [];
    public $messages = [];
    private $max_login_attempts = 5;
    private $login_timeout_seconds = 300; // 5 minutos

    public function __construct()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $this->dologinWithPostData();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->doLogout();
        }
    }

    private function dologinWithPostData()
    {
        if (empty($_POST['user_name'])) {
            $this->errors[] = "El campo usuario está vacío.";
            return;
        }
        if (empty($_POST['user_password'])) {
            $this->errors[] = "El campo contraseña está vacío.";
            return;
        }

        if ($this->isLoginBlocked()) {
            $this->errors[] = "Demasiados intentos fallidos. Intenta de nuevo en 5 minutos.";
            return;
        }

        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->db_connection->connect_errno) {
            $this->errors[] = "Error en la conexión a la base de datos.";
            return;
        }

        if (!$this->db_connection->set_charset("utf8")) {
            $this->errors[] = "Error al establecer la codificación.";
            return;
        }

        $user_name = $_POST['user_name'];
        $password = $_POST['user_password'];

        $stmt = $this->db_connection->prepare("SELECT user_id, user_name, firstname, user_email, user_password_hash FROM users WHERE user_name = ? OR user_email = ? LIMIT 1");
        $stmt->bind_param("ss", $user_name, $user_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_object();

            if (password_verify($password, $user->user_password_hash)) {
                // Login exitoso
                $_SESSION['user_id'] = $user->user_id;
                $_SESSION['firstname'] = $user->firstname;
                $_SESSION['user_name'] = $user->user_name;
                $_SESSION['user_email'] = $user->user_email;
                $_SESSION['user_login_status'] = 1;

                // Limpiar intentos fallidos
                $this->clearLoginAttempts();

                $this->messages[] = "Has iniciado sesión correctamente.";
            } else {
                $this->registerFailedAttempt();
                $this->errors[] = "Usuario y/o contraseña incorrectos.";
            }
        } else {
            $this->registerFailedAttempt();
            $this->errors[] = "Usuario y/o contraseña incorrectos.";
        }

        $stmt->close();
        $this->db_connection->close();
    }

    public function doLogout()
    {
        // Solo aceptar logout por POST para evitar CSRF simple
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->errors[] = "Acceso no autorizado para cerrar sesión.";
            return;
        }

        $_SESSION = [];
        session_destroy();
        $this->messages[] = "Has sido desconectado correctamente.";
    }

    public function isUserLoggedIn()
    {
        return isset($_SESSION['user_login_status']) && $_SESSION['user_login_status'] == 1;
    }

    private function registerFailedAttempt()
    {
        if (!isset($_SESSION['failed_login_attempts'])) {
            $_SESSION['failed_login_attempts'] = 0;
            $_SESSION['first_failed_attempt_time'] = time();
        }
        $_SESSION['failed_login_attempts']++;
    }

    private function clearLoginAttempts()
    {
        unset($_SESSION['failed_login_attempts']);
        unset($_SESSION['first_failed_attempt_time']);
    }

    private function isLoginBlocked()
    {
        if (!isset($_SESSION['failed_login_attempts']) || !isset($_SESSION['first_failed_attempt_time'])) {
            return false;
        }

        if ($_SESSION['failed_login_attempts'] >= $this->max_login_attempts) {
            $elapsed = time() - $_SESSION['first_failed_attempt_time'];
            if ($elapsed < $this->login_timeout_seconds) {
                return true; // bloqueo activo
            } else {
                $this->clearLoginAttempts();
                return false;
            }
        }
        return false;
    }
}
?>
