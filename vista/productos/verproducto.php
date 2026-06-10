<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
verificar_login();

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$producto = null;
$mensaje = '';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Producto WHERE idProducto = ?");
        $stmt->execute([$_GET['id']]);
        $producto = $stmt->fetch();
        
        if (!$producto) {
            $mensaje = "❌ Producto no encontrado";
        }
    } catch (PDOException $e) {
        $mensaje = "❌ Error: " . $e->getMessage();
    }
} else {
    $mensaje = "❌ No se especificó un producto";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Producto - Luxor</title>
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
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .product-detail { display: grid; gap: 20px; }
        .detail-row {
            display: flex;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .detail-label { font-weight: 600; color: #555; width: 40%; display: flex; align-items: center; gap: 10px; }
        .detail-value { color: #333; width: 60%; font-weight: 500; }
        .stock-indicator { display: inline-block; padding: 6px 12px; border-radius: 15px; font-weight: 600; font-size: 0.9rem; }
        .stock-good { background: #d4edda; color: #155724; }
        .stock-warning { background: #fff3cd; color: #856404; }
        .stock-danger { background: #f8d7da; color: #721c24; }
        .price-tag { font-size: 1.3rem; font-weight: 700; color: #28a745; }
        .actions { display: flex; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 2px solid #e9ecef; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍾 Detalle del Producto</h1>
            <a href="listaproductos.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($producto): ?>
        <div class="card">
            <div class="product-detail">
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-hashtag"></i> ID Producto:</div>
                    <div class="detail-value"><?php echo $producto['idProducto']; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-tag"></i> Nombre:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($producto['Nombre']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-wine-bottle"></i> Tipo de Licor:</div>
                    <div class="detail-value"><?php echo $producto['TipoLicor']; ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-shopping-cart"></i> Precio de Compra:</div>
                    <div class="detail-value">$<?php echo number_format($producto['PrecioCompra'], 2); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-dollar-sign"></i> Precio de Venta:</div>
                    <div class="detail-value"><span class="price-tag">$<?php echo number_format($producto['PrecioVenta'], 2); ?></span></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-calculator"></i> Utilidad:</div>
                    <div class="detail-value">
                        $<?php echo number_format($producto['PrecioVenta'] - $producto['PrecioCompra'], 2); ?>
                        (<?php echo round((($producto['PrecioVenta'] - $producto['PrecioCompra']) / $producto['PrecioCompra']) * 100, 2); ?>%)
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-boxes"></i> Stock Actual:</div>
                    <div class="detail-value">
                        <?php 
                        $stockClass = 'stock-good';
                        if ($producto['StockActual'] <= 0) $stockClass = 'stock-danger';
                        elseif ($producto['StockActual'] <= $producto['StockMinimo']) $stockClass = 'stock-warning';
                        ?>
                        <span class="stock-indicator <?php echo $stockClass; ?>">
                            <?php echo $producto['StockActual']; ?> unidades
                        </span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-exclamation-triangle"></i> Stock Mínimo:</div>
                    <div class="detail-value"><?php echo $producto['StockMinimo']; ?> unidades</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-calendar-alt"></i> Fecha de Vencimiento:</div>
                    <div class="detail-value">
                        <?php echo $producto['FechaVencimiento'] ? date('d/m/Y', strtotime($producto['FechaVencimiento'])) : 'No aplica'; ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label"><i class="fas fa-check-circle"></i> Estado:</div>
                    <div class="detail-value">
                        <span class="stock-indicator <?php echo $producto['Estado'] === 'Disponible' ? 'stock-good' : 'stock-danger'; ?>">
                            <?php echo $producto['Estado']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="actions">
                <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Almacenista')): ?>
                    <a href="frmeditarproducto.php?id=<?php echo $producto['idProducto']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                <?php endif; ?>
                <a href="listaproductos.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Volver al Listado
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>