<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
verificar_login();

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$factura = null;
$detalles = [];
$mensaje = '';

if (isset($_GET['id'])) {
    try {
        // Obtener datos de la factura
        $stmt = $pdo->prepare("
            SELECT 
                v.idVenta,
                v.NumeroFactura,
                v.FechaVenta,
                v.Total,
                v.Estado,
                u.Nombre as Vendedor,
                c.Nombre as Cliente
            FROM Ventas v
            LEFT JOIN Usuario u ON v.idUsuario = u.idUsuario
            LEFT JOIN Cliente c ON v.idCliente = c.idCliente
            WHERE v.idVenta = ?
        ");
        $stmt->execute([$_GET['id']]);
        $factura = $stmt->fetch();

        // Obtener detalles
        if ($factura) {
            $stmt = $pdo->prepare("
                SELECT 
                    dv.Cantidad,
                    dv.PrecioUnitario,
                    dv.Subtotal,
                    p.Nombre as Producto,
                    p.TipoLicor
                FROM DetalleVenta dv
                INNER JOIN Producto p ON dv.idProducto = p.idProducto
                WHERE dv.idVenta = ?
            ");
            $stmt->execute([$_GET['id']]);
            $detalles = $stmt->fetchAll();
        } else {
            $mensaje = "Factura no encontrada";
        }
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Factura - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }
        .container { max-width: 900px; margin: 0 auto; }
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .invoice-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 20px;
        }
        .invoice-header h2 {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 10px;
        }
        .invoice-header .numero {
            font-size: 1.2rem;
            color: #666;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .info-box h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-box p {
            margin: 5px 0;
            color: #333;
        }
        .info-box strong {
            color: #667eea;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-size: 0.85rem;
            color: #555;
            text-transform: uppercase;
        }
        td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .total-final {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .actions { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        @media print {
            body { background: white; }
            .btn, .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧾 Detalle de Factura</h1>
            <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="listafacturas.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($factura): ?>
        <div class="card">
            <div class="invoice-header">
                <h2>🍾 LUXOR</h2>
                <p class="numero">Factura N° <?php echo $factura['NumeroFactura']; ?></p>
                <p style="color: #888; margin-top: 10px;">
                    <?php echo date('d/m/Y H:i', strtotime($factura['FechaVenta'])); ?>
                </p>
                <p style="margin-top: 10px;">
                    <span class="badge <?php echo $factura['Estado'] === 'Completada' ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $factura['Estado']; ?>
                    </span>
                </p>
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <h3>Vendedor</h3>
                    <p><strong><?php echo htmlspecialchars($factura['Vendedor'] ?? 'N/A'); ?></strong></p>
                </div>
                <div class="info-box">
                    <h3>Cliente</h3>
                    <p><strong><?php echo htmlspecialchars($factura['Cliente'] ?? 'Consumidor Final'); ?></strong></p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>P. Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['Producto']); ?></td>
                        <td><?php echo $d['TipoLicor']; ?></td>
                        <td><?php echo $d['Cantidad']; ?></td>
                        <td>$<?php echo number_format($d['PrecioUnitario'], 2); ?></td>
                        <td style="font-weight: 600;">$<?php echo number_format($d['Subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-final">
                <span>TOTAL:</span>
                <span>$<?php echo number_format($factura['Total'], 2); ?></span>
            </div>

            <div class="actions">
                <a href="listafacturas.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Volver al Listado
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>