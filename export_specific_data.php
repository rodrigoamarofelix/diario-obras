<?php
// export_specific_data.php

// Configurações do MySQL (Docker)
$mysql_config = [
    'host' => 'mysql',
    'port' => 3306,
    'database' => 'diarioobras',
    'username' => 'laravel_user',
    'password' => 'laravel_pass',
    'charset' => 'utf8mb4'
];

try {
    // Conectar ao MySQL
    $dsn = "mysql:host={$mysql_config['host']};port={$mysql_config['port']};dbname={$mysql_config['database']};charset={$mysql_config['charset']}";
    $pdo = new PDO($dsn, $mysql_config['username'], $mysql_config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao MySQL com sucesso!\n";

    // Tabelas específicas que você mencionou
    $tables_to_export = [
        'empresas',
        'contratos', 
        'projetos',
        'equipe_obras',
        'pessoas',
        'funcoes',
        'lotacoes',
        'projeto_empresa'
    ];

    $data = [];
    $total_records = 0;

    foreach ($tables_to_export as $table) {
        echo "📤 Exportando tabela: $table\n";
        
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $table_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $record_count = count($table_data);
        $data[$table] = $table_data;
        $total_records += $record_count;
        
        echo "   ✅ $record_count registros exportados\n";
    }

    // Salvar dados em JSON
    $filename = 'specific_data_export_' . date('Y-m-d_H-i-s') . '.json';
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

    echo "\n💾 Dados exportados para: $filename\n";
    echo "📊 Total de tabelas: " . count($tables_to_export) . "\n";
    echo "📊 Total de registros: $total_records\n";

    // Mostrar resumo por tabela
    echo "\n📋 Resumo por tabela:\n";
    foreach ($tables_to_export as $table) {
        $count = count($data[$table]);
        echo "   - $table: $count registros\n";
    }

} catch (PDOException $e) {
    echo "❌ Erro ao conectar ao MySQL: " . $e->getMessage() . "\n";
}
?>
