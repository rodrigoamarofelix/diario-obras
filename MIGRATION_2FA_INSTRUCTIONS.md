# 🔧 Instruções para Executar Migration do 2FA

## ⚠️ **Erro Corrigido**

O erro `TypeError: Return value must be of type bool, null returned` foi corrigido no método `hasTwoFactorEnabled()` do modelo User.

## 🛠️ **Para Executar a Migration:**

### **Opção 1: Via MySQL diretamente**
```sql
-- Conectar ao MySQL
mysql -u root -p

-- Selecionar o banco
USE sgc;

-- Executar a migration
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;
```

### **Opção 2: Via phpMyAdmin**
1. Acesse o phpMyAdmin
2. Selecione o banco `sgc`
3. Vá em "SQL"
4. Execute o comando acima

### **Opção 3: Via Docker (se estiver usando)**
```bash
# Se estiver usando Docker
docker-compose exec db mysql -u root -p sgc -e "ALTER TABLE users ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at, ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled, ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret, ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;"
```

## ✅ **Verificar se funcionou:**

Após executar a migration, teste acessando:
- Menu do usuário → "🔐 Autenticação 2FA"
- A página deve carregar sem erros
- O status deve mostrar "❌ 2FA Desativado"

## 🔍 **Se ainda houver problemas:**

1. **Verificar se os campos existem:**
```sql
DESCRIBE users;
```

2. **Verificar valores padrão:**
```sql
SELECT id, name, two_factor_enabled FROM users LIMIT 5;
```

3. **Atualizar usuários existentes:**
```sql
UPDATE users SET two_factor_enabled = FALSE WHERE two_factor_enabled IS NULL;
```

## 🎯 **Próximos Passos:**

Após executar a migration:
1. ✅ Sistema 2FA estará funcionando
2. ✅ Usuários podem ativar/desativar 2FA
3. ✅ Interface completa disponível
4. ✅ Códigos de backup funcionando

---

**Sistema 2FA - Pronto para uso após migration!** 🚀


