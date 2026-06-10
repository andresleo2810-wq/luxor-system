<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
verificar_login();

// 🔒 Solo Admin y Almacenista
$rol = $_SESSION['rol'] ?? '';
if ($rol !== 'Administrador' && $rol !== 'Almacenista') {
    header("Location: ../../dashboard.php?error=acceso_denegado");
    exit;
}

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$mensaje = '';
$tipoMensaje = '';

// Procesar inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    try {
        $nombre = trim($_POST['nombre']);
        $tipoLicor = $_POST['tipoLicor'];
        $precioCompra = $_POST['precioCompra'];
        $precioVenta = $_POST['precioVenta'];
        $stockActual = $_POST['stockActual'];
        $stockMinimo = $_POST['stockMinimo'];
        $fechaVencimiento = $_POST['fechaVencimiento'] ?: null;
        $estado = $_POST['estado'];

        $stmt = $pdo->prepare("
            INSERT INTO Producto (Nombre, TipoLicor, PrecioCompra, PrecioVenta, StockActual, StockMinimo, FechaVencimiento, Estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $nombre,
            $tipoLicor,
            $precioCompra,
            $precioVenta,
            $stockActual,
            $stockMinimo,
            $fechaVencimiento,
            $estado
        ]);

        $mensaje = "✅ Producto agregado correctamente";
        $tipoMensaje = 'success';
        
    } catch (PDOException $e) {
        $mensaje = "❌ Error al agregar: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Luxor</title>
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
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
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
        .btn-success { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card h2 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            color: #333;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 8px; display: flex; align-items: center; gap: 5px; }
        .form-group input, .form-group select {
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #43e97b;
            box-shadow: 0 0 0 3px rgba(67, 233, 123, 0.1);
        }
        .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f0; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Agregar Producto</h1>
            <div>
                <a href="listaproductos.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Ver Todos
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <i class="fas fa-<?php echo $tipoMensaje === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2><i class="fas fa-plus-circle"></i> Nuevo Producto</h2>
            
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>🏷️ Nombre del Producto:</label>
                        <input type="text" name="nombre" required placeholder="Ej: Ron Cartavio Negro">
                    </div>

                    <div class="form-group">
                        <label>🍾 Tipo de Licor:</label>
                        <select name="tipoLicor" required>
                            <option value="">-- Seleccione --</option>
                            <option value="Ron">Ron</option>
                            <option value="Vino">Vino</option>
                            <option value="Cerveza">Cerveza</option>
                            <option value="Whisky">Whisky</option>
                            <option value="Vodka">Vodka</option>
                            <option value="Tequila">Tequila</option>
                            <option value="Aguardiente">Aguardiente</option>
                            <option value="Licor">Licor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>💵 Precio de Compra:</label>
                        <input type="number" name="precioCompra" step="0.01" min="0" required placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>💰 Precio de Venta:</label>
                        <input type="number" name="precioVenta" step="0.01" min="0" required placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>📦 Stock Actual:</label>
                        <input type="number" name="stockActual" min="0" required placeholder="0">
                    </div>

                    <div class="form-group">
                        <label>⚠️ Stock Mínimo:</label>
                        <input type="number" name="stockMinimo" min="0" required placeholder="5">
                    </div>

                    <div class="form-group">
                        <label>📅 Fecha de Vencimiento:</label>
                        <input type="date" name="fechaVencimiento">
                    </div>

                    <div class="form-group">
                        <label>✅ Estado:</label>
                        <select name="estado" required>
                            <option value="Disponible">Disponible</option>
                            <option value="Agotado">Agotado</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="listaproductos.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" name="agregar" class="btn btn-success">
                        <i class="fas fa-save"></i> Agregar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>