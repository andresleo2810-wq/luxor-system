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
            min-height: 100vh;
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
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 5px;
            font-weight: 700;
            letter-spacing: 2px;
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
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .input-wrapper {
            position: relative;
        }
        .form-group input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-group input.valid {
            border-color: #28a745;
        }
        .form-group input.invalid {
            border-color: #dc3545;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 5px;
            transition: color 0.3s;
        }
        .toggle-password:hover {
            color: #667eea;
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }
        .forgot-password {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .forgot-password:hover {
            color: #5568d3;
            text-decoration: underline;
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
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.8s linear infinite;
        }
        @keyframes spinner {
            to { transform: rotate(360deg); }
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
            transition: all 0.2s;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .credential-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        .credential-info {
            flex: 1;
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
            background: #667eea;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .fill-btn:hover { 
            background: #5568d3; 
        }
        
        .divider {
            margin: 25px 0;
            text-align: center;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            color: #888;
            font-size: 0.85rem;
            position: relative;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            .login-header h1 {
                font-size: 2rem;
            }
            .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
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
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form action="../controlador/auth/login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">📧 Correo Electrónico</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" required 
                           placeholder="ejemplo@luxor.com" autocomplete="username">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Contraseña</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required 
                           placeholder="••••••••" autocomplete="current-password">
                    <button type="button" class="toggle-password" id="togglePassword" title="Mostrar contraseña">
                        👁️
                    </button>
                </div>
            </div>
            
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="recordar" id="recordar">
                    <span>Recordar contraseña</span>
                </label>
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>
            
            <button type="submit" class="btn-login" id="btnLogin">
                Ingresar al Sistema
            </button>
        </form>
        
        <div class="divider">
            <span>O usa estos usuarios</span>
        </div>
        
        <!-- Credenciales de prueba -->
        <div class="credentials-section">
            <div class="credentials-list">
                <div class="credential-item admin" onclick="fillLogin('admin@luxor.com', '1234')">
                    <div class="credential-info">
                        <strong>Administrador <span class="role">ADMIN</span></strong>
                        <span>admin@luxor.com</span>
                    </div>
                    <button type="button" class="fill-btn">Usar</button>
                </div>
                <div class="credential-item vendedor" onclick="fillLogin('vendedor@luxor.com', '1234')">
                    <div class="credential-info">
                        <strong>Vendedor <span class="role">VENTAS</span></strong>
                        <span>vendedor@luxor.com</span>
                    </div>
                    <button type="button" class="fill-btn">Usar</button>
                </div>
                <div class="credential-item bodega" onclick="fillLogin('bodega@luxor.com', '1234')">
                    <div class="credential-info">
                        <strong>Almacenista <span class="role">BODEGA</span></strong>
                        <span>bodega@luxor.com</span>
                    </div>
                    <button type="button" class="fill-btn">Usar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
            this.title = type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña';
        });
        
        // Función para autocompletar el login
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            
            // Efecto visual de confirmación
            const form = document.getElementById('loginForm');
            form.style.transform = 'scale(0.98)';
            setTimeout(() => { form.style.transform = 'scale(1)'; }, 150);
            
            // Enfocar el campo de contraseña
            document.getElementById('password').focus();
            
            // Mostrar feedback visual
            const emailInput = document.getElementById('email');
            emailInput.classList.add('valid');
            setTimeout(() => { emailInput.classList.remove('valid'); }, 1000);
        }
        
        // Validación en tiempo real del email
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value.length > 0) {
                if (emailRegex.test(this.value)) {
                    this.classList.remove('invalid');
                    this.classList.add('valid');
                } else {
                    this.classList.remove('valid');
                    this.classList.add('invalid');
                }
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });
        
        // Efecto de carga en el botón
        const loginForm = document.getElementById('loginForm');
        const btnLogin = document.getElementById('btnLogin');
        
        loginForm.addEventListener('submit', function() {
            btnLogin.classList.add('loading');
            btnLogin.textContent = 'Ingresando...';
        });
        
        // Permitir login con Enter
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loginForm.submit();
            }
        });
        
        // Recordar contraseña (localStorage)
        window.addEventListener('load', function() {
            const savedEmail = localStorage.getItem('luxor_email');
            const savedPassword = localStorage.getItem('luxor_password');
            
            if (savedEmail) {
                document.getElementById('email').value = savedEmail;
                document.getElementById('recordar').checked = true;
            }
            if (savedPassword) {
                document.getElementById('password').value = savedPassword;
            }
        });
        
        // Guardar credenciales si se marca "Recordar"
        document.getElementById('recordar').addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('luxor_email', document.getElementById('email').value);
                localStorage.setItem('luxor_password', document.getElementById('password').value);
            } else {
                localStorage.removeItem('luxor_email');
                localStorage.removeItem('luxor_password');
            }
        });
    </script>
</body>
</html>