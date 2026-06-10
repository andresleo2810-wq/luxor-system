<?php
require_once '../../modelo/verificar_sesion.php';
solo_admin();
require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$fechaInicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fechaFin = $_GET['fin'] ?? date('Y-m-d');

$stmt = $pdo->prepare("CALL sp_Informe_VentasPorFecha(?, ?)");
$stmt->execute([$fechaInicio, $fechaFin]);
$detalleVentas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas por Fecha - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 30px 20px; }
        .container { max-width: 1300px; margin: 0 auto; }
        .header { background: rgba(255,255,255,0.95); padding: 25px 30px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-back { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .filter { background: white; padding: 20px; border-radius: 15px; margin-bottom: 25px; display: flex; gap: 15px; align-items: end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px; }
        .form-group input { padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-filter { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: #17a2b8; color: white; padding: 15px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        tr:hover { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 Detalle por Rango de Fechas</h1>
            <a href="reportes.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        <form method="GET" class="filter">
            <div class="form-group">
                <label>Desde:</label>
                <input type="date" name="inicio" value="<?php echo $fechaInicio; ?>" required>
            </div>
            <div class="form-group">
                <label>Hasta:</label>
                <input type="date" name="fin" value="<?php echo $fechaFin; ?>" required>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filtrar</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Vendedor</th>
                    <th>Factura</th>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>P.Unit</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalleVentas as $dv): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($dv['FechaVenta'])); ?></td>
                    <td><?php echo htmlspecialchars($dv['Vendedor']); ?></td>
                    <td><?php echo $dv['NumeroFactura']; ?></td>
                    <td><?php echo htmlspecialchars($dv['Producto']); ?></td>
                    <td><?php echo $dv['Cantidad']; ?></td>
                    <td>$<?php echo number_format($dv['PrecioUnitario'], 2); ?></td>
                    <td style="font-weight: 600;">$<?php echo number_format($dv['Subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>