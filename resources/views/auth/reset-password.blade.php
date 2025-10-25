<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config("app.name") }} - Redefinir Senha</title>

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

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            color: #000 !important;
            z-index: 10;
            position: relative;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-input[readonly] {
            background: #f8f9fa !important;
            color: #666 !important;
            cursor: not-allowed;
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

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
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

        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
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

        .input-icon .form-input {
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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card fade-in">
            <div class="logo-icon">
                <i class="fas fa-lock text-white text-3xl"></i>
            </div>
            <h1 class="logo-text">{{ config("app.name") }}</h1>
            <h2 style="color: #6b7280; margin-bottom: 30px;">Redefinir Senha</h2>

            <p style="color: #6b7280; margin-bottom: 30px;">
                Digite sua nova senha abaixo.
            </p>

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

            <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ $email ?? old('email') ?? request('email') }}"
                        readonly
                        style="background: #f8f9fa !important; color: #666 !important;"
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Nova Senha</label>
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
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
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
                    <i class="fas fa-save mr-2"></i>
                    Redefinir Senha
                </button>
            </form>

            <a href="{{ route('login') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar ao Login
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');

            // Verificar e preencher o e-mail automaticamente
            const urlParams = new URLSearchParams(window.location.search);
            const emailFromUrl = urlParams.get('email');

            if (emailFromUrl && !emailInput.value) {
                emailInput.value = decodeURIComponent(emailFromUrl);
            }

            // Função para verificar força da senha
            function checkPasswordStrength(password) {
                let strength = 0;
                let feedback = [];

                if (password.length >= 8) strength++;
                else feedback.push('Mínimo 8 caracteres');

                if (/[a-z]/.test(password)) strength++;
                else feedback.push('Adicione letras minúsculas');

                if (/[A-Z]/.test(password)) strength++;
                else feedback.push('Adicione letras maiúsculas');

                if (/[0-9]/.test(password)) strength++;
                else feedback.push('Adicione números');

                if (/[^A-Za-z0-9]/.test(password)) strength++;
                else feedback.push('Adicione símbolos');

                const strengthText = ['Muito fraca', 'Fraca', 'Regular', 'Boa', 'Muito boa'][strength] || 'Muito fraca';
                const strengthColor = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'][strength] || '#dc3545';

                passwordStrength.innerHTML = `<span style="color: ${strengthColor}">Força: ${strengthText}</span>`;
                if (feedback.length > 0) {
                    passwordStrength.innerHTML += `<br><small>${feedback.join(', ')}</small>`;
                }
            }

            // Event listeners para os campos de senha
            passwordInput.addEventListener('input', function(e) {
                checkPasswordStrength(e.target.value);
            });

            // Forçar foco no campo de senha
            setTimeout(function() {
                passwordInput.focus();
            }, 500);
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