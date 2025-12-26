<?php
// Script para configurar la base de datos completa
require_once("config/db.php");

// Conectar a MySQL sin seleccionar base de datos específica
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($connection->connect_error) {
    die("Error de conexión: " . $connection->connect_error);
}

// Leer y ejecutar el script SQL
$sql_file = 'database_complete_setup.sql';
if (!file_exists($sql_file)) {
    die("Archivo SQL no encontrado: " . $sql_file);
}

$sql_content = file_get_contents($sql_file);
$queries = explode(';', $sql_content);

echo "<h2>Configurando Base de Datos BENGA</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>";

$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query) || substr($query, 0, 2) == '--') {
        continue;
    }
    
    if ($connection->query($query)) {
        if (stripos($query, 'CREATE') !== false) {
            echo "<span style='color: green;'>✓</span> Tabla/Base creada correctamente<br>";
            $success_count++;
        } elseif (stripos($query, 'INSERT') !== false) {
            echo "<span style='color: blue;'>✓</span> Datos insertados correctamente<br>";
            $success_count++;
        }
    } else {
        if ($connection->errno != 1050 && $connection->errno != 1062) { // Ignorar "ya existe" y "duplicado"
            echo "<span style='color: red;'>✗</span> Error: " . $connection->error . "<br>";
            $error_count++;
        }
    }
}

echo "<br><strong>Resumen:</strong><br>";
echo "<span style='color: green;'>Operaciones exitosas: $success_count</span><br>";
echo "<span style='color: red;'>Errores: $error_count</span><br>";

if ($error_count == 0) {
    echo "<br><span style='color: green; font-size: 16px;'>🎉 ¡Base de datos configurada correctamente!</span><br>";
    echo "<br><strong>Datos de acceso por defecto:</strong><br>";
    echo "Usuario: admin@benga.com<br>";
    echo "Contraseña: Admin123!<br>";
    echo "<br><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";
} else {
    echo "<br><span style='color: orange;'>⚠️ Configuración completada con algunos errores</span>";
}

echo "</div>";

$connection->close();
?>
