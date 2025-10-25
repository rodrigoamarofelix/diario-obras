<?php
/**
 * Script para exportar dados do MySQL para PostgreSQL
 * Execute: php export_mysql_data.php
 */

// Configurações do MySQL (Docker)
$mysql_config = [
    'host' => 'mysql',  // nome do serviço no Docker
    'port' => 3306,
    'database' => 'diarioobras',  // nome do banco no Docker
    'username' => 'laravel_user',  // usuário configurado no Docker
    'password' => 'laravel_pass',  // senha configurada no Docker
    'charset' => 'utf8mb4'
];

try {
    // Conectar ao MySQL
    $dsn = "mysql:host={$mysql_config['host']};port={$mysql_config['port']};dbname={$mysql_config['database']};charset={$mysql_config['charset']}";
    $pdo = new PDO($dsn, $mysql_config['username'], $mysql_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao MySQL com sucesso!\n";

    // Obter lista de tabelas
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tabelas encontradas: " . implode(', ', $tables) . "\n";

    $export_data = [];

    foreach ($tables as $table) {
        echo "📤 Exportando tabela: $table\n";

        // Obter estrutura da tabela
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);

        // Obter dados da tabela
        $data = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

        $export_data[$table] = [
            'columns' => $columns,
            'data' => $data
        ];

        echo "   ✅ " . count($data) . " registros exportados\n";
    }

    // Salvar dados em arquivo JSON
    $json_file = 'mysql_export_' . date('Y-m-d_H-i-s') . '.json';
    file_put_contents($json_file, json_encode($export_data, JSON_PRETTY_PRINT));

    echo "💾 Dados exportados para: $json_file\n";
    echo "📊 Total de tabelas: " . count($tables) . "\n";
    echo "📊 Total de registros: " . array_sum(array_map(function($table) { return count($table['data']); }, $export_data)) . "\n";

} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao MySQL: " . $e->getMessage() . "\n";
    echo "🔧 Verifique as configurações de conexão no arquivo.\n";
}
?>
