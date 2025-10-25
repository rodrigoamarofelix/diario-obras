# 🔧 Executar SQL com Segurança - Instruções Manuais

## ⚠️ **Situação Atual:**
O sistema não consegue executar o SQL automaticamente devido a limitações do ambiente. Vou te guiar para executar manualmente com segurança.

## 🎯 **Opções para Executar o SQL:**

### **Opção 1 - Via phpMyAdmin (RECOMENDADO):**

1. **Abra o phpMyAdmin** no seu navegador
2. **Selecione o banco** `laravel_livewire_breeze`
3. **Clique na aba "SQL"**
4. **Cole e execute** o seguinte código:

```sql
-- Adicionar colunas 2FA com segurança
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;
```

5. **Clique em "Executar"**
6. **Verifique se funcionou** executando:
```sql
DESCRIBE users;
```

### **Opção 2 - Via MySQL Workbench:**

1. **Abra o MySQL Workbench**
2. **Conecte ao servidor**
3. **Selecione o banco** `laravel_livewire_breeze`
4. **Execute o SQL** acima
5. **Verifique a estrutura** da tabela

### **Opção 3 - Via linha de comando (se disponível):**

```bash
mysql -u root -p laravel_livewire_breeze
```

Depois execute o SQL:
```sql
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;
```

## ✅ **Verificação de Segurança:**

### **Antes de executar:**
- ✅ **Backup do banco** (recomendado)
- ✅ **Verificar se as colunas já existem**
- ✅ **Testar em ambiente de desenvolvimento primeiro**

### **Após executar:**
- ✅ **Verificar estrutura da tabela**
- ✅ **Testar o 2FA no sistema**
- ✅ **Verificar logs de erro**

## 🔍 **Verificar se Funcionou:**

Execute este SQL para verificar:
```sql
DESCRIBE users;
```

Deve mostrar as colunas:
- `two_factor_enabled`
- `two_factor_secret`
- `two_factor_backup_codes`
- `two_factor_enabled_at`

## 🚀 **Após Executar o SQL:**

1. **Recarregue a página** (Ctrl+F5)
2. **Teste o 2FA:**
   - Clique em "🚀 Ativar 2FA"
   - Escaneie o QR Code com Authy/Microsoft Authenticator
   - Digite o código de 6 dígitos

## 🎉 **Resultado Esperado:**

- ✅ **Sem erros de coluna não encontrada**
- ✅ **2FA funcionando perfeitamente**
- ✅ **Códigos sendo validados**
- ✅ **Backup codes sendo salvos**

**Execute o SQL e teste o 2FA!** 🚀✅

