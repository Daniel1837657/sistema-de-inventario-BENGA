<?php
// Verificación mínima de versión PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Simple PHP Login no funciona en versiones de PHP menores a 5.3.7");
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
    require_once("libraries/password_compatibility_library.php");
}

// Carga configuración de base de datos y clase de login
require_once("config/db.php");
require_once("classes/Login.php");

$login = new Login();

// Si el usuario ya está logueado, redirigir al dashboard (stock.php)
if ($login->isUserLoggedIn()) {
    header("Location: stock.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <title>BENGA | Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="css/login.css" rel="stylesheet" media="screen,projection" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .card-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .profile-img-card {
            display: block;
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-signin {
            margin-top: 1.5rem;
        }

        .form-control {
            height: 50px;
            font-size: 16px;
            margin-bottom: 1rem;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-signin {
            height: 50px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .form-check {
            margin-left: 0.5rem;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: #495057;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card card-container">
            <img id="profile-img" class="profile-img-card" src="img/BENGALogo.jpg" alt="Perfil" />
            <h4 class="text-center mb-4 text-dark">Iniciar Sesión</h4>
            <form method="post" action="login.php" name="loginform" autocomplete="on" role="form" class="form-signin" novalidate>
                <?php if (!empty($login->errors)) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Error!</strong>
                        <?php foreach ($login->errors as $error) echo htmlspecialchars($error) . "<br>"; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($login->messages)) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Aviso!</strong>
                        <?php foreach ($login->messages as $message) echo htmlspecialchars($message) . "<br>"; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="form-floating mb-3">
                    <input class="form-control" placeholder="Email" name="user_name" type="email" id="user_name" autocomplete="email" autofocus required>
                    <label for="user_name">Email</label>
                </div>
                
                <div class="mb-3">
                    <div class="input-group">
                        <input class="form-control" placeholder="Contraseña" name="user_password" type="password" id="user_password" autocomplete="current-password" required style="height: 50px; font-size: 16px; border-radius: 10px 0 0 10px;" oninput="checkPasswordStrength()">
                        <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword" style="border-radius: 0 10px 10px 0;">
                            <i class="bi bi-eye" id="loginEyeIcon"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="progress" style="height: 6px; display: none;" id="passwordStrengthBar">
                            <div class="progress-bar" role="progressbar" style="width: 0%" id="strengthProgress"></div>
                        </div>
                        <small class="text-muted" id="passwordStrengthText" style="display: none;"></small>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me">
                    <label class="form-check-label" for="rememberMe">
                        <i class="bi bi-bookmark-heart me-1"></i>Recordar mis datos
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 btn-signin" name="login" id="submit">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                </button>

                <div class="text-center mt-3">
                    <a href="registro.php" class="text-decoration-none">¿No tienes cuenta? Regístrate</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    
    <script>
        // Toggle password visibility for login
        document.getElementById('toggleLoginPassword').addEventListener('click', function() {
            const password = document.getElementById('user_password');
            const eyeIcon = document.getElementById('loginEyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        });

        // Load saved credentials if available
        document.addEventListener('DOMContentLoaded', function() {
            const savedEmail = localStorage.getItem('benga_saved_email');
            const rememberMe = localStorage.getItem('benga_remember_me') === 'true';
            
            if (rememberMe && savedEmail) {
                document.getElementById('user_name').value = savedEmail;
                document.getElementById('rememberMe').checked = true;
            }
        });

        // Save credentials when form is submitted
        document.querySelector('form[name="loginform"]').addEventListener('submit', function() {
            const rememberMe = document.getElementById('rememberMe').checked;
            const email = document.getElementById('user_name').value;
            
            if (rememberMe) {
                localStorage.setItem('benga_saved_email', email);
                localStorage.setItem('benga_remember_me', 'true');
            } else {
                localStorage.removeItem('benga_saved_email');
                localStorage.removeItem('benga_remember_me');
            }
        });

        // Password strength checker
        function checkPasswordStrength() {
            const password = document.getElementById('user_password').value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthProgress = document.getElementById('strengthProgress');
            const strengthText = document.getElementById('passwordStrengthText');
            
            if (password.length === 0) {
                strengthBar.style.display = 'none';
                strengthText.style.display = 'none';
                return;
            }
            
            strengthBar.style.display = 'block';
            strengthText.style.display = 'block';
            
            let score = 0;
            let feedback = [];
            
            // Length check
            if (password.length >= 8) {
                score += 20;
            } else {
                feedback.push('mínimo 8 caracteres');
            }
            
            // Uppercase check
            if (/[A-Z]/.test(password)) {
                score += 20;
            } else {
                feedback.push('mayúscula');
            }
            
            // Lowercase check
            if (/[a-z]/.test(password)) {
                score += 20;
            } else {
                feedback.push('minúscula');
            }
            
            // Number check
            if (/[0-9]/.test(password)) {
                score += 20;
            } else {
                feedback.push('número');
            }
            
            // Special character check
            if (/[^A-Za-z0-9]/.test(password)) {
                score += 20;
            } else {
                feedback.push('carácter especial');
            }
            
            // Update progress bar
            strengthProgress.style.width = score + '%';
            
            // Update colors and text
            if (score < 40) {
                strengthProgress.className = 'progress-bar bg-danger';
                strengthText.innerHTML = '<i class="bi bi-shield-x me-1"></i>Débil - Falta: ' + feedback.join(', ');
                strengthText.className = 'text-danger small';
            } else if (score < 80) {
                strengthProgress.className = 'progress-bar bg-warning';
                strengthText.innerHTML = '<i class="bi bi-shield-check me-1"></i>Media - Falta: ' + feedback.join(', ');
                strengthText.className = 'text-warning small';
            } else {
                strengthProgress.className = 'progress-bar bg-success';
                strengthText.innerHTML = '<i class="bi bi-shield-fill-check me-1"></i>Fuerte - Contraseña segura';
                strengthText.className = 'text-success small';
            }
        }
    </script>
</body>

</html>