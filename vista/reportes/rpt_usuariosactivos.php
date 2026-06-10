<?php
require_once '../../modelo/verificar_sesion.php';
solo_admin();
require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

// Consulta directa con JOIN para obtener el nombre del rol
$stmt = $pdo->query("
    SELECT 
        u.idUsuario,
        u.Nombre,
        u.Apellido,
        u.Email,
        u.Estado,
        r.NombreRol
    FROM usuario u
    INNER JOIN roles r ON u.idRoles = r.idRoles
    WHERE u.Estado = 'Activo'
    ORDER BY u.Nombre
");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Activos - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 30px 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: rgba(255,255,255,0.95); padding: 25px 30px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-back { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .btn-back:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: #007bff; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        tr:hover { background: #f8f9fa; }
        .badge { background: #28a745; color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Usuarios Activos</h1>
            <a href="reportes.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?php echo $u['idUsuario']; ?></strong></td>
                    <td><?php echo htmlspecialchars($u['Nombre'].' '.$u['Apellido']); ?></td>
                    <td><?php echo htmlspecialchars($u['Email']); ?></td>
                    <td><?php echo htmlspecialchars($u['NombreRol']); ?></td>
                    <td><span class="badge"><?php echo $u['Estado']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>