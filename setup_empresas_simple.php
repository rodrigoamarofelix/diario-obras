<?php

// Script simples para criar tabela empresas e inserir dados
// Usando PDO para conexão direta com banco

try {
    // Configuração do banco (ajuste conforme sua configuração)
    $host = 'localhost';
    $dbname = 'diario_obras';
    $username = 'root';
    $password = '';

    // Tentar conectar com SQLite primeiro
    $dsn = 'sqlite:' . __DIR__ . '/database/database.sqlite';

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Conectado ao SQLite\n";
    } catch (PDOException $e) {
        // Se SQLite falhar, tentar MySQL
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Conectado ao MySQL\n";
    }

    echo "🚀 Criando tabela empresas...\n";

    // SQL para criar tabela
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS empresas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome VARCHAR(255) NOT NULL,
        razao_social VARCHAR(255) NOT NULL,
        cnpj VARCHAR(18) NOT NULL UNIQUE,
        email VARCHAR(255),
        telefone VARCHAR(20),
        whatsapp VARCHAR(20),
        cep VARCHAR(10) NOT NULL,
        endereco VARCHAR(255) NOT NULL,
        numero VARCHAR(20),
        complemento VARCHAR(255),
        bairro VARCHAR(255) NOT NULL,
        cidade VARCHAR(255) NOT NULL,
        estado VARCHAR(2) NOT NULL,
        pais VARCHAR(50) DEFAULT 'Brasil',
        site VARCHAR(255),
        observacoes TEXT,
        ativo BOOLEAN DEFAULT 1,
        created_by INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        deleted_at DATETIME
    )
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

    echo "\n🚀 Próximos passos:\n";
    echo "1. Acesse o sistema web\n";
    echo "2. Faça login\n";
    echo "3. Clique em 'Empresas' no menu lateral\n";
    echo "4. Teste todas as funcionalidades!\n";

} catch (PDOException $e) {
    echo "❌ Erro de banco de dados: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}



