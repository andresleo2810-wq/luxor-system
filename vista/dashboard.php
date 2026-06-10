<?php 
session_start();

if (!isset($_SESSION['logueado'])) {
    header("Location: login.php");
    exit;
}

// 🔒 Verificar rol del usuario
$es_admin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador';

// Valores por defecto
$stats = [
    'ventas_hoy' => ['total' => 0, 'monto' => 0],
    'stock_bajo' => 0,
    'usuarios_activos' => 0,
    'caja_abierta' => 0
];

$error_db = "";

// Intentar conectar
try {
    // Verificar si config.php existe
    if (!file_exists('../config.php')) {
        throw new Exception("No se encuentra config.php");
    }
    
    require_once '../config.php';
    
    // Verificar si conexion.php existe
    if (!file_exists('../modelo/conexion.php')) {
        throw new Exception("No se encuentra modelo/conexion.php");
    }
    
    require_once '../modelo/conexion.php';
    
    $conexion = new Conexion();
    $pdo = $conexion->conectar();
    
    // 1. Ventas de hoy
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total, COALESCE(SUM(Total), 0) as monto 
        FROM Ventas 
        WHERE DATE(FechaVenta) = CURDATE() AND Estado = 'Completada'
    ");
    $stmt->execute();
    $ventas = $stmt->fetch();
    $stats['ventas_hoy'] = $ventas;
    
    // 2. Productos con stock bajo
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM Producto 
        WHERE StockActual <= StockMinimo AND Estado = 'Disponible'
    ");
    $stats['stock_bajo'] = $stmt->fetch()['total'];
    
    // 3. Usuarios activos (solo admin puede ver esto)
    if ($es_admin) {
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM Usuario 
            WHERE Estado = 'Activo'
        ");
        $stats['usuarios_activos'] = $stmt->fetch()['total'];
    }
    
    // 4. Cajas abiertas
    $stmt = $pdo->query("
        SELECT COUNT(*) as total 
        FROM Caja 
        WHERE Estado = 'Abierta'
    ");
    $stats['caja_abierta'] = $stmt->fetch()['total'];
    
} catch (Exception $e) {
    $error_db = "⚠️ " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        /* Header Superior */
        .top-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand h1 {
            font-size: 1.6rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .brand-icon {
            font-size: 2rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-info .name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        /* 🔒 Badge de rol con colores */
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 3px;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .role-badge.vendedor {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .role-badge.almacenista {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .btn-logout {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
        }

        /* Contenedor Principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Bienvenida */
        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .welcome-section h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 5px;
        }

        .welcome-section p {
            color: #666;
            font-size: 1rem;
        }

        .current-date {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            font-weight: 500;
        }

        /* Estadísticas Rápidas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-icon.ventas { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-icon.stock { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .stat-icon.usuarios { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .stat-icon.caja { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }

        .stat-info h3 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 3px;
        }

        .stat-info p {
            color: #888;
            font-size: 0.9rem;
        }

        /* Grid de Módulos */
        .modules-section {
            margin-bottom: 30px;
        }

        .modules-section h3 {
            color: white;
            font-size: 1.4rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .module-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
        }

        .module-card:nth-child(1) { --color-start: #667eea; --color-end: #764ba2; }
        .module-card:nth-child(2) { --color-start: #f093fb; --color-end: #f5576c; }
        .module-card:nth-child(3) { --color-start: #4facfe; --color-end: #00f2fe; }
        .module-card:nth-child(4) { --color-start: #43e97b; --color-end: #38f9d7; }
        .module-card:nth-child(5) { --color-start: #fa709a; --color-end: #fee140; }

        .module-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }

        .module-card:hover .module-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .module-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
            transition: all 0.3s;
            background: linear-gradient(135deg, var(--color-start), var(--color-end));
            color: white;
        }

        .module-card h3 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #333;
        }

        .module-card p {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .module-arrow {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 1.5rem;
            color: var(--color-start);
            opacity: 0;
            transition: all 0.3s;
        }

        .module-card:hover .module-arrow {
            opacity: 1;
            transform: translateX(5px);
        }

        /* 🔒 Badge de solo admin */
        .admin-only-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .user-menu {
                flex-direction: column;
                width: 100%;
            }

            .user-info {
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-card, .module-card {
            animation: fadeIn 0.5s ease-out;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Header Superior -->
    <header class="top-header">
        <div class="brand">
            <span class="brand-icon"></span>
            <h1>Luxor</h1>
        </div>
        <div class="user-menu">
            <div class="user-info">
                <div class="name"><?php echo $_SESSION['nombre']; ?></div>
                <span class="role-badge <?php echo strtolower($_SESSION['rol']); ?>">
                    <i class="fas fa-<?php echo $es_admin ? 'shield-alt' : 'user'; ?>"></i>
                    <?php echo $_SESSION['rol']; ?>
                </span>
            </div>
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
            </div>
            <a href="../controlador/auth/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </header>

    <!-- Contenedor Principal -->
    <div class="container">
        <!-- Sección de Bienvenida -->
        <div class="welcome-section">
            <h2> Bienvenido al Sistema</h2>
            <div class="current-date">
                 <?php echo date('d \d\e F \d\e\l Y - h:i A'); ?>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon ventas">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['ventas_hoy']['total']; ?></h3>
                    <p>Ventas Hoy</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stock">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['stock_bajo']; ?></h3>
                    <p>Stock Bajo</p>
                </div>
            </div>

            <!--  SOLO ADMIN ve estadística de usuarios -->
            <?php if ($es_admin): ?>
            <div class="stat-card">
                <div class="stat-icon usuarios">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['usuarios_activos']; ?></h3>
                    <p>Usuarios Activos</p>
                </div>
            </div>
            <?php endif; ?>

            <div class="stat-card">
                <div class="stat-icon caja">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['caja_abierta']; ?></h3>
                    <p>Cajas Abiertas</p>
                </div>
            </div>
        </div>

        <!--  MÓDULOS COMUNES (Todos los usuarios) -->
        <div class="modules-section">
            <h3> Módulos del Sistema</h3>
            <div class="modules-grid">
                <a href="facturas/listafacturas.php" class="module-card">
                    <div class="module-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3>Ventas / Facturas</h3>
                    <p>Registra nuevas ventas, consulta el historial y genera tickets de venta</p>
                    <span class="module-arrow"><i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="productos/listaproductos.php" class="module-card">
                    <div class="module-icon">
                        <i class="fas fa-wine-bottle"></i>
                    </div>
                    <h3>Productos</h3>
                    <p>Gestiona el inventario, actualiza precios y controla el stock de licores</p>
                    <span class="module-arrow"><i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="caja/historialcajas.php" class="module-card">
                    <div class="module-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3>Caja</h3>
                    <p>Abre y cierra cajas, controla movimientos y realiza arqueos</p>
                    <span class="module-arrow"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>

        <!--  MÓDULOS SOLO PARA ADMINISTRADOR -->
        <?php if ($es_admin): ?>
        <div class="modules-section">
            <h3> Módulos Administrativos <small style="font-size: 0.8rem; opacity: 0.8;">(Solo Administradores)</small></h3>
            <div class="modules-grid">
                <a href="reportes/reportes.php" class="module-card">
                    <span class="admin-only-badge">
                        <i class="fas fa-lock"></i> Admin
                    </span>
                    <div class="module-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Reportes</h3>
                    <p>Analiza el rendimiento con informes de ventas, stock y proveedores</p>
                    <span class="module-arrow"><i class="fas fa-arrow-right"></i></span>
                </a>

                <a href="usuarios/listausuarios.php" class="module-card">
                    <span class="admin-only-badge">
                        <i class="fas fa-lock"></i> Admin
                    </span>
                    <div class="module-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h3>Usuarios</h3>
                    <p>Administra el personal, roles de acceso y permisos del sistema</p>
                    <span class="module-arrow"><i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Luxor - Sistema de Ventas de Licores | Todos los derechos reservados</p>
    </footer>
</body>
</html>