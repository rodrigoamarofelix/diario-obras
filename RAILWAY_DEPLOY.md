# Railway Deployment Guide
# Guia de deploy para Railway

## 🚀 Passo a Passo para Deploy

### 1. Preparar o Projeto
```bash
# Instalar Railway CLI
npm install -g @railway/cli

# Login no Railway
railway login
```

### 2. Configurar Variáveis de Ambiente
```bash
# Criar arquivo .env.production
cp .env .env.production

# Editar .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-app.up.railway.app
DB_CONNECTION=pgsql
```

### 3. Deploy
```bash
# Inicializar projeto
railway init

# Deploy
railway up
```

### 4. Configurar Banco de Dados
```bash
# Adicionar PostgreSQL
railway add postgresql

# Executar migrations
railway run php artisan migrate
```

## 🔧 Configurações Necessárias

### Variáveis de Ambiente
- APP_ENV=production
- APP_DEBUG=false
- APP_URL=https://seu-app.up.railway.app
- DB_CONNECTION=pgsql
- DB_HOST=${{RAILWAY_DATABASE_HOST}}
- DB_PORT=${{RAILWAY_DATABASE_PORT}}
- DB_DATABASE=${{RAILWAY_DATABASE_NAME}}
- DB_USERNAME=${{RAILWAY_DATABASE_USER}}
- DB_PASSWORD=${{RAILWAY_DATABASE_PASSWORD}}

### Comandos de Build
- composer install --no-dev --optimize-autoloader
- php artisan config:cache
- php artisan route:cache
- php artisan view:cache

### Comando de Start
- php artisan serve --host=0.0.0.0 --port=$PORT

## 📱 URLs Importantes

- **App Principal:** https://seu-app.up.railway.app
- **Sistema Offline:** https://seu-app.up.railway.app/sistema_offline.html
- **API Offline:** https://seu-app.up.railway.app/api/offline-data
- **API Sync:** https://seu-app.up.railway.app/api/sync-photos

## 🎯 Benefícios do Railway

✅ **Gratuito** até $5/mês de uso
✅ **Deploy automático** via GitHub
✅ **Banco PostgreSQL** incluído
✅ **SSL automático**
✅ **Domínio personalizado**
✅ **Suporte ao Laravel**
✅ **Logs em tempo real**
✅ **Métricas de performance**

