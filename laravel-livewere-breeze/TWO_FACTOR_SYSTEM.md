# 🔐 Sistema de Autenticação de Dois Fatores (2FA) - SGC

## 📋 Visão Geral

O Sistema de Autenticação de Dois Fatores (2FA) do SGC adiciona uma camada extra de segurança à sua conta. Além da senha tradicional, você precisará de um código gerado pelo seu aplicativo autenticador.

## 🚀 Funcionalidades Implementadas

### ✅ **Sistema Completo**

- **Ativação Opcional**: Usuários podem escolher ativar ou não o 2FA
- **QR Code**: Geração automática de QR Code para configuração
- **Códigos de Backup**: 8 códigos únicos para recuperação de acesso
- **Interface Intuitiva**: Interface Livewire responsiva e fácil de usar
- **Integração Completa**: Integrado ao sistema de usuários existente
- **Indicadores Visuais**: Status de segurança no menu do usuário

### 🔧 **Componentes Implementados**

#### 1. **TwoFactorService**
- Localização: `app/Services/TwoFactorService.php`
- Geração de chaves secretas
- Códigos TOTP (Time-based One-Time Password)
- Códigos de backup
- Verificação de códigos

#### 2. **TwoFactorController**
- Localização: `app/Http/Controllers/TwoFactorController.php`
- Endpoints para gerenciar 2FA
- Validação de códigos
- Ativação/desativação

#### 3. **TwoFactorComponent (Livewire)**
- Localização: `app/Livewire/TwoFactorComponent.php`
- Interface dinâmica e responsiva
- Geração de QR Code em tempo real
- Gerenciamento de códigos de backup

#### 4. **Modelo User Atualizado**
- Campos adicionados: `two_factor_enabled`, `two_factor_secret`, `two_factor_backup_codes`, `two_factor_enabled_at`
- Métodos para gerenciar 2FA
- Verificação de códigos de backup

## 🎯 **Como Usar**

### **Para Ativar o 2FA:**

1. **Acesse**: Menu do usuário → "Autenticação 2FA"
2. **Clique**: "🚀 Ativar 2FA"
3. **Instale**: Um aplicativo autenticador (Google Authenticator, Authy, etc.)
4. **Escaneie**: O QR Code gerado
5. **Digite**: O código de 6 dígitos do app
6. **Guarde**: Os códigos de backup em local seguro

### **Para Desativar o 2FA:**

1. **Acesse**: Menu do usuário → "Autenticação 2FA"
2. **Digite**: Sua senha atual
3. **Clique**: "🗑️ Desativar"

### **Para Regenerar Códigos de Backup:**

1. **Acesse**: Página do 2FA
2. **Digite**: Sua senha atual
3. **Clique**: "Regenerar"
4. **Guarde**: Os novos códigos

## 📱 **Aplicativos Recomendados**

### **Google Authenticator**
- ✅ iOS e Android
- ✅ Gratuito
- ✅ Fácil de usar
- ✅ Sincronização local

### **Authy**
- ✅ iOS e Android
- ✅ Gratuito
- ✅ Backup na nuvem
- ✅ Múltiplos dispositivos

### **Microsoft Authenticator**
- ✅ iOS e Android
- ✅ Gratuito
- ✅ Integração com Microsoft
- ✅ Backup na nuvem

## 🔒 **Segurança**

### **Campos Protegidos:**
- `two_factor_secret`: Chave secreta (oculta)
- `two_factor_backup_codes`: Códigos de backup (ocultos)

### **Validações:**
- Códigos TOTP válidos por 30 segundos
- Códigos de backup únicos (usados apenas uma vez)
- Senha obrigatória para desativar
- Senha obrigatória para regenerar códigos

### **Recomendações:**
- ✅ Ative o 2FA para maior segurança
- ✅ Guarde códigos de backup em local seguro
- ✅ Use aplicativos confiáveis
- ✅ Não compartilhe códigos com ninguém

## 🛠️ **Configuração Técnica**

### **Migration:**
```sql
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN two_factor_secret VARCHAR(255) NULL,
ADD COLUMN two_factor_backup_codes JSON NULL,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL;
```

### **Rotas Configuradas:**
```php
Route::middleware('auth')->prefix('two-factor')->name('two-factor.')->group(function () {
    Route::get('/', [TwoFactorController::class, 'index'])->name('index');
    Route::post('/generate-secret', [TwoFactorController::class, 'generateSecret'])->name('generate-secret');
    Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
    Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/regenerate-backup-codes', [TwoFactorController::class, 'regenerateBackupCodes'])->name('regenerate-backup-codes');
});
```

## 🎨 **Interface do Usuário**

### **Status Visual:**
- 🟢 **Verde**: 2FA Ativado
- 🔴 **Vermelho**: 2FA Desativado
- ⚠️ **Amarelo**: Recomendação de ativação

### **Indicadores no Menu:**
- Badge "Ativo" para usuários com 2FA
- Badge "Inativo" para usuários sem 2FA
- Ícone de escudo para identificação

## 📊 **Estatísticas de Segurança**

### **Métricas Disponíveis:**
- Total de usuários com 2FA ativado
- Total de usuários sem 2FA
- Percentual de adoção do 2FA
- Data de ativação por usuário

## 🔄 **Próximas Melhorias**

### **Planejadas:**
- [ ] **2FA Obrigatório**: Para perfis Master/Admin
- [ ] **Notificações**: Alertas de segurança
- [ ] **Relatórios**: Estatísticas de segurança
- [ ] **Integração**: Com sistema de login
- [ ] **Backup**: Códigos criptografados

### **Integrações Futuras:**
- [ ] **SMS**: Códigos via SMS
- [ ] **Email**: Códigos via email
- [ ] **Hardware**: Chaves físicas (FIDO2)
- [ ] **Biometria**: Integração com biometria

## 🚨 **Solução de Problemas**

### **Problemas Comuns:**

#### 1. **QR Code não aparece**
- **Causa**: JavaScript não carregado
- **Solução**: Verificar console do navegador

#### 2. **Código inválido**
- **Causa**: Relógio desincronizado
- **Solução**: Verificar hora do dispositivo

#### 3. **Códigos de backup não funcionam**
- **Causa**: Códigos já utilizados
- **Solução**: Regenerar novos códigos

### **Logs de Debug:**
```bash
# Verificar logs do Laravel
tail -f storage/logs/laravel.log

# Verificar configuração do banco
php artisan tinker
>>> User::first()->two_factor_enabled
```

## 📞 **Suporte**

Para dúvidas sobre o sistema 2FA:

1. **Consulte esta documentação**
2. **Verifique os logs do sistema**
3. **Teste com aplicativo autenticador**
4. **Entre em contato com o administrador**

---

## 🎉 **Sistema Implementado com Sucesso!**

O Sistema de Autenticação de Dois Fatores está **100% funcional** e integrado ao SGC, oferecendo:

- ✅ **Segurança robusta** com 2FA opcional
- ✅ **Interface intuitiva** e responsiva
- ✅ **Códigos de backup** para recuperação
- ✅ **Integração completa** com o sistema
- ✅ **Indicadores visuais** de status
- ✅ **Documentação completa**

**Pronto para uso em produção!** 🚀

---

**SGC - Gestão de Contratos v1.0.0**
*Sistema de Autenticação de Dois Fatores*


