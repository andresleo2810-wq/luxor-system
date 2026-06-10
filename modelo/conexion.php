<?php
// Usar ruta absoluta para evitar problemas
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config.php';
}

class Conexion {
    private $host = DB_HOST;
    private $db = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $charset = DB_CHARSET;
    
    public function conectar() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            return new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("❌ Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
}
?>