# 🔧 Como Testar o Sistema 2FA

## ✅ **Link Adicionado ao Menu Lateral**

O link "🔐 Autenticação 2FA" foi adicionado ao menu lateral do sistema, logo após o item "Backup".

## 🛠️ **Para Executar a Migration:**

### **Opção 1: Via phpMyAdmin (Recomendado)**
1. Acesse o phpMyAdmin
2. Selecione o banco `sgc`
3. Vá na aba "SQL"
4. Execute este comando:

```sql
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;
```

### **Opção 2: Via MySQL Command Line**
```bash
mysql -u root -p
USE sgc;
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;
```

## 🎯 **Como Testar:**

### **1. Acessar o Sistema 2FA:**
- Faça login no sistema
- No menu lateral, clique em "🔐 Autenticação 2FA"
- Você verá o status "❌ 2FA Desativado"

### **2. Ativar o 2FA:**
- Clique em "🚀 Ativar 2FA"
- Instale um app autenticador (Google Authenticator, Authy, etc.)
- Escaneie o QR Code gerado
- Digite o código de 6 dígitos
- Guarde os códigos de backup

### **3. Verificar Status:**
- Após ativar, o menu mostrará "✅ Ativo"
- Você pode desativar digitando sua senha

## 📱 **Apps Recomendados (TODOS GRATUITOS):**

### **🔍 Google Authenticator** ⭐ **RECOMENDADO**
- ✅ **100% Gratuito**
- ✅ iOS e Android
- ✅ Fácil de usar
- ✅ Desenvolvido pelo Google
- 📱 **Download**: App Store / Google Play

### **🔐 Authy**
- ✅ **100% Gratuito**
- ✅ iOS e Android
- ✅ Backup na nuvem
- ✅ Múltiplos dispositivos
- 📱 **Download**: App Store / Google Play

### **🛡️ Microsoft Authenticator**
- ✅ **100% Gratuito**
- ✅ iOS e Android
- ✅ Integração com Microsoft
- ✅ Backup na nuvem
- 📱 **Download**: App Store / Google Play

### **🔑 Alternativas Gratuitas:**
- **FreeOTP** (iOS/Android) - Open Source
- **Aegis Authenticator** (Android) - Open Source
- **TOTP Authenticator** (iOS) - Gratuito
- **Authenticator** (iOS) - Gratuito

## ⚠️ **IMPORTANTE - Todos são GRATUITOS!**

**Não confunda com apps pagos!** Os aplicativos autenticadores principais são **100% gratuitos**:

- ✅ **Google Authenticator** - Gratuito
- ✅ **Authy** - Gratuito
- ✅ **Microsoft Authenticator** - Gratuito

**Evite apps pagos** que cobram por funcionalidades básicas. Os apps oficiais das grandes empresas são sempre gratuitos.

## 🎯 **Recomendação:**

**Use o Google Authenticator** - É o mais confiável e amplamente usado:
1. Vá na App Store ou Google Play
2. Procure por "Google Authenticator"
3. Instale (é gratuito!)
4. Escaneie o QR Code do sistema
5. Pronto! 🎉

## 🔍 **Se não aparecer o link:**

1. **Verifique se a migration foi executada:**
```sql
DESCRIBE users;
```

2. **Verifique se os campos existem:**
```sql
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'users' AND COLUMN_NAME LIKE 'two_factor%';
```

3. **Limpe o cache do navegador** (Ctrl+F5)

## 🎉 **Após a Migration:**

- ✅ Link "🔐 Autenticação 2FA" aparecerá no menu lateral
- ✅ Status visual (Ativo/Inativo) funcionando
- ✅ Interface completa disponível
- ✅ QR Code sendo gerado
- ✅ Códigos de backup funcionando

---

**Execute a migration e teste o sistema!** 🚀
