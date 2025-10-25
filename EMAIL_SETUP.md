# Configuração de E-mail - SGL

## Problema Resolvido ✅

### 1. Mensagens em Português
- ✅ Adicionadas traduções em `lang/pt_BR/auth.php`
- ✅ Configurado `APP_LOCALE=pt_BR` no `.env`
- ✅ Mensagem agora aparece: "Enviamos o link de redefinição de senha para seu e-mail!"

### 2. E-mail Funcionando
- ✅ E-mail está sendo enviado (salvo em log)
- ✅ Link de reset está sendo gerado corretamente
- ✅ Template personalizado criado em `resources/views/emails/password-reset.blade.php`

## Como Verificar o E-mail Enviado

### Opção 1: Verificar Logs
```bash
# Ver o último e-mail enviado
docker compose exec php-fpm tail -100 storage/logs/laravel.log

# Procurar por e-mails específicos
docker compose exec php-fpm grep -A 20 -B 5 "admin@test.com" storage/logs/laravel.log
```

### Opção 2: Testar com Comando
```bash
# Testar envio para qualquer e-mail
docker compose exec php-fpm php artisan test:email admin@test.com
```

## Configurar E-mail Real (Opcional)

### Para usar Gmail:
1. Edite o arquivo `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="seu-email@gmail.com"
MAIL_FROM_NAME="SGL - Sistema de Gestão"
```

2. Configure senha de app no Gmail:
   - Acesse: https://myaccount.google.com/apppasswords
   - Gere uma senha de app
   - Use essa senha no `MAIL_PASSWORD`

### Para usar Mailtrap (Recomendado para desenvolvimento):
1. Crie conta em: https://mailtrap.io
2. Copie as credenciais do inbox
3. Edite o arquivo `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=sua_username_mailtrap
MAIL_PASSWORD=sua_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sgl.com"
MAIL_FROM_NAME="SGL - Sistema de Gestão"
```

## Status Atual
- ✅ **Mensagens em português**: Funcionando
- ✅ **Envio de e-mail**: Funcionando (salvo em log)
- ✅ **Link de reset**: Funcionando
- ✅ **Template personalizado**: Criado
- ⚠️ **E-mail real**: Configurado para log (desenvolvimento)

## Próximos Passos
1. Para desenvolvimento: Continue usando `MAIL_MAILER=log`
2. Para produção: Configure SMTP real (Gmail, Mailtrap, etc.)
3. Os e-mails aparecerão nos logs em `storage/logs/laravel.log`



