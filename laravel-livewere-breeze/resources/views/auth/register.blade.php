<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SGL - Criar Conta</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8)),
                        url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .logo-text {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .logo-subtitle {
            color: #6b7280;
            font-size: 1rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            width: 100%;
            padding: 15px;
            background: transparent;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            color: #6b7280;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-secondary:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 20px;
            position: relative;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
        }

        .input-icon input {
            padding-left: 50px;
            padding-right: 60px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            font-size: 20px;
            background: rgba(255,255,255,0.95);
            padding: 8px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border: 2px solid rgba(255,255,255,0.8);
        }

        .password-toggle:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.8rem;
        }

        .strength-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f59e0b; width: 50%; }
        .strength-good { background: #10b981; width: 75%; }
        .strength-strong { background: #059669; width: 100%; }

        @media (max-width: 480px) {
            .register-card {
                padding: 30px 20px;
                margin: 10px;
            }

            .logo-text {
                font-size: 2rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card fade-in">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h1 class="logo-text">SGL</h1>
                <p class="logo-subtitle">Criar Nova Conta</p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                @if ($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user mr-2"></i>Nome Completo
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            placeholder="Seu nome completo"
                            value="{{ old('name') }}"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope mr-2"></i>E-mail
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="seu@email.com"
                            value="{{ old('email') }}"
                            required
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock mr-2"></i>Senha
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Mínimo 8 caracteres"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                        <span class="password-toggle" id="togglePassword" onclick="togglePasswordVisibility()" title="Mostrar/Ocultar senha">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="password-strength" id="passwordStrength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <span id="strengthText"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock mr-2"></i>Confirmar Senha
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-input"
                            placeholder="Digite a senha novamente"
                            required
                            autocomplete="new-password"
                        >
                        <span class="password-toggle" id="togglePasswordConfirmation" onclick="togglePasswordConfirmationVisibility()" title="Mostrar/Ocultar senha">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus mr-2"></i>
                    Criar Conta
                </button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <!-- Login Link -->
            <a href="{{ route('login') }}" class="btn-secondary">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Já tem uma conta? Entrar
            </a>

            <!-- Additional Info -->
            <div style="text-align: center; margin-top: 30px; color: #9ca3af; font-size: 0.8rem;">
                <p>
                    <i class="fas fa-shield-alt mr-1"></i>
                    Seus dados estão seguros conosco
                </p>
            </div>
        </div>
    </div>

    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            if (password.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }

            strengthDiv.style.display = 'block';

            let strength = 0;
            let strengthLabel = '';

            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;

            // Character variety checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            // Determine strength level
            if (strength <= 2) {
                strengthFill.className = 'strength-fill strength-weak';
                strengthLabel = 'Fraca';
            } else if (strength <= 4) {
                strengthFill.className = 'strength-fill strength-fair';
                strengthLabel = 'Razoável';
            } else if (strength <= 5) {
                strengthFill.className = 'strength-fill strength-good';
                strengthLabel = 'Boa';
            } else {
                strengthFill.className = 'strength-fill strength-strong';
                strengthLabel = 'Forte';
            }

            strengthText.textContent = `Força da senha: ${strengthLabel}`;
        });

        // Password confirmation check
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmation = this.value;

            if (confirmation.length > 0) {
                if (password === confirmation) {
                    this.style.borderColor = '#10b981';
                } else {
                    this.style.borderColor = '#ef4444';
                }
            } else {
                this.style.borderColor = '#e5e7eb';
            }
        });

        // Adicionar efeito de foco suave
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Auto-hide messages and clear form fields
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessage = document.querySelector('.error-message');
            const successMessage = document.querySelector('.success-message');

            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.transition = 'opacity 0.5s ease-out';
                    errorMessage.style.opacity = '0';
                    setTimeout(() => errorMessage.remove(), 500);
                }, 8000);
            }

            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.transition = 'opacity 0.5s ease-out';
                    successMessage.style.opacity = '0';
                    setTimeout(() => successMessage.remove(), 500);
                }, 5000);
            }

            // Limpar campos de senha para evitar preenchimento automático
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';

            // Limpar campo de email se não houver erro (old input)
            const emailField = document.getElementById('email');
            if (!emailField.value || emailField.value === '') {
                emailField.value = '';
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;

            if (!name || !email || !password || !passwordConfirmation) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
                return false;
            }

            if (password !== passwordConfirmation) {
                e.preventDefault();
                alert('As senhas não coincidem.');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres.');
                return false;
            }
        });

        // Função para alternar visibilidade da senha
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }

        // Função para alternar visibilidade da confirmação de senha
        function togglePasswordConfirmationVisibility() {
            const passwordInput = document.getElementById('password_confirmation');
            const toggleIcon = document.getElementById('togglePasswordConfirmation');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                toggleIcon.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }
    </script>
</body>
</html>
