#!/bin/bash

echo "🔧 Corrigindo todas as consultas MySQL para PostgreSQL..."

# Lista de arquivos para corrigir
files=(
    "app/Http/Controllers/Api/PagamentoController.php"
    "app/Http/Controllers/Api/UserController.php"
    "app/Http/Controllers/Api/ContratoController.php"
    "app/Services/CacheService.php"
    "app/Livewire/DashboardComponent.php"
)

# Função para corrigir um arquivo
fix_file() {
    local file="$1"
    echo "📝 Corrigindo $file..."
    
    if [ -f "$file" ]; then
        # Substituir DATE_FORMAT por TO_CHAR diretamente
        sed -i 's/DATE_FORMAT(created_at, "%Y-%m") as mes/TO_CHAR(created_at, '\''YYYY-MM'\'') as mes/g' "$file"
        sed -i 's/DATE_FORMAT(created_at, "%Y") as ano/TO_CHAR(created_at, '\''YYYY'\'') as ano/g' "$file"
        sed -i 's/DATE_FORMAT(created_at, "%Y-%m-%d") as dia/TO_CHAR(created_at, '\''YYYY-MM-DD'\'') as dia/g' "$file"
        
        # Remover imports do DatabaseHelper se existirem
        sed -i '/use App\\Helpers\\DatabaseHelper;/d' "$file"
        
        echo "✅ $file corrigido"
    else
        echo "⚠️  Arquivo $file não encontrado"
    fi
}

# Corrigir cada arquivo
for file in "${files[@]}"; do
    fix_file "$file"
done

echo "✅ Todas as correções concluídas!"
