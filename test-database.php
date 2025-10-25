<?php
// test-database.php
// Execute: php test-database.php

echo "=== TESTE DE CONEXÃO COM BANCO ===\n\n";

// Carregar configuração do Laravel
$env = parse_ini_file('.env');

echo "Configuração atual:\n";
echo "DB_CONNECTION: " . ($env['DB_CONNECTION'] ?? 'não definido') . "\n";
echo "DB_HOST: " . ($env['DB_HOST'] ?? 'não definido') . "\n";
echo "DB_PORT: " . ($env['DB_PORT'] ?? 'não definido') . "\n";
echo "DB_DATABASE: " . ($env['DB_DATABASE'] ?? 'não definido') . "\n";
echo "DB_USERNAME: " . ($env['DB_USERNAME'] ?? 'não definido') . "\n\n";

// Testar conexão MySQL
if ($env['DB_CONNECTION'] === 'mysql') {
    echo "Testando conexão MySQL...\n";

    $host = $env['DB_HOST'];
    $port = $env['DB_PORT'];
    $dbname = $env['DB_DATABASE'];
    $username = $env['DB_USERNAME'];
    $password = $env['DB_PASSWORD'];

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ Conexão MySQL bem-sucedida!\n";

        // Verificar tabelas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Tabelas encontradas: " . count($tables) . "\n";
        foreach ($tables as $table) {
            echo "  - $table\n";
        }

        // Verificar se tabela pessoas existe
        if (in_array('pessoas', $tables)) {
            echo "\n✅ Tabela 'pessoas' encontrada!\n";

            // Verificar estrutura
            $stmt = $pdo->query("DESCRIBE pessoas");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "Colunas da tabela pessoas:\n";
            foreach ($columns as $column) {
                echo "  - {$column['Field']} ({$column['Type']})\n";
            }
        } else {
            echo "\n⚠️ Tabela 'pessoas' não encontrada. Execute: php artisan migrate\n";
        }

    } catch (PDOException $e) {
        echo "❌ Erro na conexão MySQL: " . $e->getMessage() . "\n";
        echo "\nPossíveis soluções:\n";
        echo "1. Verificar se o container MySQL está rodando: docker ps\n";
        echo "2. Verificar credenciais no .env\n";
        echo "3. Usar SQLite como alternativa\n";
    }
}

echo "\n=== TESTE CONCLUÍDO ===\n";
