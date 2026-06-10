<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
verificar_login();

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

// Obtener parámetros
$fechaInicio = $_GET['fechaInicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fechaFin = $_GET['fechaFin'] ?? date('Y-m-d');
$estado = $_GET['estado'] ?? '';
$vendedor = $_GET['vendedor'] ?? '';

try {
    $sql = "
        SELECT 
            v.idVenta,
            v.NumeroFactura,
            v.FechaVenta,
            v.Total,
            v.Estado,
            v.idCaja,
            u.Nombre as Vendedor,
            c.Nombre as Cliente
        FROM Ventas v
        LEFT JOIN Usuario u ON v.idUsuario = u.idUsuario
        LEFT JOIN Cliente c ON v.idCliente = c.idCliente
        WHERE DATE(v.FechaVenta) BETWEEN ? AND ?
    ";
    
    $params = [$fechaInicio, $fechaFin];
    
    if ($estado != '') {
        $sql .= " AND v.Estado = ?";
        $params[] = $estado;
    }
    
    if ($vendedor != '') {
        $sql .= " AND v.idUsuario = ?";
        $params[] = $vendedor;
    }
    
    $sql .= " ORDER BY v.FechaVenta DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $facturas = $stmt->fetchAll();
    
    // Obtener vendedores para el filtro
    $stmtVend = $pdo->query("SELECT idUsuario, Nombre FROM Usuario WHERE idRoles IN (2, 3) AND Estado = 'Activo' ORDER BY Nombre");
    $vendedores = $stmtVend->fetchAll();
    
} catch (PDOException $e) {
    $facturas = [];
    $vendedores = [];
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Facturas - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
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
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
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
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .total { font-weight: 700; color: #28a745; font-size: 1.1rem; }
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
            <h1>🧾 Listado de Facturas</h1>
            <div class="header-actions">
                <a href="frmfactura.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nueva Venta
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label> Fecha Inicio:</label>
                    <input type="date" name="fechaInicio" value="<?php echo $fechaInicio; ?>">
                </div>
                <div class="form-group">
                    <label> Fecha Fin:</label>
                    <input type="date" name="fechaFin" value="<?php echo $fechaFin; ?>">
                </div>
                <div class="form-group">
                    <label>👤 Vendedor:</label>
                    <select name="vendedor">
                        <option value="">Todos</option>
                        <?php foreach ($vendedores as $v): ?>
                            <option value="<?php echo $v['idUsuario']; ?>" <?php echo $vendedor == $v['idUsuario'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($v['Nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="Completada" <?php echo $estado == 'Completada' ? 'selected' : ''; ?>>Completada</option>
                        <option value="Anulada" <?php echo $estado == 'Anulada' ? 'selected' : ''; ?>>Anulada</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </form>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-receipt"></i> Facturas Registradas</h2>
                <span><?php echo count($facturas); ?> facturas</span>
            </div>

            <?php if (empty($facturas)): ?>
                <div class="empty-state">
                    <i class="fas fa-receipt" style="font-size: 4rem; color: #ddd;"></i>
                    <h3>No se encontraron facturas</h3>
                    <p>Intenta con otros filtros de fecha</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>N° Factura</th>
                            <th>Fecha</th>
                            <th>Vendedor</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facturas as $f): 
                            $badgeClass = $f['Estado'] === 'Completada' ? 'badge-success' : 'badge-danger';
                        ?>
                            <tr>
                                <td><strong><?php echo $f['NumeroFactura']; ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($f['FechaVenta'])); ?></td>
                                <td><?php echo htmlspecialchars($f['Vendedor'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($f['Cliente'] ?? 'Consumidor Final'); ?></td>
                                <td class="total">$<?php echo number_format($f['Total'], 2); ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $f['Estado']; ?></span></td>
                                <td>
                                    <a href="verfactura.php?id=<?php echo $f['idVenta']; ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.8rem;">
                                        <i class="fas fa-eye"></i> Ver
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