<?php
require_once '../../modelo/verificar_sesion.php';
solo_admin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Luxor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left h1 {
            font-size: 1.8rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .header-left p {
            color: #666;
            font-size: 0.95rem;
        }

        .header-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .user-badge {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        /* Alerta informativa */
        .alert-info {
            background: rgba(255, 255, 255, 0.95);
            border-left: 5px solid #17a2b8;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            color: #0c5460;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-info i {
            font-size: 1.5rem;
            color: #17a2b8;
        }

        /* Grid de Reportes */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .report-card {
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

        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
        }

        .report-card:nth-child(1) { --color-start: #667eea; --color-end: #764ba2; }
        .report-card:nth-child(2) { --color-start: #f093fb; --color-end: #f5576c; }
        .report-card:nth-child(3) { --color-start: #4facfe; --color-end: #00f2fe; }
        .report-card:nth-child(4) { --color-start: #43e97b; --color-end: #38f9d7; }
        .report-card:nth-child(5) { --color-start: #fa709a; --color-end: #fee140; }

        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }

        .report-card:hover .report-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .report-icon {
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

        .report-card h3 {
            font-size: 1.3rem;
            margin-bottom: 8px;
            color: #333;
        }

        .report-card p {
            color: #888;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .report-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e9ecef;
            color: #495057;
        }

        .report-arrow {
            position: absolute;
            bottom: 25px;
            right: 25px;
            font-size: 1.5rem;
            color: var(--color-start);
            opacity: 0;
            transition: all 0.3s;
        }

        .report-card:hover .report-arrow {
            opacity: 1;
            transform: translateX(5px);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 30px;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin-top: 40px;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .report-card {
            animation: fadeIn 0.5s ease-out;
        }

        .report-card:nth-child(1) { animation-delay: 0.1s; }
        .report-card:nth-child(2) { animation-delay: 0.2s; }
        .report-card:nth-child(3) { animation-delay: 0.3s; }
        .report-card:nth-child(4) { animation-delay: 0.4s; }
        .report-card:nth-child(5) { animation-delay: 0.5s; }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1> Centro de Reportes</h1>
                <p>Análisis detallado del rendimiento del sistema</p>
            </div>
            <div class="header-right">
                <span class="user-badge">
                    <i class="fas fa-shield-alt"></i>
                    <?php echo $_SESSION['nombre']; ?> - <?php echo $_SESSION['rol']; ?>
                </span>
                <a href="../dashboard.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>
        </div>

        

        <!-- Grid de Reportes -->
        <div class="reports-grid">
            <!-- Reporte 1: Ventas por Vendedor -->
            <a href="rpt_ventasvendedor.php" class="report-card">
                <div class="report-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Ventas por Vendedor</h3>
                <p>Analiza el rendimiento de cada vendedor: cantidad de ventas, total vendido y promedio.</p>
                <span class="report-tag"> Rendimiento</span>
                <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Reporte 2: Ventas por Fecha -->
            <a href="rpt_ventasfecha.php" class="report-card">
                <div class="report-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Ventas por Fecha</h3>
                <p>Consulta el detalle de ventas filtrando por rango de fechas específicas.</p>
                <span class="report-tag"> Temporal</span>
                <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Reporte 3: Stock Bajo -->
            <a href="rpt_stockbajo.php" class="report-card">
                <div class="report-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Stock Bajo</h3>
                <p>Identifica productos con inventario crítico que requieren reabastecimiento urgente.</p>
                <span class="report-tag"> Inventario</span>
                <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Reporte 4: Pedidos por Proveedor -->
            <a href="rpt_pedidos.php" class="report-card">
                <div class="report-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Pedidos por Proveedor</h3>
                <p>Gestiona y consulta pedidos filtrando por proveedor y estado (Pendiente, Recibido, Cancelado).</p>
                <span class="report-tag"> Proveedores</span>
                <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>

            <!-- Reporte 5: Usuarios Activos -->
            <a href="rpt_usuariosactivos.php" class="report-card">
                <div class="report-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Usuarios Activos</h3>
                <p>Lista completa de usuarios activos del sistema con sus roles y estados.</p>
                <span class="report-tag"> Personal</span>
                <span class="report-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Luxor - Sistema de Ventas de Licores | Centro de Reportes</p>
    </footer>
</body>
</html>