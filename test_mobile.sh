#!/bin/bash

echo "🔍 Testando conectividade móvel..."
echo "📡 IP do PC: 172.31.163.215"
echo "🔌 Porta: 3000"
echo ""

# Testar conectividade local
echo "1. Testando conectividade local..."
if curl -s -I http://localhost:3000 > /dev/null; then
    echo "✅ Servidor local funcionando"
else
    echo "❌ Servidor local não está funcionando"
fi

# Testar conectividade externa
echo "2. Testando conectividade externa..."
if curl -s -I http://172.31.163.215:3000 > /dev/null; then
    echo "✅ Servidor externo funcionando"
else
    echo "❌ Servidor externo não está funcionando"
fi

# Verificar processos
echo "3. Verificando processos..."
if docker ps | grep -q laravel_nginx; then
    echo "✅ Container nginx está rodando"
else
    echo "❌ Container nginx não está rodando"
fi

# Verificar portas
echo "4. Verificando portas..."
if ss -tlnp | grep -q :3000; then
    echo "✅ Porta 3000 está escutando"
else
    echo "❌ Porta 3000 não está escutando"
fi

echo ""
echo "📱 Para testar no celular:"
echo "http://172.31.163.215:3000"
echo ""
echo "📸 Para testar fotos:"
echo "http://172.31.163.215:3000/diario-obras/fotos/create"



