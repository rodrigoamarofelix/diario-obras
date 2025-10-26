#!/bin/bash

# Script para expor a aplicação Laravel publicamente
echo "🚀 Configurando acesso público para o Diário de Obras..."

# Verificar se o Docker está rodando
if ! docker ps | grep -q laravel_nginx; then
    echo "❌ Container nginx não está rodando. Iniciando..."
    docker compose up -d nginx
    sleep 5
fi

# Verificar se a aplicação está respondendo
if curl -s http://localhost:8000 > /dev/null; then
    echo "✅ Aplicação funcionando localmente"
else
    echo "❌ Aplicação não está respondendo localmente"
    exit 1
fi

# Obter IP público
PUBLIC_IP=$(curl -s https://api.ipify.org)
echo "🌐 Seu IP público: $PUBLIC_IP"

# Verificar se a porta 8000 está aberta
echo "🔍 Verificando conectividade..."

# Tentar diferentes métodos de túnel
echo "📡 Tentando configurar túnel..."

# Método 1: Usar o ngrok que já baixamos
if [ -f "./ngrok" ]; then
    echo "🔧 Usando ngrok..."
    ./ngrok http 8000 --log=stdout > ngrok.log 2>&1 &
    NGROK_PID=$!
    sleep 5

    # Tentar obter a URL do ngrok
    NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o '"public_url":"[^"]*"' | head -1 | cut -d'"' -f4)

    if [ ! -z "$NGROK_URL" ]; then
        echo "🎉 Túnel ngrok criado com sucesso!"
        echo "📱 Acesse pelo celular: $NGROK_URL"
        echo "💡 Mantenha este terminal aberto para o túnel funcionar"
        echo "🛑 Para parar, pressione Ctrl+C"

        # Manter o script rodando
        wait $NGROK_PID
    else
        echo "❌ Falha ao criar túnel ngrok"
        kill $NGROK_PID 2>/dev/null
    fi
else
    echo "❌ ngrok não encontrado"
fi

echo "🔧 Alternativas:"
echo "1. Configure port forwarding no seu roteador (porta 8000)"
echo "2. Use um serviço de túnel online"
echo "3. Conecte o PC no Wi-Fi em vez do cabo"




