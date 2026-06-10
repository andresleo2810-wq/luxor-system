<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
solo_admin(); // Solo administradores

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$mensaje = '';
$tipoMensaje = '';
$usuario = null;
$esEditar = false;

// Obtener roles
try {
    $stmtRoles = $pdo->query("SELECT idRoles, NombreRol FROM roles ORDER BY NombreRol");
    $roles = $stmtRoles->fetchAll();
} catch (PDOException $e) {
    $roles = [];
}

// Si hay ID, es edición
if (isset($_GET['id'])) {
    $esEditar = true;
    try {
        $stmt = $pdo->prepare("
            SELECT u.*, r.NombreRol 
            FROM usuario u
            INNER JOIN roles r ON u.idRoles = r.idRoles
            WHERE u.idUsuario = ?
        ");
        $stmt->execute([$_GET['id']]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            $mensaje = "❌ Usuario no encontrado";
            $tipoMensaje = 'danger';
        }
    } catch (PDOException $e) {
        $mensaje = "❌ Error: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);
        $idRoles = $_POST['idRoles'];
        $estado = $_POST['estado'];
        $password = $_POST['password'] ?? '';
        
        if ($esEditar) {
            // Actualizar usuario existente
            $idUsuario = $_POST['idUsuario'];
            
            if ($password != '') {
                // Con nueva contraseña
                $stmt = $pdo->prepare("
                    UPDATE usuario SET
                        Nombre = ?,
                        Apellido = ?,
                        Email = ?,
                        idRoles = ?,
                        PasswordHash = SHA2(?, 256),
                        Estado = ?
                    WHERE idUsuario = ?
                ");
                $stmt->execute([$nombre, $apellido, $email, $idRoles, $password, $estado, $idUsuario]);
            } else {
                // Sin cambiar contraseña
                $stmt = $pdo->prepare("
                    UPDATE usuario SET
                        Nombre = ?,
                        Apellido = ?,
                        Email = ?,
                        idRoles = ?,
                        Estado = ?
                    WHERE idUsuario = ?
                ");
                $stmt->execute([$nombre, $apellido, $email, $idRoles, $estado, $idUsuario]);
            }
            
            $mensaje = "✅ Usuario actualizado correctamente";
            $tipoMensaje = 'success';
            
            // Recargar datos
            $stmt = $pdo->prepare("
                SELECT u.*, r.NombreRol 
                FROM usuario u
                INNER JOIN roles r ON u.idRoles = r.idRoles
                WHERE u.idUsuario = ?
            ");
            $stmt->execute([$idUsuario]);
            $usuario = $stmt->fetch();
            
        } else {
            // Crear nuevo usuario
            if ($password == '') {
                throw new Exception("La contraseña es obligatoria para nuevos usuarios");
            }
            
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuario WHERE Email = ?");
            $stmt->execute([$email]);
            $existe = $stmt->fetch();
            
            if ($existe['total'] > 0) {
                throw new Exception("El email ya está registrado");
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO usuario (Nombre, Apellido, Email, PasswordHash, idRoles, Estado)
                VALUES (?, ?, ?, SHA2(?, 256), ?, ?)
            ");
            $stmt->execute([$nombre, $apellido, $email, $password, $idRoles, $estado]);
            
            $mensaje = "✅ Usuario creado correctamente";
            $tipoMensaje = 'success';
            
            // Limpiar formulario
            $usuario = null;
        }
        
    } catch (Exception $e) {
        $mensaje = "❌ Error: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $esEditar ? 'Editar Usuario' : 'Nuevo Usuario'; ?> - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }
        .container { max-width: 800px; margin: 0 auto; }
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.8rem;
            background: linear-gradient(135deg, <?php echo $esEditar ? '#f093fb 0%, #f5576c 100%' : '#43e97b 0%, #38f9d7 100%'; ?>);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card h2 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: #333;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 8px; display: flex; align-items: center; gap: 5px; }
        .form-group input, .form-group select {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-group input[readonly] {
            background: #f8f9fa;
            cursor: not-allowed;
        }
        .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f0; }
        .password-hint {
            font-size: 0.8rem;
            color: #888;
            margin-top: 5px;
        }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo $esEditar ? '✏️ Editar Usuario' : '➕ Nuevo Usuario'; ?></h1>
            <div>
                <a href="listausuarios.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Ver Todos
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <i class="fas fa-<?php echo $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (!$esEditar || $usuario): ?>
        <div class="card">
            <h2><i class="fas fa-user"></i> Información del Usuario</h2>
            
            <form method="POST">
                <?php if ($esEditar): ?>
                    <input type="hidden" name="idUsuario" value="<?php echo $usuario['idUsuario']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>👤 Nombre:</label>
                        <input type="text" name="nombre" 
                               value="<?php echo $esEditar ? htmlspecialchars($usuario['Nombre']) : ''; ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label>👤 Apellido:</label>
                        <input type="text" name="apellido" 
                               value="<?php echo $esEditar ? htmlspecialchars($usuario['Apellido']) : ''; ?>" 
                               required>
                    </div>

                    <div class="form-group full-width">
                        <label>📧 Email:</label>
                        <input type="email" name="email" 
                               value="<?php echo $esEditar ? htmlspecialchars($usuario['Email']) : ''; ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label>🔒 Contraseña:</label>
                        <input type="password" name="password" 
                               <?php echo $esEditar ? '' : 'required'; ?>
                               placeholder="<?php echo $esEditar ? 'Dejar vacío para no cambiar' : 'Mínimo 6 caracteres'; ?>">
                        <?php if ($esEditar): ?>
                            <span class="password-hint">Dejar vacío para mantener la contraseña actual</span>
                        <?php else: ?>
                            <span class="password-hint">Mínimo 6 caracteres</span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>🎭 Rol:</label>
                        <select name="idRoles" required>
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['idRoles']; ?>" 
                                    <?php echo ($esEditar && $usuario['idRoles'] == $r['idRoles']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($r['NombreRol']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>✅ Estado:</label>
                        <select name="estado" required>
                            <option value="Activo" <?php echo ($esEditar && $usuario['Estado'] === 'Activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="Inactivo" <?php echo ($esEditar && $usuario['Estado'] === 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="listausuarios.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn <?php echo $esEditar ? 'btn-primary' : 'btn-success'; ?>">
                        <i class="fas fa-save"></i> <?php echo $esEditar ? 'Actualizar' : 'Crear'; ?> Usuario
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>