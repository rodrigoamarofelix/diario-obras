<?php
/**
 * Script para converter dados do MySQL para PostgreSQL
 * Execute: php convert_to_postgresql.php mysql_export_YYYY-MM-DD_HH-MM-SS.json
 */

if ($argc < 2) {
    echo "❌ Uso: php convert_to_postgresql.php arquivo_export.json\n";
    exit(1);
}

$json_file = $argv[1];

if (!file_exists($json_file)) {
    echo "❌ Arquivo não encontrado: $json_file\n";
    exit(1);
}

$export_data = json_decode(file_get_contents($json_file), true);

if (!$export_data) {
    echo "❌ Erro ao ler arquivo JSON\n";
    exit(1);
}

echo "🔄 Convertendo dados do MySQL para PostgreSQL...\n";

$sql_file = 'postgresql_import_' . date('Y-m-d_H-i-s') . '.sql';
$sql_content = "-- Script de importação para PostgreSQL\n";
$sql_content .= "-- Gerado automaticamente em " . date('Y-m-d H:i:s') . "\n\n";

foreach ($export_data as $table => $table_data) {
    echo "📋 Processando tabela: $table\n";
    
    $columns = $table_data['columns'];
    $data = $table_data['data'];
    
    if (empty($data)) {
        echo "   ⚠️  Tabela vazia, pulando...\n";
        continue;
    }
    
    // Converter tipos de dados MySQL para PostgreSQL
    $pg_columns = [];
    foreach ($columns as $column) {
        $pg_type = convert_mysql_to_pg_type($column['Type']);
        $pg_columns[] = "{$column['Field']} $pg_type";
    }
    
    // Gerar INSERT statements
    foreach ($data as $row) {
        $values = [];
        foreach ($row as $key => $value) {
            if ($value === null) {
                $values[] = 'NULL';
            } else {
                // Escapar aspas simples
                $escaped_value = str_replace("'", "''", $value);
                $values[] = "'$escaped_value'";
            }
        }
        
        $sql_content .= "INSERT INTO $table (" . implode(', ', array_keys($row)) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
    
    echo "   ✅ " . count($data) . " registros convertidos\n";
}

file_put_contents($sql_file, $sql_content);

echo "💾 Script PostgreSQL salvo em: $sql_file\n";
echo "📊 Total de tabelas processadas: " . count($export_data) . "\n";

/**
 * Converte tipos de dados MySQL para PostgreSQL
 */
function convert_mysql_to_pg_type($mysql_type) {
    $mysql_type = strtolower($mysql_type);
    
    // Mapeamento de tipos
    $type_map = [
        'tinyint(1)' => 'BOOLEAN',
        'tinyint' => 'SMALLINT',
        'smallint' => 'SMALLINT',
        'mediumint' => 'INTEGER',
        'int' => 'INTEGER',
        'bigint' => 'BIGINT',
        'decimal' => 'DECIMAL',
        'float' => 'REAL',
        'double' => 'DOUBLE PRECISION',
        'varchar' => 'VARCHAR',
        'char' => 'CHAR',
        'text' => 'TEXT',
        'longtext' => 'TEXT',
        'mediumtext' => 'TEXT',
        'tinytext' => 'TEXT',
        'date' => 'DATE',
        'time' => 'TIME',
        'datetime' => 'TIMESTAMP',
        'timestamp' => 'TIMESTAMP',
        'year' => 'INTEGER',
        'blob' => 'BYTEA',
        'longblob' => 'BYTEA',
        'mediumblob' => 'BYTEA',
        'tinyblob' => 'BYTEA',
        'json' => 'JSON',
        'enum' => 'VARCHAR',
        'set' => 'VARCHAR'
    ];
    
    // Extrair tipo base
    $base_type = preg_replace('/\([^)]*\)/', '', $mysql_type);
    
    if (isset($type_map[$base_type])) {
        return $type_map[$base_type];
    }
    
    // Se não encontrar, usar VARCHAR como padrão
    return 'VARCHAR';
}
?>
