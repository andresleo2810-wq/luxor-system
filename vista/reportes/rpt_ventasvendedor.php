<?php
require_once '../../modelo/verificar_sesion.php';
solo_admin();
require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$stmt = $pdo->query("CALL sp_Informe_VentasPorVendedor()");
$ventas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas por Vendedor - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 30px 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: rgba(255,255,255,0.95); padding: 25px 30px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-back { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-back:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; text-align: left; font-weight: 600; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        tr:hover { background: #f8f9fa; }
        .empty { text-align: center; padding: 60px; color: #888; background: white; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Rendimiento por Vendedor</h1>
            <a href="reportes.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        <?php if (empty($ventas)): ?>
            <div class="empty"><i class="fas fa-chart-bar" style="font-size: 4rem; color: #ddd; margin-bottom: 15px;"></i><h3>No hay ventas registradas</h3></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Vendedor</th>
                    <th>Cantidad Ventas</th>
                    <th>Total Vendido</th>
                    <th>Promedio</th>
                    <th>Última Venta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $v): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($v['Vendedor']); ?></strong></td>
                    <td><?php echo $v['CantidadVentas']; ?></td>
                    <td style="color: #28a745; font-weight: 600;">$<?php echo number_format($v['TotalDinero'], 2); ?></td>
                    <td>$<?php echo number_format($v['CantidadVentas'] > 0 ? $v['TotalDinero']/$v['CantidadVentas'] : 0, 2); ?></td>
                    <td><?php echo $v['UltimaVenta'] ? date('d/m/Y', strtotime($v['UltimaVenta'])) : 'N/A'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>