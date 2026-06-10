<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
solo_admin(); // Solo administradores

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

// Obtener parámetros
$buscar = $_GET['buscar'] ?? '';
$rol = $_GET['rol'] ?? '';
$estado = $_GET['estado'] ?? '';

try {
    $sql = "
        SELECT 
            u.idUsuario,
            u.Nombre,
            u.Apellido,
            u.Email,
            u.Estado,
            r.NombreRol,
            r.idRoles
        FROM usuario u
        INNER JOIN roles r ON u.idRoles = r.idRoles
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($buscar != '') {
        $sql .= " AND (u.Nombre LIKE ? OR u.Apellido LIKE ? OR u.Email LIKE ?)";
        $params[] = "%$buscar%";
        $params[] = "%$buscar%";
        $params[] = "%$buscar%";
    }
    
    if ($rol != '') {
        $sql .= " AND r.idRoles = ?";
        $params[] = $rol;
    }
    
    if ($estado != '') {
        $sql .= " AND u.Estado = ?";
        $params[] = $estado;
    }
    
    $sql .= " ORDER BY u.Nombre";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll();
    
    // Obtener roles para el filtro
    $stmtRoles = $pdo->query("SELECT idRoles, NombreRol FROM roles ORDER BY NombreRol");
    $roles = $stmtRoles->fetchAll();
    
} catch (PDOException $e) {
    $usuarios = [];
    $roles = [];
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }
        .container { max-width: 1300px; margin: 0 auto; }
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header h1 {
            font-size: 1.8rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .header-actions { display: flex; gap: 10px; align-items: center; }
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
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        .btn-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .btn-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .filter-form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px; }
        .form-group input, .form-group select {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-size: 0.85rem;
            color: #555;
            text-transform: uppercase;
            font-weight: 600;
        }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        tr:hover { background: #f8f9fa; }
        .badge { padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-admin { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .badge-vendedor { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .badge-almacenista { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        @media (max-width: 768px) {
            .filter-form { grid-template-columns: 1fr; }
            table { min-width: 900px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Gestión de Usuarios</h1>
            <div class="header-actions">
                <a href="frmusuario.php" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>🔍 Buscar:</label>
                    <input type="text" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>" placeholder="Nombre, apellido o email...">
                </div>
                <div class="form-group">
                    <label> Rol:</label>
                    <select name="rol">
                        <option value="">Todos</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?php echo $r['idRoles']; ?>" <?php echo $rol == $r['idRoles'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($r['NombreRol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="Activo" <?php echo $estado == 'Activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="Inactivo" <?php echo $estado == 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </form>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-users"></i> Usuarios del Sistema</h2>
                <span><?php echo count($usuarios); ?> usuarios</span>
            </div>

            <?php if (empty($usuarios)): ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash" style="font-size: 4rem; color: #ddd;"></i>
                    <h3>No se encontraron usuarios</h3>
                    <p>Intenta con otros filtros de búsqueda</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): 
                            $badgeClass = '';
                            if (stripos($u['NombreRol'], 'admin') !== false) $badgeClass = 'badge-admin';
                            elseif (stripos($u['NombreRol'], 'vendedor') !== false) $badgeClass = 'badge-vendedor';
                            elseif (stripos($u['NombreRol'], 'almacen') !== false) $badgeClass = 'badge-almacenista';
                            else $badgeClass = 'badge-success';
                        ?>
                            <tr>
                                <td><strong><?php echo $u['idUsuario']; ?></strong></td>
                                <td><?php echo htmlspecialchars($u['Nombre'] . ' ' . $u['Apellido']); ?></td>
                                <td><?php echo htmlspecialchars($u['Email']); ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $u['NombreRol']; ?></span></td>
                                <td>
                                    <span class="badge <?php echo $u['Estado'] === 'Activo' ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo $u['Estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="frmusuario.php?id=<?php echo $u['idUsuario']; ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 0.8rem;">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>