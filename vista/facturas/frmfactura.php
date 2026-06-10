<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../modelo/verificar_sesion.php';
verificar_login();

require_once '../../modelo/conexion.php';
$pdo = (new Conexion())->conectar();

$mensaje = '';
$tipoMensaje = '';
$productos = [];
$clientes = [];

// Obtener productos disponibles
try {
    $stmt = $pdo->query("SELECT idProducto, Nombre, PrecioVenta, StockActual FROM Producto WHERE Estado = 'Disponible' AND StockActual > 0 ORDER BY Nombre");
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $productos = [];
}

// Obtener clientes
try {
    $stmt = $pdo->query("SELECT idCliente, Nombre FROM Cliente WHERE Estado = 'Activo' ORDER BY Nombre");
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $clientes = [];
}

// Obtener caja abierta
try {
    $stmt = $pdo->query("SELECT idCaja FROM Caja WHERE Estado = 'Abierta' LIMIT 1");
    $cajaAbierta = $stmt->fetch();
} catch (PDOException $e) {
    $cajaAbierta = null;
}

// Procesar venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    try {
        $idCliente = $_POST['idCliente'] ?: null;
        $idUsuario = $_SESSION['idUsuario'];
        $idCaja = $cajaAbierta['idCaja'] ?? null;
        $items = $_POST['items'] ?? [];
        
        if (empty($items)) {
            throw new Exception("Debe agregar al menos un producto");
        }
        
        if (!$cajaAbierta) {
            throw new Exception("No hay una caja abierta. Debe abrir caja primero");
        }
        
        // Calcular total
        $total = 0;
        foreach ($items as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        // Generar número de factura
        $numeroFactura = 'F-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insertar venta
        $stmt = $pdo->prepare("
            INSERT INTO Ventas (NumeroFactura, idUsuario, idCliente, idCaja, FechaVenta, Total, Estado)
            VALUES (?, ?, ?, ?, NOW(), ?, 'Completada')
        ");
        $stmt->execute([$numeroFactura, $idUsuario, $idCliente, $idCaja, $total]);
        $idVenta = $pdo->lastInsertId();
        
        // Insertar detalles y actualizar stock
        $stmtDetalle = $pdo->prepare("
            INSERT INTO DetalleVenta (idVenta, idProducto, Cantidad, PrecioUnitario, Subtotal)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmtStock = $pdo->prepare("
            UPDATE Producto SET StockActual = StockActual - ? WHERE idProducto = ? AND StockActual >= ?
        ");
        
        foreach ($items as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            
            $stmtDetalle->execute([
                $idVenta,
                $item['idProducto'],
                $item['cantidad'],
                $item['precio'],
                $subtotal
            ]);
            
            $stmtStock->execute([$item['cantidad'], $item['idProducto'], $item['cantidad']]);
        }
        
        $mensaje = "✅ Venta registrada exitosamente. Factura: $numeroFactura";
        $tipoMensaje = 'success';
        
    } catch (Exception $e) {
        $mensaje = "❌ Error: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
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
        .btn-danger { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .alert-danger { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card h2 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #333;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .form-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px; align-items: end; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 0.85rem; font-weight: 600; color: #555; margin-bottom: 5px; }
        .form-group input, .form-group select {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th { background: #f8f9fa; padding: 12px; text-align: left; font-size: 0.85rem; color: #555; }
        .items-table td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .total-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-box .label { font-size: 1.2rem; }
        .total-box .amount { font-size: 2rem; font-weight: 700; }
        .form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .btn-remove { background: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; border: none; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Nueva Venta</h1>
            <div>
                <a href="listafacturas.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Ver Facturas
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (!$cajaAbierta): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>¡Atención!</strong> No hay una caja abierta. Debe abrir caja antes de registrar ventas.
            </div>
        <?php else: ?>

        <form method="POST" id="formVenta">
            <!-- Datos de la venta -->
            <div class="card">
                <h2><i class="fas fa-user"></i> Datos de la Venta</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>👤 Cliente (opcional):</label>
                        <select name="idCliente">
                            <option value="">Consumidor Final</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?php echo $c['idCliente']; ?>">
                                    <?php echo htmlspecialchars($c['Nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>👨💼 Vendedor:</label>
                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- Agregar productos -->
            <div class="card">
                <h2><i class="fas fa-plus-circle"></i> Agregar Productos</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Producto:</label>
                        <select id="selectProducto">
                            <option value="">-- Seleccione --</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?php echo $p['idProducto']; ?>" 
                                        data-precio="<?php echo $p['PrecioVenta']; ?>"
                                        data-stock="<?php echo $p['StockActual']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($p['Nombre']); ?>">
                                    <?php echo htmlspecialchars($p['Nombre']); ?> (Stock: <?php echo $p['StockActual']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad:</label>
                        <input type="number" id="inputCantidad" min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label>Precio Unit.:</label>
                        <input type="number" id="inputPrecio" step="0.01" readonly>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="agregarProducto()">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>

                <!-- Tabla de items -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>P. Unitario</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Items se agregan dinámicamente -->
                    </tbody>
                </table>

                <div class="total-box">
                    <span class="label">TOTAL:</span>
                    <span class="amount" id="totalVenta">$0.00</span>
                </div>

                <!-- Items ocultos para enviar -->
                <div id="itemsHidden"></div>

                <div class="form-actions">
                    <a href="listafacturas.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" name="registrar" class="btn btn-success">
                        <i class="fas fa-save"></i> Registrar Venta
                    </button>
                </div>
            </div>
        </form>

        <?php endif; ?>
    </div>

    <script>
        let items = [];
        let total = 0;

        document.getElementById('selectProducto').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                document.getElementById('inputPrecio').value = option.dataset.precio;
                document.getElementById('inputCantidad').max = option.dataset.stock;
            } else {
                document.getElementById('inputPrecio').value = '';
            }
        });

        function agregarProducto() {
            const select = document.getElementById('selectProducto');
            const cantidad = parseInt(document.getElementById('inputCantidad').value);
            const precio = parseFloat(document.getElementById('inputPrecio').value);
            
            if (!select.value || !cantidad || !precio) {
                alert('Seleccione un producto y verifique la cantidad');
                return;
            }

            const option = select.options[select.selectedIndex];
            const stock = parseInt(option.dataset.stock);
            
            if (cantidad > stock) {
                alert('No hay suficiente stock disponible');
                return;
            }

            const item = {
                idProducto: select.value,
                nombre: option.dataset.nombre,
                cantidad: cantidad,
                precio: precio,
                subtotal: cantidad * precio
            };

            items.push(item);
            renderItems();
            
            // Reset
            select.value = '';
            document.getElementById('inputPrecio').value = '';
            document.getElementById('inputCantidad').value = 1;
        }

        function renderItems() {
            const tbody = document.getElementById('itemsBody');
            const hidden = document.getElementById('itemsHidden');
            tbody.innerHTML = '';
            hidden.innerHTML = '';
            total = 0;

            items.forEach((item, index) => {
                total += item.subtotal;
                
                tbody.innerHTML += `
                    <tr>
                        <td>${item.nombre}</td>
                        <td>${item.cantidad}</td>
                        <td>$${item.precio.toFixed(2)}</td>
                        <td>$${item.subtotal.toFixed(2)}</td>
                        <td><button type="button" class="btn-remove" onclick="removerItem(${index})"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `;

                hidden.innerHTML += `
                    <input type="hidden" name="items[${index}][idProducto]" value="${item.idProducto}">
                    <input type="hidden" name="items[${index}][nombre]" value="${item.nombre}">
                    <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="items[${index}][precio]" value="${item.precio}">
                `;
            });

            document.getElementById('totalVenta').textContent = '$' + total.toFixed(2);
        }

        function removerItem(index) {
            items.splice(index, 1);
            renderItems();
        }
    </script>
</body>
</html>