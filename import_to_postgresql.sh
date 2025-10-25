#!/bin/bash

echo "🐘 Importando dados para PostgreSQL local"
echo "========================================"

# Verificar se o PostgreSQL está rodando
if ! docker compose ps postgresql | grep -q "Up"; then
    echo "🐳 Iniciando PostgreSQL..."
    docker compose up -d postgresql
    echo "⏳ Aguardando PostgreSQL inicializar..."
    sleep 10
fi

# Encontrar o arquivo SQL mais recente
SQL_FILE=$(ls -t postgresql_import_*.sql | head -n1)

if [ -z "$SQL_FILE" ]; then
    echo "❌ Arquivo SQL não encontrado. Execute primeiro a migração MySQL → PostgreSQL."
    exit 1
fi

echo "📄 Arquivo encontrado: $SQL_FILE"

# Executar migrations do Laravel primeiro (criar estrutura das tabelas)
echo "🏗️  Executando migrations do Laravel..."
docker compose exec php-fpm php artisan migrate --force

if [ $? -eq 0 ]; then
    echo "✅ Migrations executadas com sucesso!"

    # Importar dados
    echo "📥 Importando dados para PostgreSQL..."
    docker compose exec -T postgresql psql -U laravel_user -d diarioobras_pg < "$SQL_FILE"

    if [ $? -eq 0 ]; then
        echo "✅ Dados importados com sucesso!"

        echo ""
        echo "🎉 Importação concluída!"
        echo "======================="
        echo "🌐 Acesse o Adminer: http://localhost:8081"
        echo ""
        echo "📋 Configurações do Adminer:"
        echo "   - Sistema: PostgreSQL"
        echo "   - Servidor: postgresql"
        echo "   - Usuário: laravel_user"
        echo "   - Senha: laravel_pass"
        echo "   - Banco: diarioobras_pg"

    else
        echo "❌ Erro ao importar dados"
        exit 1
    fi
else
    echo "❌ Erro ao executar migrations"
    exit 1
fi
