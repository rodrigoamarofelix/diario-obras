<?php
// test-cadastro-pendente.php
// Execute: php test-cadastro-pendente.php

require_once 'vendor/autoload.php';

use App\Models\Pessoa;
use App\Services\ReceitaFederalService;

echo "=== TESTE DO SISTEMA DE CADASTRO PENDENTE ===\n\n";

// Teste 1: Verificar se a classe existe
echo "1. Testando ReceitaFederalService...\n";
try {
    $service = new ReceitaFederalService();
    echo "   ✅ ReceitaFederalService carregado com sucesso\n";
} catch (Exception $e) {
    echo "   ❌ Erro: " . $e->getMessage() . "\n";
}

// Teste 2: Consultar CPF de teste
echo "\n2. Testando consulta de CPF...\n";
try {
    $resultado = $service->consultarCpf('11144477735');
    if ($resultado['success']) {
        echo "   ✅ CPF válido: " . $resultado['nome'] . "\n";
    } else {
        echo "   ⚠️ CPF rejeitado: " . $resultado['message'] . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro na consulta: " . $e->getMessage() . "\n";
}

// Teste 3: Consultar CPF não listado
echo "\n3. Testando CPF não listado...\n";
try {
    $resultado = $service->consultarCpf('99988877766');
    if ($resultado['success']) {
        echo "   ✅ CPF válido: " . $resultado['nome'] . "\n";
    } else {
        echo "   ⚠️ CPF rejeitado: " . $resultado['message'] . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro na consulta: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
echo "Se todos os testes passaram, o sistema está funcionando!\n";
echo "Agora você pode:\n";
echo "1. Atualizar o PHP para 8.2+\n";
echo "2. Executar: php artisan migrate\n";
echo "3. Executar: php artisan serve\n";
echo "4. Acessar: http://localhost:8000/pessoa/create\n";
