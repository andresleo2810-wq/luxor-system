<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
verificar_login();

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

// Obtener parámetros de búsqueda
$buscar = $_GET['buscar'] ?? '';
$tipoLicor = $_GET['tipoLicor'] ?? '';
$estado = $_GET['estado'] ?? '';

try {
    $sql = "SELECT * FROM Producto WHERE 1=1";
    $params = [];
    
    if ($buscar != '') {
        $sql .= " AND (Nombre LIKE ? OR TipoLicor LIKE ?)";
        $params[] = "%$buscar%";
        $params[] = "%$buscar%";
    }
    
    if ($tipoLicor != '') {
        $sql .= " AND TipoLicor = ?";
        $params[] = $tipoLicor;
    }
    
    if ($estado != '') {
        $sql .= " AND Estado = ?";
        $params[] = $estado;
    }
    
    $sql .= " ORDER BY Nombre";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $productos = [];
    $error = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos - Luxor</title>
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
        .stock-badge { padding: 4px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; }
        .stock-ok { background: #d4edda; color: #155724; }
        .stock-low { background: #fff3cd; color: #856404; }
        .stock-critical { background: #f8d7da; color: #721c24; }
        .price { font-weight: 600; color: #28a745; }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        @media (max-width: 768px) {
            .filter-form { grid-template-columns: 1fr; }
            table { min-width: 800px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍾 Listado de Productos</h1>
            <div class="header-actions">
                <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Almacenista')): ?>
                    <a href="frmagregarproducto.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar
                    </a>
                <?php endif; ?>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>🔍 Buscar producto:</label>
                    <input type="text" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>" placeholder="Nombre o tipo de licor...">
                </div>
                <div class="form-group">
                    <label>🍾 Tipo de Licor:</label>
                    <select name="tipoLicor">
                        <option value="">Todos</option>
                        <option value="Ron" <?php echo $tipoLicor == 'Ron' ? 'selected' : ''; ?>>Ron</option>
                        <option value="Vino" <?php echo $tipoLicor == 'Vino' ? 'selected' : ''; ?>>Vino</option>
                        <option value="Cerveza" <?php echo $tipoLicor == 'Cerveza' ? 'selected' : ''; ?>>Cerveza</option>
                        <option value="Whisky" <?php echo $tipoLicor == 'Whisky' ? 'selected' : ''; ?>>Whisky</option>
                        <option value="Vodka" <?php echo $tipoLicor == 'Vodka' ? 'selected' : ''; ?>>Vodka</option>
                        <option value="Tequila" <?php echo $tipoLicor == 'Tequila' ? 'selected' : ''; ?>>Tequila</option>
                        <option value="Aguardiente" <?php echo $tipoLicor == 'Aguardiente' ? 'selected' : ''; ?>>Aguardiente</option>
                        <option value="Licor" <?php echo $tipoLicor == 'Licor' ? 'selected' : ''; ?>>Licor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="Disponible" <?php echo $estado == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                        <option value="Agotado" <?php echo $estado == 'Agotado' ? 'selected' : ''; ?>>Agotado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </form>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-wine-bottle"></i> Productos</h2>
                <span><?php echo count($productos); ?> productos</span>
            </div>

            <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: #ddd;"></i>
                    <h3>No se encontraron productos</h3>
                    <p>Intenta con otros filtros de búsqueda</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>P. Compra</th>
                            <th>P. Venta</th>
                            <th>Stock</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): 
                            $stockClass = 'stock-ok';
                            if ($p['StockActual'] <= 0) {
                                $stockClass = 'stock-critical';
                            } elseif ($p['StockActual'] <= $p['StockMinimo']) {
                                $stockClass = 'stock-low';
                            }
                        ?>
                            <tr>
                                <td><strong><?php echo $p['idProducto']; ?></strong></td>
                                <td><?php echo htmlspecialchars($p['Nombre']); ?></td>
                                <td><?php echo $p['TipoLicor']; ?></td>
                                <td>$<?php echo number_format($p['PrecioCompra'], 2); ?></td>
                                <td class="price">$<?php echo number_format($p['PrecioVenta'], 2); ?></td>
                                <td>
                                    <span class="stock-badge <?php echo $stockClass; ?>">
                                        <?php echo $p['StockActual']; ?> und
                                    </span>
                                </td>
                                <td><?php echo $p['FechaVencimiento'] ? date('d/m/Y', strtotime($p['FechaVencimiento'])) : '-'; ?></td>
                                <td><?php echo $p['Estado']; ?></td>
                                <td>
                                    <a href="verproducto.php?id=<?php echo $p['idProducto']; ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.8rem;">
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