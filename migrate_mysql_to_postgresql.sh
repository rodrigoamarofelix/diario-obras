#!/bin/bash

echo "🚀 Iniciando processo de migração MySQL → PostgreSQL"
echo "=================================================="

# Verificar se o Docker está rodando
if ! docker ps > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Inicie o Docker primeiro."
    exit 1
fi

echo "🐳 Iniciando containers Docker..."
docker-compose up -d mysql adminer phpmyadmin

echo "⏳ Aguardando MySQL inicializar..."
sleep 10

echo "📊 Verificando conexão com MySQL..."
docker-compose exec mysql mysql -u laravel_user -plaravel_pass -e "SHOW DATABASES;" diarioobras

if [ $? -eq 0 ]; then
    echo "✅ MySQL conectado com sucesso!"
else
    echo "❌ Erro ao conectar com MySQL. Verifique as configurações."
    exit 1
fi

echo "📤 Exportando dados do MySQL..."
php export_mysql_data.php

if [ $? -eq 0 ]; then
    echo "✅ Dados exportados com sucesso!"
    
    # Encontrar o arquivo JSON mais recente
    JSON_FILE=$(ls -t mysql_export_*.json | head -n1)
    echo "📄 Arquivo encontrado: $JSON_FILE"
    
    echo "🔄 Convertendo para PostgreSQL..."
    php convert_to_postgresql.php "$JSON_FILE"
    
    if [ $? -eq 0 ]; then
        echo "✅ Conversão concluída!"
        
        # Encontrar o arquivo SQL mais recente
        SQL_FILE=$(ls -t postgresql_import_*.sql | head -n1)
        echo "📄 Arquivo SQL gerado: $SQL_FILE"
        
        echo ""
        echo "🎉 Migração concluída com sucesso!"
        echo "=================================="
        echo "📊 Arquivos gerados:"
        echo "   - $JSON_FILE (dados exportados)"
        echo "   - $SQL_FILE (script PostgreSQL)"
        echo ""
        echo "🌐 Acesse as ferramentas de administração:"
        echo "   - phpMyAdmin: http://localhost:8080"
        echo "   - Adminer: http://localhost:8081"
        echo ""
        echo "📋 Próximos passos:"
        echo "   1. Configure o banco PostgreSQL no Railway"
        echo "   2. Execute as migrations do Laravel"
        echo "   3. Importe os dados usando o arquivo: $SQL_FILE"
        
    else
        echo "❌ Erro na conversão para PostgreSQL"
        exit 1
    fi
else
    echo "❌ Erro ao exportar dados do MySQL"
    exit 1
fi
