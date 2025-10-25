# 💾 Sistema de Backup Automático - SGC Gestão de Contratos

## 📋 Visão Geral

O Sistema de Backup Automático do SGC permite criar, gerenciar e restaurar backups do banco de dados e arquivos do sistema de forma automatizada e segura.

## 🚀 Funcionalidades

### ✅ Implementadas

- **Backup Completo**: Banco de dados + arquivos do sistema
- **Backup de Banco**: Apenas dados do banco de dados MySQL/MariaDB
- **Backup de Arquivos**: Apenas arquivos do sistema (código, configurações, etc.)
- **Interface Web**: Gerenciamento através da interface administrativa
- **Download de Backups**: Baixar backups individuais
- **Exclusão de Backups**: Remover backups desnecessários
- **Estatísticas**: Visualizar informações sobre backups
- **Limpeza Automática**: Remover backups antigos automaticamente
- **Comando CLI**: Execução via linha de comando para agendamento

### 🔧 Tipos de Backup

#### 1. Backup Completo (`full`)
- **Banco de dados**: Dump completo de todas as tabelas
- **Arquivos**: Código fonte, configurações, logs
- **Tamanho**: ~3.25 MB (dependendo dos dados)
- **Tempo**: ~30 segundos

#### 2. Backup de Banco (`database`)
- **Conteúdo**: Apenas dados do banco de dados
- **Formato**: Arquivo SQL com estrutura e dados
- **Tamanho**: ~108 KB (dependendo dos dados)
- **Tempo**: ~5 segundos

#### 3. Backup de Arquivos (`files`)
- **Conteúdo**: Código fonte, configurações, logs
- **Formato**: Arquivo ZIP comprimido
- **Tamanho**: ~1.5 MB
- **Tempo**: ~25 segundos

## 🎯 Como Usar

### Interface Web

1. **Acesse**: `/backup` (apenas administradores)
2. **Crie Backup**: Use os botões de ação rápida
3. **Visualize**: Lista de todos os backups disponíveis
4. **Baixe**: Clique no ícone de download
5. **Exclua**: Clique no ícone de lixeira
6. **Configure**: Agendamento automático

### Linha de Comando

```bash
# Backup completo
php artisan backup:schedule --type=full

# Backup apenas do banco
php artisan backup:schedule --type=database

# Backup apenas de arquivos
php artisan backup:schedule --type=files

# Backup usando Spatie (apenas arquivos)
php artisan backup:run --only-files
```

## 📁 Estrutura de Arquivos

```
storage/
├── app/
│   ├── backups/                    # Backups personalizados (banco)
│   │   ├── database-2025-10-17-00-35-58.sql
│   │   └── database-2025-10-17-00-36-22.sql
│   └── private/
│       └── Laravel/               # Backups do Spatie (arquivos)
│           ├── 2025-10-17-00-34-37.zip
│           └── 2025-10-17-00-36-19.zip
```

## ⚙️ Configuração

### Arquivo de Configuração
- **Localização**: `config/backup.php`
- **Banco**: Configurado para MySQL/MariaDB
- **Arquivos**: Inclui diretórios essenciais do sistema

### Diretórios Incluídos
- `app/` - Código da aplicação
- `config/` - Configurações
- `database/` - Migrations e seeders
- `resources/` - Views e assets
- `routes/` - Rotas da aplicação
- `storage/app/` - Arquivos da aplicação
- `storage/logs/` - Logs do sistema
- `.env` - Variáveis de ambiente
- `composer.json` - Dependências

### Diretórios Excluídos
- `vendor/` - Dependências do Composer
- `node_modules/` - Dependências do NPM
- `storage/framework/cache/` - Cache do framework
- `storage/framework/sessions/` - Sessões
- `storage/framework/views/` - Views compiladas
- `bootstrap/cache/` - Cache de bootstrap

## 🔄 Agendamento Automático

### Cron Job (Recomendado)

```bash
# Adicione ao crontab do servidor
# Backup diário às 2:00 AM
0 2 * * * cd /path/to/project && php artisan backup:schedule --type=full

# Backup semanal do banco às 3:00 AM (domingos)
0 3 * * 0 cd /path/to/project && php artisan backup:schedule --type=database
```

### Docker

```yaml
# docker-compose.yml
services:
  backup-scheduler:
    image: php:8.2-cli
    volumes:
      - .:/var/www/html
    command: >
      sh -c "
        while true; do
          php artisan backup:schedule --type=full;
          sleep 86400;
        done
      "
```

## 📊 Monitoramento

### Estatísticas Disponíveis
- **Total de Backups**: Quantidade de backups armazenados
- **Tamanho Total**: Espaço ocupado pelos backups
- **Último Backup**: Data/hora do último backup
- **Backup Mais Antigo**: Data do backup mais antigo

### Logs
- **Localização**: `storage/logs/laravel.log`
- **Comando**: `php artisan backup:schedule` registra execução
- **Interface**: Logs de criação/exclusão de backups

## 🔒 Segurança

### Permissões
- **Acesso**: Apenas usuários com perfil `Master` ou `Admin`
- **Rotas**: Protegidas por middleware `can:manage-users`
- **Arquivos**: Armazenados em `storage/app/` (não acessível via web)

### Recomendações
1. **Backup Remoto**: Configure backup para servidor externo
2. **Criptografia**: Considere criptografar backups sensíveis
3. **Teste**: Teste restauração regularmente
4. **Retenção**: Mantenha backups por pelo menos 30 dias

## 🚨 Solução de Problemas

### Problemas Comuns

#### 1. Erro de SSL/TLS
```
mysqldump: Got error: 2026: "TLS/SSL error: self-signed certificate"
```
**Solução**: Sistema usa backup personalizado via Laravel (resolvido)

#### 2. Permissões de Arquivo
```
Permission denied: storage/app/backups/
```
**Solução**:
```bash
chmod -R 755 storage/app/backups/
chown -R www-data:www-data storage/app/backups/
```

#### 3. Espaço em Disco
```
No space left on device
```
**Solução**: Configure limpeza automática ou aumente espaço

### Logs de Debug
```bash
# Verificar logs do Laravel
tail -f storage/logs/laravel.log

# Verificar execução do comando
php artisan backup:schedule --type=full -v
```

## 📈 Próximas Melhorias

### Planejadas
- [ ] **Backup para Cloud**: AWS S3, Google Drive, Dropbox
- [ ] **Criptografia**: Criptografar backups sensíveis
- [ ] **Compressão**: Melhor compressão de arquivos
- [ ] **Notificações**: Email/SMS quando backup falha
- [ ] **Restauração**: Interface para restaurar backups
- [ ] **Backup Incremental**: Apenas arquivos modificados
- [ ] **Verificação**: Checksum para validar integridade

### Integrações
- [ ] **Slack**: Notificações no Slack
- [ ] **Discord**: Notificações no Discord
- [ ] **Telegram**: Notificações no Telegram
- [ ] **Webhook**: Notificações via webhook personalizado

## 📞 Suporte

Para dúvidas ou problemas com o sistema de backup:

1. **Verifique os logs**: `storage/logs/laravel.log`
2. **Teste manualmente**: `php artisan backup:schedule --type=database`
3. **Verifique permissões**: Diretórios e arquivos
4. **Consulte documentação**: Laravel Backup (Spatie)

---

**Sistema de Backup Automático** - SGC Gestão de Contratos
*Versão 1.0 - Implementado em 17/10/2025*




