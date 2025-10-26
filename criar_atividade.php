<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AtividadeObra;
use App\Models\Projeto;
use App\Models\User;

try {
    // Verificar se já existe
    $existente = AtividadeObra::where('titulo', 'Instalação Elétrica - Sala Principal')->first();
    if ($existente) {
        echo "✓ Atividade já existe! ID: {$existente->id}\n";
        exit(0);
    }

    // Pegar primeiro projeto
    $projeto = Projeto::whereNull('deleted_at')->first();
    if (!$projeto) {
        echo "✗ ERRO: Nenhum projeto encontrado\n";
        exit(1);
    }

    // Pegar primeiro usuário
    $user = User::where('profile', '!=', 'pending')->first();
    if (!$user) {
        echo "✗ ERRO: Nenhum usuário encontrado\n";
        exit(1);
    }

    // Criar atividade
    $atividade = AtividadeObra::create([
        'projeto_id' => $projeto->id,
        'data_atividade' => date('Y-m-d'),
        'titulo' => 'Instalação Elétrica - Sala Principal',
        'descricao' => 'Instalação da fiação elétrica completa para a sala principal.',
        'tipo' => 'construcao',
        'status' => 'em_andamento',
        'hora_inicio' => '08:00:00',
        'hora_fim' => '17:00:00',
        'tempo_gasto_minutos' => 540,
        'observacoes' => 'Trabalho iniciado pela manhã.',
        'problemas_encontrados' => 'Nenhum problema.',
        'solucoes_aplicadas' => 'N/A',
        'responsavel_id' => $user->id,
        'created_by' => $user->id,
    ]);

    echo "✓ Atividade criada com sucesso!\n";
    echo "  ID: {$atividade->id}\n";
    echo "  Título: {$atividade->titulo}\n";
    echo "  Projeto: {$projeto->nome}\n";
    echo "  Status: {$atividade->status}\n";
    
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}

