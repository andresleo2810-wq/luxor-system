<?php
require_once 'config.php';
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header('Location: vista/dashboard.php');
    exit;
} else {
    header('Location: vista/login.php');
    exit;
}
?>