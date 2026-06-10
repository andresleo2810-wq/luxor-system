<?php 
session_start();
require_once '../config.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Luxor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .login-header p {
            color: #888;
            font-size: 0.9rem;
        }
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Sección de credenciales */
        .credentials-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }
        .credentials-section h4 {
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 15px;
            text-align: center;
        }
        .credentials-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .credential-item {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 0.85rem;
        }
        .credential-item:hover {
            background: #e9ecef;
        }
        .credential-item strong {
            color: #333;
            display: block;
            margin-bottom: 3px;
        }
        .credential-item span {
            color: #666;
            font-family: monospace;
        }
        .credential-item .role {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-left: 8px;
        }
        .credential-item.admin { border-left-color: #667eea; }
        .credential-item.vendedor { border-left-color: #28a745; }
        .credential-item.bodega { border-left-color: #fd7e14; }
        
        .fill-btn {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 0.8rem;
            padding: 0;
            margin-left: 10px;
            text-decoration: underline;
        }
        .fill-btn:hover { color: #5568d3; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>LUXOR</h1>
            <p>Sistema de Ventas de Licores</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                 <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="../controlador/auth/login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email"> Correo Electrónico</label>
                <input type="email" id="email" name="email" required 
                       placeholder="ejemplo@luxor.com" autocomplete="username">
            </div>
            
            <div class="form-group">
                <label for="password"> Contraseña</label>
                <input type="password" id="password" name="password" required 
                       placeholder="••••••••" autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn-login"> Ingresar al Sistema</button>
        </form>
        
        <!-- Credenciales de prueba -->
        <div class="credentials-section">
            <h4> Usuarios de prueba (clic para autocompletar)</h4>
            <div class="credentials-list">
                <div class="credential-item admin" onclick="fillLogin('admin@luxor.com', '1234')">
                    <strong>Administrador <span class="role">ADMIN</span></strong>
                    <span>admin@luxor.com</span>
                    <button type="button" class="fill-btn">Usar</button>
                </div>
                <div class="credential-item vendedor" onclick="fillLogin('vendedor@luxor.com', '1234')">
                    <strong>Vendedor <span class="role">VENTAS</span></strong>
                    <span>vendedor@luxor.com</span>
                    <button type="button" class="fill-btn">Usar</button>
                </div>
                <div class="credential-item bodega" onclick="fillLogin('bodega@luxor.com', '1234')">
                    <strong>Almacenista <span class="role">BODEGA</span></strong>
                    <span>bodega@luxor.com</span>
                    <button type="button" class="fill-btn">Usar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para autocompletar el login con un clic
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            
            // Efecto visual de confirmación
            const form = document.getElementById('loginForm');
            form.style.transform = 'scale(0.98)';
            setTimeout(() => { form.style.transform = 'scale(1)'; }, 150);
            
            // Enfocar el campo de contraseña para facilitar el ingreso
            document.getElementById('password').focus();
        }
        
        // Permitir login con Enter
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>