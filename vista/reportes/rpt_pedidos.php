<?php
require_once '../../modelo/verificar_sesion.php';
solo_admin();
require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$idProv = $_GET['idProveedor'] ?? '';
$estado = $_GET['estado'] ?? '';

$stmtProveedores = $pdo->query("SELECT idProveedor, RazonSocial FROM Proveedor WHERE Estado='Activo'");
$proveedores = $stmtProveedores->fetchAll();

$stmt = $pdo->prepare("CALL sp_Informe_PedidosProveedor(?, ?)");
$stmt->execute([$idProv == '' ? null : $idProv, $estado]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos por Proveedor - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 30px 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: rgba(255,255,255,0.95); padding: 25px 30px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-back { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .filter { background: white; padding: 20px; border-radius: 15px; margin-bottom: 25px; display: flex; gap: 15px; align-items: end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px; }
        .form-group select { padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-filter { background: #fd7e14; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: #6c757d; color: white; padding: 15px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        tr:hover { background: #f8f9fa; }
        .badge { padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Pedidos por Proveedor</h1>
            <a href="reportes.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        <form method="GET" class="filter">
            <div class="form-group">
                <label>Proveedor:</label>
                <select name="idProveedor">
                    <option value="">Todos</option>
                    <?php foreach ($proveedores as $p): ?>
                        <option value="<?php echo $p['idProveedor']; ?>" <?php echo $idProv == $p['idProveedor'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['RazonSocial']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado:</label>
                <select name="estado">
                    <option value="">Todos</option>
                    <option value="Pendiente" <?php echo $estado == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Recibido" <?php echo $estado == 'Recibido' ? 'selected' : ''; ?>>Recibido</option>
                    <option value="Cancelado" <?php echo $estado == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filtrar</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Proveedor</th>
                    <th>Solicitado Por</th>
                    <th>Fecha</th>
                    <th>Factura Prov.</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: #888;">No se encontraron pedidos</td></tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $pe): 
                        $badgeClass = $pe['Estado']=='Recibido'?'badge-success':($pe['Estado']=='Pendiente'?'badge-warning':'badge-danger');
                    ?>
                    <tr>
                        <td><strong><?php echo $pe['idPedido']; ?></strong></td>
                        <td><?php echo htmlspecialchars($pe['Proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($pe['SolicitadoPor']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($pe['FechaPedido'])); ?></td>
                        <td><?php echo $pe['NumeroFacturaProveedor'] ?: 'N/A'; ?></td>
                        <td style="font-weight: 600;">$<?php echo number_format($pe['TotalPedido'], 2); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $pe['Estado']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>