<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Rejeitada - SGL</title>
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
        .rejection-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        .rejection-icon .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
            color: #ef4444;
            margin-bottom: 15px;
        }
        .message p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .user-info {
            background: #fef2f2;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .user-info h3 {
            margin-top: 0;
            color: #dc2626;
        }
        .user-info p {
            margin: 5px 0;
            color: #6b7280;
        }
        .contact-info {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .contact-info h3 {
            margin-top: 0;
            color: #1e40af;
        }
        .contact-info p {
            margin: 5px 0;
            color: #1e40af;
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
            <div class="rejection-icon">
                <div class="icon">✗</div>
            </div>

            <div class="message">
                <h2>❌ Sua conta foi rejeitada</h2>
                <p>Olá <strong>{{ $user->name }}</strong>,</p>
                <p>Infelizmente, sua solicitação de cadastro no SGL foi <strong>rejeitada</strong>.</p>
                <p>Isso pode ter ocorrido por diversos motivos, como informações incompletas ou não conformidade com nossos critérios de aprovação.</p>
            </div>

            <div class="user-info">
                <h3>📋 Informações da solicitação:</h3>
                <p><strong>Nome:</strong> {{ $user->name }}</p>
                <p><strong>E-mail:</strong> {{ $user->email }}</p>
                <p><strong>Status:</strong> Rejeitado</p>
                <p><strong>Data:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="contact-info">
                <h3>📞 Precisa de ajuda?</h3>
                <p>Se você acredita que houve um erro ou gostaria de esclarecer alguma informação, entre em contato conosco:</p>
                <p><strong>E-mail:</strong> suporte@sgl.com</p>
                <p><strong>Telefone:</strong> (11) 9999-9999</p>
                <p>Nossa equipe estará disponível para ajudá-lo e esclarecer qualquer dúvida.</p>
            </div>

            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <p style="margin: 0; color: #92400e; font-weight: 500;">
                    💡 <strong>Dica:</strong> Você pode tentar criar uma nova conta no futuro, preenchendo todas as informações solicitadas corretamente.
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



