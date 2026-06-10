<?php
session_start();
require_once '../../modelo/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    try {
        $conexion = new Conexion();
        $pdo = $conexion->conectar();
        $stmt = $pdo->prepare("CALL sp_Login_Validate(?, ?)");
        $stmt->execute([$email, $password]);
        $resultado = $stmt->fetch();
        if ($resultado['Resultado'] === 'SUCCESS') {
            $_SESSION['logueado'] = true;
            $_SESSION['idUsuario'] = $resultado['idUsuario'];
            $_SESSION['nombre'] = $resultado['Usuario'];
            $_SESSION['rol'] = $resultado['NombreRol'];
            header('Location: ../../vista/dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = $resultado['Mensaje'];
            header('Location: ../../vista/login.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de conexión: " . $e->getMessage();
        header('Location: ../../vista/login.php');
        exit;
    }
} else {
    header('Location: ../../vista/login.php');
    exit;
}
?>