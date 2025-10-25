<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Aprovada - SGL</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .success-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        .success-icon .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
        }
        .message {
            text-align: center;
            margin-bottom: 30px;
        }
        .message h2 {
            color: #10b981;
            margin-bottom: 15px;
        }
        .message p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .cta-button {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
        }
        .cta-button a:hover {
            transform: translateY(-2px);
        }
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .user-info h3 {
            margin-top: 0;
            color: #374151;
        }
        .user-info p {
            margin: 5px 0;
            color: #6b7280;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SGL</h1>
            <p>Sistema de Gestão de Contratos</p>
        </div>

        <div class="content">
            <div class="success-icon">
                <div class="icon">✓</div>
            </div>

            <div class="message">
                <h2>🎉 Sua conta foi aprovada!</h2>
                <p>Olá <strong>{{ $user->name }}</strong>,</p>
                <p>É com grande prazer que informamos que sua conta no SGL foi <strong>aprovada</strong> com sucesso!</p>
                <p>Agora você pode acessar o sistema e começar a utilizar todas as funcionalidades disponíveis.</p>
            </div>

            <div class="user-info">
                <h3>📋 Informações da sua conta:</h3>
                <p><strong>Nome:</strong> {{ $user->name }}</p>
                <p><strong>E-mail:</strong> {{ $user->email }}</p>
                <p><strong>Perfil:</strong> Usuário</p>
                <p><strong>Status:</strong> Aprovado</p>
            </div>

            <div class="cta-button">
                <a href="{{ $loginUrl }}">🚀 Acessar o Sistema</a>
            </div>

            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <p style="margin: 0; color: #1e40af; font-weight: 500;">
                    💡 <strong>Dica:</strong> Guarde suas credenciais em local seguro e não compartilhe com terceiros.
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>SGL - Sistema de Gestão de Contratos</strong></p>
            <p>Este é um email automático, por favor não responda.</p>
            <p>Se você não solicitou este cadastro, ignore este email.</p>
        </div>
    </div>
</body>
</html>



