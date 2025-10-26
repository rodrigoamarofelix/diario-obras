# Deploy para Teste Móvel

## Opções de Deploy Gratuito

### 1. Vercel (Recomendado)
```bash
# Instalar Vercel CLI
npm i -g vercel

# Deploy
vercel --prod
```

### 2. Netlify
```bash
# Build do projeto
npm run build

# Upload da pasta public/ para Netlify
```

### 3. Heroku
```bash
# Criar Procfile
echo "web: php -S 0.0.0.0:\$PORT -t public" > Procfile

# Deploy
git push heroku main
```

### 4. Railway
```bash
# Conectar repositório GitHub
# Deploy automático
```

## Configurações Necessárias

### .env para Produção
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.vercel.app

DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
```

### Comandos de Deploy
```bash
# Migrar banco
php artisan migrate --force

# Cache de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Teste Móvel
Após deploy, teste:
- GPS no celular
- Upload de fotos
- Geolocalização
- Interface responsiva



