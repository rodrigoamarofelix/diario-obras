<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AtividadeObra;
use App\Models\Projeto;
use App\Models\User;

class AtividadeTesteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se a atividade já existe
        $atividadeExistente = AtividadeObra::where('titulo', 'Instalação Elétrica - Sala Principal')->first();
        
        if ($atividadeExistente) {
            echo "Atividade já existe! ID: " . $atividadeExistente->id . "\n";
            return;
        }

        // Pegar primeiro projeto disponível
        $projeto = Projeto::whereNull('deleted_at')->first();
        
        if (!$projeto) {
            echo "ERRO: Nenhum projeto encontrado no banco de dados!\n";
            return;
        }

        // Pegar primeiro usuário disponível
        $user = User::where('profile', '!=', 'pending')->first();
        
        if (!$user) {
            echo "ERRO: Nenhum usuário encontrado no banco de dados!\n";
            return;
        }

        // Criar a atividade de teste
        $atividade = AtividadeObra::create([
            'projeto_id' => $projeto->id,
            'data_atividade' => now()->format('Y-m-d'),
            'titulo' => 'Instalação Elétrica - Sala Principal',
            'descricao' => 'Instalação da fiação elétrica completa para a sala principal, incluindo iluminação, tomadas e aparelhos de ar condicionado. A instalação será executada conforme projeto elétrico aprovado.',
            'tipo' => 'construcao',
            'status' => 'em_andamento',
            'hora_inicio' => '08:00:00',
            'hora_fim' => '17:00:00',
            'tempo_gasto_minutos' => 540,
            'observacoes' => 'Trabalho iniciado pela manhã. Toda a equipe está trabalhando conforme o planejado.',
            'problemas_encontrados' => 'Nenhum problema encontrado até o momento.',
            'solucoes_aplicadas' => 'N/A',
            'responsavel_id' => $user->id,
            'created_by' => $user->id,
        ]);

        echo "✓ Atividade criada com sucesso!\n";
        echo "   ID: {$atividade->id}\n";
        echo "   Título: {$atividade->titulo}\n";
        echo "   Projeto: {$projeto->nome}\n";
        echo "   Status: {$atividade->status}\n";
        echo "   Responsável: {$user->name}\n";
    }
}

