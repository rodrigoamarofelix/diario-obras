# 🚀 Como Fazer o Deploy

## 📋 **Status Atual**

✅ Commit estável criado na branch `feature/novos-perfis-usuarios`
✅ Código testado e funcionando
✅ Backups criados para todos os arquivos modificados

## 🔀 **Opções de Deploy**

### **Opção 1: Merge com Main e Push**

```bash
# 1. Voltar para main
git checkout main

# 2. Mesclar a branch
git merge feature/novos-perfis-usuarios

# 3. Push para o remoto
git push origin main
```

### **Opção 2: Push da Branch (Recomendado)**

```bash
# Push da branch atual
git push origin feature/novos-perfis-usuarios

# Criar Pull Request no GitHub/GitLab
# Revisar e aprovar
# Merge via interface
```

### **Opção 3: Deploy Direto em Produção**

```bash
# No servidor de produção
git pull origin feature/novos-perfis-usuarios

# Executar migrations
php artisan migrate

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Reiniciar workers (se houver)
php artisan queue:restart
```

## ⚠️ **ATENÇÃO: Antes do Deploy**

### **1. Executar Migrations no Banco**

```sql
-- Adicionar colunas em pessoas
ALTER TABLE pessoas ADD COLUMN IF NOT EXISTS email VARCHAR(255);
ALTER TABLE pessoas ADD COLUMN IF NOT EXISTS perfil VARCHAR(50) DEFAULT 'user';
ALTER TABLE pessoas ADD COLUMN IF NOT EXISTS password VARCHAR(255);

-- Adicionar pessoa_id em users
ALTER TABLE users ADD COLUMN IF NOT EXISTS pessoa_id BIGINT;

-- Atualizar constraints
ALTER TABLE users DROP CONSTRAINT IF EXISTS users_profile_check;
ALTER TABLE users ADD CONSTRAINT users_profile_check
CHECK (profile IN ('user', 'admin', 'master', 'gestor', 'fiscal', 'construtor', 'visualizador'));
```

### **2. Configurar Email no .env de Produção**

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=4d6f41975016a4
MAIL_PASSWORD=6c11e785f4f723
MAIL_ENCRYPTION=tls
```

### **3. Verificar Backups**

Todos os arquivos modificados têm backups:
- ✅ `_original.php`
- ✅ `_original.blade.php`

## 📊 **O Que Será Deployado**

### **Funcionalidades Novas**
- ✅ Sistema de 7 perfis de usuário
- ✅ Middleware de permissões
- ✅ Campos email/perfil em pessoas
- ✅ Integração Pessoas ↔ Usuários
- ✅ Menu restrito por perfil

### **Melhorias**
- ✅ Validação CPF simplificada
- ✅ Remoção Excel auditoria
- ✅ Email de aprovação funcionando
- ✅ Constraints atualizados

### **Migrations**
- ✅ Nova migration para pessoas

## 🧪 **Testar Após Deploy**

1. Login com usuário existente
2. Verificar menu por perfil
3. Criar nova pessoa com email
4. Aprovar usuário pendente
5. Verificar email no Mailtrap

## ✅ **Checklist de Deploy**

- [ ] Migration executada no banco
- [ ] .env atualizado com credenciais Mailtrap
- [ ] Cache limpo
- [ ] Workers reiniciados (se houver)
- [ ] Testes realizados
- [ ] Backup do banco antes de deploy
- [ ] Rollback plan ready

## 🎉 **Tudo Pronto!**

Siga os passos acima para realizar o deploy com segurança.

