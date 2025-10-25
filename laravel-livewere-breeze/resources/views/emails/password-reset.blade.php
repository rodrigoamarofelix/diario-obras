<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - SGL</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            font-size: 1rem;
            line-height: 1.6;
            color: #6b7280;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 30px;
            transition: transform 0.2s ease;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .warning-text {
            color: #92400e;
            font-size: 0.9rem;
            margin: 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            font-size: 0.9rem;
            color: #9ca3af;
            margin: 0;
        }
        .link {
            color: #667eea;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SGL</div>
            <div class="subtitle">Sistema de Gestão de Contratos</div>
        </div>

        <div class="content">
            <h1 class="title">Redefinir sua senha</h1>

            <p class="message">
                Olá!<br><br>
                Você solicitou a redefinição de senha para sua conta no SGL.
                Clique no botão abaixo para criar uma nova senha:
            </p>

            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="button">
                    Redefinir Senha
                </a>
            </div>

            <div class="warning">
                <p class="warning-text">
                    <strong>⚠️ Importante:</strong> Este link expira em {{ $count }} minutos.
                    Se você não solicitou esta redefinição, ignore este e-mail.
                </p>
            </div>

            <p class="message">
                Se o botão acima não funcionar, copie e cole o link abaixo no seu navegador:<br>
                <a href="{{ $actionUrl }}" class="link">{{ $actionUrl }}</a>
            </p>
        </div>

        <div class="footer">
            <p class="footer-text">
                Este e-mail foi enviado automaticamente pelo SGL.<br>
                Se você não solicitou esta redefinição, pode ignorar este e-mail com segurança.
            </p>
        </div>
    </div>
</body>
</html>



