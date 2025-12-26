<?php
/*-------------------------
Autor: Daniel Felipe Perdomo Hernández
---------------------------*/

// Incluir configuración de base de datos
require_once("db.php");

// Crear conexión global
$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($con->connect_error) {
    die("Imposible conectarse: " . $con->connect_error);
}

// Establecer charset utf8 para evitar problemas con caracteres especiales
$con->set_charset("utf8");

// Función para cerrar la conexión
function cerrar_conexion() {
    global $con;
    if ($con) {
        $con->close();
    }
}

// Registrar función para cerrar conexión al finalizar el script
register_shutdown_function('cerrar_conexion');
?>
