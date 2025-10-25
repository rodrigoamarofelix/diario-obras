#!/bin/bash

# Script para emitir certificado SSL em produção
# Uso: ./issue-prod-cert.sh domain.com email@example.com

set -e

DOMAIN=${1:-"yourdomain.com"}
EMAIL=${2:-"admin@yourdomain.com"}

echo "🔒 Emitindo certificado SSL para produção..."
echo "🌐 Domínio: $DOMAIN"
echo "📧 Email: $EMAIL"

# Verificar se o domínio está resolvendo
if ! nslookup $DOMAIN > /dev/null 2>&1; then
    echo "❌ Erro: Domínio $DOMAIN não está resolvendo"
    echo "💡 Verifique se o DNS está configurado corretamente"
    exit 1
fi

# Verificar se o servidor está acessível
if ! curl -s -o /dev/null -w "%{http_code}" http://$DOMAIN | grep -q "200\|301\|302"; then
    echo "❌ Erro: Servidor não está acessível em http://$DOMAIN"
    echo "💡 Verifique se o servidor está rodando e acessível"
    exit 1
fi

# Parar containers para evitar conflitos
echo "🛑 Parando containers..."
docker-compose down

# Iniciar apenas Nginx para o desafio ACME
echo "🌐 Iniciando Nginx para desafio ACME..."
docker-compose up -d nginx

# Aguardar Nginx ficar pronto
echo "⏳ Aguardando Nginx ficar pronto..."
sleep 5

# Emitir certificado
echo "📜 Emitindo certificado SSL..."
docker-compose run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/html/.well-known/acme-challenge \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    --force-renewal \
    -d $DOMAIN

# Verificar se o certificado foi emitido
if [ -f "./nginx/ssl/live/$DOMAIN/fullchain.pem" ]; then
    echo "✅ Certificado emitido com sucesso!"
    echo "📁 Certificado: ./nginx/ssl/live/$DOMAIN/fullchain.pem"
    echo "🔑 Chave privada: ./nginx/ssl/live/$DOMAIN/privkey.pem"
else
    echo "❌ Erro: Certificado não foi emitido"
    exit 1
fi

# Configurar renovação automática
echo "🔄 Configurando renovação automática..."
cat > ./certbot/letsencrypt/renewal-hooks/renew.sh << 'EOF'
#!/bin/bash
# Script de renovação automática
docker-compose run --rm certbot renew
docker-compose restart nginx
EOF

chmod +x ./certbot/letsencrypt/renewal-hooks/renew.sh

# Iniciar todos os containers
echo "🚀 Iniciando todos os containers..."
docker-compose up -d

echo ""
echo "🎉 Certificado SSL configurado com sucesso!"
echo ""
echo "📋 Informações importantes:"
echo "   🌐 Aplicação: https://$DOMAIN"
echo "   🔒 SSL: Ativo e funcionando"
echo "   🔄 Renovação: Automática"
echo ""
echo "📝 Comandos úteis:"
echo "   make logs          # Ver logs"
echo "   make ssl-renew      # Renovar certificado manualmente"
echo "   make down           # Parar ambiente"
echo ""
echo "⚠️  Lembre-se de configurar o arquivo .env com suas credenciais de produção"
