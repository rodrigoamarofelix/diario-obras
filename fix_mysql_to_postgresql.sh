#!/bin/bash

echo "🔧 Corrigindo consultas MySQL para PostgreSQL..."

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

    # Adicionar import do DatabaseHelper se não existir
    if ! grep -q "use App\\Helpers\\DatabaseHelper;" "$file"; then
        sed -i '/use App\\Http\\Controllers\\Controller;/a use App\\Helpers\\DatabaseHelper;' "$file"
    fi

    # Substituir DATE_FORMAT por DatabaseHelper
    sed -i 's/DATE_FORMAT(created_at, "%Y-%m") as mes/DatabaseHelper::formatDateForMonthGrouping()/g' "$file"
    sed -i 's/DATE_FORMAT(created_at, "%Y") as ano/DatabaseHelper::formatDateForYearGrouping()/g' "$file"
    sed -i 's/DATE_FORMAT(created_at, "%Y-%m-%d") as dia/DatabaseHelper::formatDateForDayGrouping()/g' "$file"
}

# Corrigir cada arquivo
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        fix_file "$file"
    else
        echo "⚠️  Arquivo $file não encontrado"
    fi
done

echo "✅ Correções concluídas!"
