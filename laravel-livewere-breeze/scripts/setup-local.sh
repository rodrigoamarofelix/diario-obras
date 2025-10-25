#!/bin/bash

# Script para configurar ambiente local
# Uso: ./setup-local.sh

set -e

echo "🚀 Configurando ambiente local..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Inicie o Docker primeiro."
    exit 1
fi

# Copiar arquivo de ambiente
if [ ! -f .env ]; then
    echo "📝 Copiando arquivo de ambiente..."
    cp .env.example .env
    echo "✅ Arquivo .env criado"
else
    echo "⚠️  Arquivo .env já existe"
fi

# Construir e iniciar containers
echo "🐳 Construindo e iniciando containers..."
docker-compose up -d --build

# Aguardar containers ficarem prontos
echo "⏳ Aguardando containers ficarem prontos..."
sleep 10

# Instalar dependências PHP
echo "📦 Instalando dependências PHP..."
docker-compose exec php-fpm composer install

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker-compose exec php-fpm php artisan key:generate

# Executar migrações
echo "🗄️  Executando migrações..."
docker-compose exec php-fpm php artisan migrate:fresh --seed

# Instalar dependências Node
echo "📦 Instalando dependências Node..."
docker-compose exec node npm install

# Configurar hosts (Linux/Mac)
if [[ "$OSTYPE" == "linux-gnu"* ]] || [[ "$OSTYPE" == "darwin"* ]]; then
    echo "🌐 Configurando hosts..."
    if ! grep -q "laravel.local" /etc/hosts; then
        echo "127.0.0.1 laravel.local" | sudo tee -a /etc/hosts
        echo "✅ Host laravel.local adicionado"
    else
        echo "⚠️  Host laravel.local já existe"
    fi
fi

echo ""
echo "🎉 Ambiente local configurado com sucesso!"
echo ""
echo "📋 Informações importantes:"
echo "   🌐 Aplicação: http://laravel.local"
echo "   📧 Mailhog: http://localhost:8025"
echo "   🔧 Vite HMR: http://localhost:5173"
echo ""
echo "📝 Comandos úteis:"
echo "   make logs          # Ver logs"
echo "   make shell         # Shell no container PHP"
echo "   make artisan       # Comandos Artisan"
echo "   make composer      # Comandos Composer"
echo "   make npm           # Comandos NPM"
echo ""
echo "🛑 Para parar: make down"
