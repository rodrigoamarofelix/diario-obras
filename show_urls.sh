#!/bin/bash

echo "📱 URLs para teste no celular:"
echo "================================"

# Obter IP local
IP=$(ip route get 8.8.8.8 | awk '{print $7; exit}')

echo "🌐 IP do PC: $IP"
echo ""

# URLs para testar
echo "🔗 Teste estas URLs no seu celular:"
echo ""
echo "1. http://$IP:3000"
echo "2. http://$IP:8081"
echo "3. http://$IP:9000"
echo "4. http://$IP:9001"
echo "5. http://$IP:9002"
echo ""

echo "📸 Para testar o sistema de fotos, adicione:"
echo "/diario-obras/fotos/create"
echo ""
echo "Exemplo: http://$IP:3000/diario-obras/fotos/create"
echo ""

echo "🔍 Verificando quais portas estão ativas:"
echo ""

# Verificar portas
for port in 3000 8081 9000 9001 9002; do
    if ss -tlnp | grep -q ":$port "; then
        echo "✅ Porta $port: ATIVA"
    else
        echo "❌ Porta $port: INATIVA"
    fi
done

echo ""
echo "💡 Dica: Teste primeiro a porta 3000 (Docker)"
echo "Se não funcionar, teste as outras portas"


