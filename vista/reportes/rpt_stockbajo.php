<?php
require_once '../../modelo/verificar_sesion.php';
solo_admin();
require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$stmt = $pdo->query("CALL sp_Informe_StockBajo()");
$stock = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock Bajo - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 30px 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: rgba(255,255,255,0.95); padding: 25px 30px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-back { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .alert { background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin-bottom: 25px; border-left: 5px solid #28a745; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        th { background: #dc3545; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; }
        tr:hover { background: #f8f9fa; }
        .badge { background: #f8d7da; color: #721c24; padding: 5px 12px; border-radius: 15px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Productos con Stock Bajo</h1>
            <a href="reportes.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>
        <?php if (empty($stock)): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> <strong>¡Excelente!</strong> Todos los productos tienen stock suficiente.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Código</th>
                    <th>Stock Actual</th>
                    <th>Stock Mínimo</th>
                    <th>Faltante</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stock as $s): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($s['Producto']); ?></strong></td>
                    <td><?php echo $s['CodigoBarras']; ?></td>
                    <td><span class="badge"><?php echo $s['StockActual']; ?></span></td>
                    <td><?php echo $s['StockMinimo']; ?></td>
                    <td style="color: #dc3545; font-weight: 600;">-<?php echo $s['CantidadFaltante']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>