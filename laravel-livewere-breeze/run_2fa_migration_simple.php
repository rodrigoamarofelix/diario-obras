<?php

// Script simples para executar migration do 2FA
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar conexão com banco
$config = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'sgc',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

try {
    // Conectar ao banco
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se os campos já existem
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'two_factor_enabled'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Campos do 2FA já existem na tabela users.\n";
        exit;
    }

    // Executar migration
    $sql = "ALTER TABLE users
            ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
            ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
            ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
            ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes";

    $pdo->exec($sql);
    echo "✅ Migration do 2FA executada com sucesso!\n";
    echo "✅ Campos adicionados: two_factor_enabled, two_factor_secret, two_factor_backup_codes, two_factor_enabled_at\n";

} catch (PDOException $e) {
    echo "❌ Erro ao executar migration: " . $e->getMessage() . "\n";
    echo "💡 Verifique se o banco de dados 'sgc' existe e está acessível.\n";
}


