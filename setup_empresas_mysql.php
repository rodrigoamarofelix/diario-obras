<?php

// Script para criar tabela empresas e inserir dados no MySQL
// Configuração do banco MySQL

try {
    // Configuração do MySQL (ajuste conforme sua configuração)
    $host = '127.0.0.1';
    $port = '3306';
    $dbname = 'diario_obras';
    $username = 'root';
    $password = '';

    // Conectar ao MySQL
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao MySQL com sucesso!\n";
    echo "📊 Banco: $dbname\n";

    echo "🚀 Criando tabela empresas...\n";

    // SQL para criar tabela empresas no MySQL
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS empresas (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        razao_social VARCHAR(255) NOT NULL,
        cnpj VARCHAR(18) NOT NULL UNIQUE,
        email VARCHAR(255) NULL,
        telefone VARCHAR(20) NULL,
        whatsapp VARCHAR(20) NULL,
        cep VARCHAR(10) NOT NULL,
        endereco VARCHAR(255) NOT NULL,
        numero VARCHAR(20) NULL,
        complemento VARCHAR(255) NULL,
        bairro VARCHAR(255) NOT NULL,
        cidade VARCHAR(255) NOT NULL,
        estado VARCHAR(2) NOT NULL,
        pais VARCHAR(50) DEFAULT 'Brasil',
        site VARCHAR(255) NULL,
        observacoes TEXT NULL,
        ativo BOOLEAN DEFAULT TRUE,
        created_by BIGINT UNSIGNED DEFAULT 1,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP NULL,

        INDEX idx_cnpj (cnpj),
        INDEX idx_nome (nome),
        INDEX idx_razao_social (razao_social),
        INDEX idx_ativo (ativo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $pdo->exec($createTableSQL);
    echo "✅ Tabela empresas criada com sucesso!\n";

    echo "📊 Inserindo dados fictícios...\n";

    // Dados das empresas
    $empresas = [
        [
            'Construtora ABC Ltda',
            'Construtora ABC Ltda',
            '12345678000195',
            'contato@construtoraabc.com.br',
            '1133334444',
            '11999998888',
            '01310100',
            'Av. Paulista',
            '1000',
            'Sala 101',
            'Bela Vista',
            'São Paulo',
            'SP',
            'Brasil',
            'https://www.construtoraabc.com.br',
            'Empresa especializada em construção civil com mais de 20 anos de experiência no mercado.',
            1,
            1
        ],
        [
            'Engenharia XYZ S.A.',
            'Engenharia XYZ S.A.',
            '98765432000123',
            'info@engenhariaxyz.com.br',
            '2133335555',
            '21988887777',
            '20040020',
            'Rua da Carioca',
            '500',
            'Andar 15',
            'Centro',
            'Rio de Janeiro',
            'RJ',
            'Brasil',
            'https://www.engenhariaxyz.com.br',
            'Empresa focada em projetos de infraestrutura e grandes obras públicas.',
            1,
            1
        ],
        [
            'Obras e Construções MG',
            'Obras e Construções MG Ltda',
            '11223344000156',
            'contato@obrasmg.com.br',
            '3133336666',
            '31977776666',
            '30112000',
            'Av. Afonso Pena',
            '2000',
            'Conjunto 301',
            'Centro',
            'Belo Horizonte',
            'MG',
            'Brasil',
            'https://www.obrasmg.com.br',
            'Especializada em obras residenciais e comerciais na região metropolitana de Belo Horizonte.',
            1,
            1
        ],
        [
            'Construtora Sul Brasil',
            'Construtora Sul Brasil Ltda',
            '55667788000199',
            'vendas@sulbrasil.com.br',
            '5133337777',
            '51988889999',
            '90020000',
            'Rua da Praia',
            '150',
            'Loja 1',
            'Centro Histórico',
            'Porto Alegre',
            'RS',
            'Brasil',
            'https://www.sulbrasil.com.br',
            'Empresa tradicional do sul do país, especializada em construção de alto padrão.',
            1,
            1
        ],
        [
            'Nordeste Construtora',
            'Nordeste Construtora S.A.',
            '99887766000144',
            'comercial@nordesteconstrutora.com.br',
            '8133338888',
            '81977778888',
            '50000000',
            'Av. Boa Viagem',
            '3000',
            'Torre A',
            'Boa Viagem',
            'Recife',
            'PE',
            'Brasil',
            'https://www.nordesteconstrutora.com.br',
            'Líder em construção civil no nordeste brasileiro, com projetos inovadores.',
            1,
            1
        ]
    ];

    // SQL para inserir dados
    $insertSQL = "
    INSERT INTO empresas (
        nome, razao_social, cnpj, email, telefone, whatsapp,
        cep, endereco, numero, complemento, bairro, cidade, estado, pais, site, observacoes, ativo, created_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($insertSQL);

    foreach ($empresas as $empresa) {
        $stmt->execute($empresa);
        echo "✅ Empresa '{$empresa[0]}' inserida com sucesso!\n";
    }

    echo "\n🎉 Sistema de empresas configurado com sucesso!\n";
    echo "📊 Total de empresas inseridas: " . count($empresas) . "\n";

    // Verificar dados inseridos
    echo "\n📋 Empresas cadastradas:\n";
    $stmt = $pdo->query("SELECT id, nome, cidade, estado, cnpj FROM empresas ORDER BY nome");
    $empresasInseridas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($empresasInseridas as $empresa) {
        echo "- {$empresa['nome']} ({$empresa['cidade']}/{$empresa['estado']}) - CNPJ: {$empresa['cnpj']}\n";
    }

    // Verificar estrutura da tabela
    echo "\n🔍 Estrutura da tabela empresas:\n";
    $stmt = $pdo->query("DESCRIBE empresas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Key']}\n";
    }

    echo "\n🚀 Próximos passos:\n";
    echo "1. Acesse o sistema web\n";
    echo "2. Faça login\n";
    echo "3. Clique em 'Empresas' no menu lateral\n";
    echo "4. Teste todas as funcionalidades!\n";
    echo "5. Teste a busca de CEP\n";
    echo "6. Teste a validação de CNPJ\n";

} catch (PDOException $e) {
    echo "❌ Erro de banco de dados: " . $e->getMessage() . "\n";
    echo "💡 Dicas:\n";
    echo "- Verifique se o MySQL está rodando\n";
    echo "- Verifique se o banco 'diario_obras' existe\n";
    echo "- Verifique as credenciais de acesso\n";
    echo "- Execute: CREATE DATABASE IF NOT EXISTS diario_obras;\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}



