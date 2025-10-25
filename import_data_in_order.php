<?php
// import_data_in_order.php

if ($argc < 2) {
    echo "Uso: php import_data_in_order.php <arquivo_json_exportado>\n";
    exit(1);
}

$jsonFile = $argv[1];

if (!file_exists($jsonFile)) {
    echo "Erro: Arquivo JSON '$jsonFile' não encontrado.\n";
    exit(1);
}

$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erro ao decodificar JSON: " . json_last_error_msg() . "\n";
    exit(1);
}

// Ordem de importação respeitando dependências
$import_order = [
    'users',           // Primeiro: usuários (referenciado por outras tabelas)
    'funcoes',         // Segundo: funções (referenciado por pessoas)
    'lotacoes',        // Terceiro: lotações (referenciado por pessoas)
    'pessoas',         // Quarto: pessoas (referenciado por projetos e equipe)
    'empresas',        // Quinto: empresas (referenciado por projetos)
    'projetos',        // Sexto: projetos (referenciado por equipe e projeto_empresa)
    'contratos',       // Sétimo: contratos
    'equipe_obras',    // Oitavo: equipe de obras
    'projeto_empresa', // Nono: relação projeto-empresa
    'medicoes',        // Décimo: medições
    'pagamentos',      // Décimo primeiro: pagamentos
    'auditorias',      // Último: auditorias
];

$sqlOutput = '';

echo "🔄 Convertendo dados para PostgreSQL na ordem correta...\n";

foreach ($import_order as $tableName) {
    if (!isset($data[$tableName]) || empty($data[$tableName])) {
        echo "📋 Processando tabela: $tableName\n";
        echo "   ⚠️  Tabela vazia ou não encontrada, pulando...\n";
        continue;
    }

    echo "📋 Processando tabela: $tableName\n";

    $rows = $data[$tableName];
    $record_count = count($rows);

    if ($record_count === 0) {
        echo "   ⚠️  Tabela vazia, pulando...\n";
        continue;
    }

    // Verificar se há dados válidos
    if (!is_array($rows) || empty($rows) || !is_array($rows[0])) {
        echo "   ⚠️  Dados inválidos na tabela, pulando...\n";
        continue;
    }

    // Escape table name for PostgreSQL
    $pgTableName = '"' . $tableName . '"';

    $columns = array_keys($rows[0]);
    $pgColumns = implode(', ', array_map(fn($col) => '"' . $col . '"', $columns));

    foreach ($rows as $row) {
        $values = [];
        foreach ($columns as $col) {
            $value = $row[$col];
            if ($value === null) {
                $values[] = 'NULL';
            } elseif (is_numeric($value)) {
                $values[] = $value;
            } else {
                $values[] = "'" . str_replace("'", "''", $value) . "'";
            }
        }
        $pgValues = implode(', ', $values);
        $sqlOutput .= "INSERT INTO $pgTableName ($pgColumns) VALUES ($pgValues);\n";
    }

    echo "   ✅ $record_count registros convertidos\n";
}

$outputFilename = 'postgresql_ordered_import_' . date('Y-m-d_H-i-s') . '.sql';
file_put_contents($outputFilename, $sqlOutput);

echo "\n💾 Script PostgreSQL salvo em: $outputFilename\n";
echo "📊 Total de tabelas processadas: " . count($import_order) . "\n";
echo "✅ Conversão concluída!\n";
?>
