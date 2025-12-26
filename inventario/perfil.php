<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit();
}

require_once("config/db.php");
$title = "Perfil de Usuario";
$active_perfil = "active";

// Obtener datos del usuario
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

$user_id = $_SESSION['user_id'];

// Agregar columnas si no existen
$connection->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) DEFAULT NULL");
$connection->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL");
$connection->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS position VARCHAR(100) DEFAULT NULL");

// Crear tabla company_info si no existe
$connection->query("CREATE TABLE IF NOT EXISTS company_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200),
    nit VARCHAR(50),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(200),
    industry VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Obtener datos del usuario
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

// Obtener datos de la empresa
$company_data = null;
$query = "SELECT * FROM company_info LIMIT 1";
$result = $connection->query($query);
if ($result && $result->num_rows > 0) {
    $company_data = $result->fetch_assoc();
}

// Procesar formularios
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update_personal') {
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $user_name = trim($_POST['user_name']);
            $user_email = trim($_POST['user_email']);
            $phone = trim($_POST['phone']);
            $position = trim($_POST['position']);
            
            $query = "UPDATE users SET firstname=?, lastname=?, user_name=?, user_email=?, phone=?, position=? WHERE user_id=?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ssssssi", $firstname, $lastname, $user_name, $user_email, $phone, $position, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['firstname'] = $firstname;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_email'] = $user_email;
                $success_message = "Información personal actualizada correctamente";
                // Recargar datos
                $stmt = $connection->prepare("SELECT * FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user_data = $stmt->get_result()->fetch_assoc();
            } else {
                $error_message = "Error al actualizar información personal";
            }
        }
        
        if ($_POST['action'] == 'update_company') {
            $company_name = trim($_POST['company_name']);
            $nit = trim($_POST['nit']);
            $address = trim($_POST['address']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);
            $website = trim($_POST['website']);
            $industry = trim($_POST['industry']);
            $description = trim($_POST['description']);
            
            // Verificar si existe registro
            $check = $connection->query("SELECT id FROM company_info LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $row = $check->fetch_assoc();
                $query = "UPDATE company_info SET company_name=?, nit=?, address=?, phone=?, email=?, website=?, industry=?, description=? WHERE id=?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ssssssssi", $company_name, $nit, $address, $phone, $email, $website, $industry, $description, $row['id']);
            } else {
                $query = "INSERT INTO company_info (company_name, nit, address, phone, email, website, industry, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ssssssss", $company_name, $nit, $address, $phone, $email, $website, $industry, $description);
            }
            
            if ($stmt->execute()) {
                $success_message = "Información de empresa actualizada correctamente";
                // Recargar datos
                $result = $connection->query("SELECT * FROM company_info LIMIT 1");
                if ($result && $result->num_rows > 0) {
                    $company_data = $result->fetch_assoc();
                }
            } else {
                $error_message = "Error al actualizar información de empresa";
            }
        }
    }
}

// Manejar subida de imagen
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $upload_dir = 'uploads/profiles/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['profile_image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    
    if (in_array(strtolower($filetype), $allowed)) {
        $newname = 'profile_' . $user_id . '_' . time() . '.' . $filetype;
        $target = $upload_dir . $newname;
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Eliminar imagen anterior
            if (!empty($user_data['profile_image'])) {
                $old_file = $upload_dir . $user_data['profile_image'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            // Actualizar base de datos
            $query = "UPDATE users SET profile_image = ? WHERE user_id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("si", $newname, $user_id);
            
            if ($stmt->execute()) {
                $success_message = "Imagen de perfil actualizada correctamente";
                $user_data['profile_image'] = $newname;
            } else {
                $error_message = "Error al guardar imagen en base de datos";
            }
        } else {
            $error_message = "Error al subir imagen";
        }
    } else {
        $error_message = "Formato de imagen no válido";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("head.php"); ?>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            object-fit: cover;
        }
        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .info-item {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .info-value {
            color: #6c757d;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <?php include("navbar.php"); ?>

        <main class="main-content">
            <div class="container my-4">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Header del Perfil -->
                <div class="profile-header text-center">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <img src="<?php echo !empty($user_data['profile_image']) ? 'uploads/profiles/' . $user_data['profile_image'] : 'https://via.placeholder.com/120x120/667eea/ffffff?text=' . strtoupper(substr($user_data['firstname'], 0, 1)); ?>" 
                                 alt="Foto de Perfil" 
                                 class="profile-avatar">
                            <div class="mt-2">
                                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                    <i class="bi bi-camera me-1"></i>Cambiar Foto
                                </button>
                            </div>
                        </div>
                        <div class="col-md-9 text-md-start">
                            <h2 class="mb-1"><?php echo htmlspecialchars($user_data['firstname'] . ' ' . $user_data['lastname']); ?></h2>
                            <p class="mb-1 opacity-75">@<?php echo htmlspecialchars($user_data['user_name']); ?></p>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($user_data['user_email']); ?>
                            </p>
                            <small class="opacity-75">
                                <i class="bi bi-calendar me-1"></i>Miembro desde <?php echo date('d/m/Y', strtotime($user_data['date_added'])); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Información Personal -->
                    <div class="col-lg-6 mb-4">
                        <div class="profile-card card h-100">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-person me-2"></i>Información Personal
                                </h5>
                                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editPersonalModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="info-item">
                                    <div class="info-label">Nombre Completo</div>
                                    <p class="info-value"><?php echo htmlspecialchars($user_data['firstname'] . ' ' . $user_data['lastname']); ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Usuario</div>
                                    <p class="info-value"><?php echo htmlspecialchars($user_data['user_name']); ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Correo Electrónico</div>
                                    <p class="info-value"><?php echo htmlspecialchars($user_data['user_email']); ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Teléfono</div>
                                    <p class="info-value"><?php echo !empty($user_data['phone']) ? htmlspecialchars($user_data['phone']) : 'No especificado'; ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Cargo</div>
                                    <p class="info-value"><?php echo !empty($user_data['position']) ? htmlspecialchars($user_data['position']) : 'No especificado'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Empresa -->
                    <div class="col-lg-6 mb-4">
                        <div class="profile-card card h-100">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-building me-2"></i>Información de la Empresa
                                </h5>
                                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editCompanyModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="info-item">
                                    <div class="info-label">Nombre de la Empresa</div>
                                    <p class="info-value"><?php echo $company_data && !empty($company_data['company_name']) ? htmlspecialchars($company_data['company_name']) : 'No especificado'; ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">NIT</div>
                                    <p class="info-value"><?php echo $company_data && !empty($company_data['nit']) ? htmlspecialchars($company_data['nit']) : 'No especificado'; ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Dirección</div>
                                    <p class="info-value"><?php echo $company_data && !empty($company_data['address']) ? htmlspecialchars($company_data['address']) : 'No especificado'; ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Teléfono</div>
                                    <p class="info-value"><?php echo $company_data && !empty($company_data['phone']) ? htmlspecialchars($company_data['phone']) : 'No especificado'; ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <p class="info-value"><?php echo $company_data && !empty($company_data['email']) ? htmlspecialchars($company_data['email']) : 'No especificado'; ?></p>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Sitio Web</div>
                                    <p class="info-value"><?php echo $company_data && !empty($company_data['website']) ? htmlspecialchars($company_data['website']) : 'No especificado'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <?php 
        include("modal/upload_image.php");
        include("modal/edit_personal.php");
        include("modal/edit_company.php");
        include("footer.php"); 
        ?>
    </div>
</body>
</html>

<?php $connection->close(); ?>
