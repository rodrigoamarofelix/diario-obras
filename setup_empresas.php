<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Configuração do banco de dados
$capsule = new Capsule;

// Configuração para SQLite (mais simples para teste)
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../database/database.sqlite',
    'prefix' => '',
]);

// Configuração para MySQL (descomente se usar MySQL)
/*
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'diario_obras',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
*/

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "🚀 Criando tabela empresas...\n";

    // Criar tabela empresas
    Capsule::schema()->create('empresas', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('razao_social');
        $table->string('cnpj', 18)->unique();
        $table->string('email')->nullable();
        $table->string('telefone', 20)->nullable();
        $table->string('whatsapp', 20)->nullable();
        $table->string('cep', 10);
        $table->string('endereco');
        $table->string('numero', 20)->nullable();
        $table->string('complemento')->nullable();
        $table->string('bairro');
        $table->string('cidade');
        $table->string('estado', 2);
        $table->string('pais', 50)->default('Brasil');
        $table->string('site')->nullable();
        $table->text('observacoes')->nullable();
        $table->boolean('ativo')->default(true);
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
        $table->timestamps();
        $table->softDeletes();

        $table->index(['cnpj']);
        $table->index(['nome']);
        $table->index(['razao_social']);
        $table->index(['ativo']);
    });

    echo "✅ Tabela empresas criada com sucesso!\n";

    echo "📊 Inserindo dados fictícios...\n";

    // Dados fictícios das empresas
    $empresas = [
        [
            'nome' => 'Construtora ABC Ltda',
            'razao_social' => 'Construtora ABC Ltda',
            'cnpj' => '12345678000195',
            'email' => 'contato@construtoraabc.com.br',
            'telefone' => '1133334444',
            'whatsapp' => '11999998888',
            'cep' => '01310100',
            'endereco' => 'Av. Paulista',
            'numero' => '1000',
            'complemento' => 'Sala 101',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'pais' => 'Brasil',
            'site' => 'https://www.construtoraabc.com.br',
            'observacoes' => 'Empresa especializada em construção civil com mais de 20 anos de experiência no mercado.',
            'ativo' => true,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'Engenharia XYZ S.A.',
            'razao_social' => 'Engenharia XYZ S.A.',
            'cnpj' => '98765432000123',
            'email' => 'info@engenhariaxyz.com.br',
            'telefone' => '2133335555',
            'whatsapp' => '21988887777',
            'cep' => '20040020',
            'endereco' => 'Rua da Carioca',
            'numero' => '500',
            'complemento' => 'Andar 15',
            'bairro' => 'Centro',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'pais' => 'Brasil',
            'site' => 'https://www.engenhariaxyz.com.br',
            'observacoes' => 'Empresa focada em projetos de infraestrutura e grandes obras públicas.',
            'ativo' => true,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'Obras e Construções MG',
            'razao_social' => 'Obras e Construções MG Ltda',
            'cnpj' => '11223344000156',
            'email' => 'contato@obrasmg.com.br',
            'telefone' => '3133336666',
            'whatsapp' => '31977776666',
            'cep' => '30112000',
            'endereco' => 'Av. Afonso Pena',
            'numero' => '2000',
            'complemento' => 'Conjunto 301',
            'bairro' => 'Centro',
            'cidade' => 'Belo Horizonte',
            'estado' => 'MG',
            'pais' => 'Brasil',
            'site' => 'https://www.obrasmg.com.br',
            'observacoes' => 'Especializada em obras residenciais e comerciais na região metropolitana de Belo Horizonte.',
            'ativo' => true,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'Construtora Sul Brasil',
            'razao_social' => 'Construtora Sul Brasil Ltda',
            'cnpj' => '55667788000199',
            'email' => 'vendas@sulbrasil.com.br',
            'telefone' => '5133337777',
            'whatsapp' => '51988889999',
            'cep' => '90020000',
            'endereco' => 'Rua da Praia',
            'numero' => '150',
            'complemento' => 'Loja 1',
            'bairro' => 'Centro Histórico',
            'cidade' => 'Porto Alegre',
            'estado' => 'RS',
            'pais' => 'Brasil',
            'site' => 'https://www.sulbrasil.com.br',
            'observacoes' => 'Empresa tradicional do sul do país, especializada em construção de alto padrão.',
            'ativo' => true,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'nome' => 'Nordeste Construtora',
            'razao_social' => 'Nordeste Construtora S.A.',
            'cnpj' => '99887766000144',
            'email' => 'comercial@nordesteconstrutora.com.br',
            'telefone' => '8133338888',
            'whatsapp' => '81977778888',
            'cep' => '50000000',
            'endereco' => 'Av. Boa Viagem',
            'numero' => '3000',
            'complemento' => 'Torre A',
            'bairro' => 'Boa Viagem',
            'cidade' => 'Recife',
            'estado' => 'PE',
            'pais' => 'Brasil',
            'site' => 'https://www.nordesteconstrutora.com.br',
            'observacoes' => 'Líder em construção civil no nordeste brasileiro, com projetos inovadores.',
            'ativo' => true,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ];

    // Inserir empresas
    foreach ($empresas as $empresa) {
        Capsule::table('empresas')->insert($empresa);
        echo "✅ Empresa '{$empresa['nome']}' inserida com sucesso!\n";
    }

    echo "\n🎉 Sistema de empresas configurado com sucesso!\n";
    echo "📊 Total de empresas inseridas: " . count($empresas) . "\n";

    // Verificar dados inseridos
    echo "\n📋 Empresas cadastradas:\n";
    $empresasInseridas = Capsule::table('empresas')->select('id', 'nome', 'cidade', 'estado', 'cnpj')->get();

    foreach ($empresasInseridas as $empresa) {
        echo "- {$empresa->nome} ({$empresa->cidade}/{$empresa->estado}) - CNPJ: {$empresa->cnpj}\n";
    }

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📝 Detalhes: " . $e->getTraceAsString() . "\n";
}



