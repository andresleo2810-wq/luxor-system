<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificar_login() {
    if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
        header('Location: ../vista/login.php');
        exit;
    }
}

function solo_admin() {
    verificar_login();
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
        header('Location: ../../vista/dashboard.php?error=no_permiso');
        exit;
    }
}

function es_admin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';
}
?>