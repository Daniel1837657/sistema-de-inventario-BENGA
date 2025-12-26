<?php
session_start();

// Usar operador ?? para evitar "undefined index" y comparar de forma segura
if (empty($_SESSION['user_login_status']) || $_SESSION['user_login_status'] !== 1) {
    header("Location: ../login.php");
    exit();
}
