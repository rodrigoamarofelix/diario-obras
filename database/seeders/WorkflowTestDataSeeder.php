<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkflowAprovacao;
use App\Models\User;
use App\Models\Medicao;
use App\Models\Pagamento;
use Carbon\Carbon;

class WorkflowTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuários existentes
        $users = User::all();
        if ($users->count() < 3) {
            $this->command->warn('Precisa de pelo menos 3 usuários para criar workflows de teste.');
            return;
        }

        $solicitante = $users->where('profile', 'user')->first();
        $aprovador = $users->where('profile', 'admin')->first();
        $master = $users->where('profile', 'master')->first();

        if (!$solicitante || !$aprovador || !$master) {
            $this->command->warn('Precisa de usuários com perfis: user, admin e master.');
            return;
        }

        // Buscar medições existentes
        $medicoes = Medicao::limit(5)->get();
        $pagamentos = Pagamento::limit(3)->get();

        // Criar workflows para medições
        foreach ($medicoes as $index => $medicao) {
            $status = ['pendente', 'em_analise', 'aprovado', 'rejeitado'][$index % 4];
            $urgente = $medicao->valor_total > 50000;

            WorkflowAprovacao::create([
                'model_type' => Medicao::class,
                'model_id' => $medicao->id,
                'tipo' => 'medicao',
                'status' => $status,
                'solicitante_id' => $solicitante->id,
                'aprovador_id' => $urgente ? $master->id : $aprovador->id,
                'aprovado_por' => $status === 'aprovado' ? ($urgente ? $master->id : $aprovador->id) : null,
                'aprovado_em' => $status === 'aprovado' ? now()->subDays(rand(1, 7)) : null,
                'comentarios' => $status === 'aprovado' ? 'Aprovado conforme análise técnica.' : null,
                'justificativa_rejeicao' => $status === 'rejeitado' ? 'Valores inconsistentes com o contrato.' : null,
                'nivel_aprovacao' => 1,
                'nivel_maximo' => $urgente ? 2 : 1,
                'valor' => $medicao->valor_total,
                'prazo_aprovacao' => now()->addDays(3),
                'urgente' => $urgente,
                'created_at' => now()->subDays(rand(1, 10)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
        }

        // Criar workflows para pagamentos
        foreach ($pagamentos as $index => $pagamento) {
            $status = ['pendente', 'em_analise', 'aprovado'][$index % 3];
            $urgente = $pagamento->valor_pagamento > 100000;

            WorkflowAprovacao::create([
                'model_type' => Pagamento::class,
                'model_id' => $pagamento->id,
                'tipo' => 'pagamento',
                'status' => $status,
                'solicitante_id' => $solicitante->id,
                'aprovador_id' => $urgente ? $master->id : $aprovador->id,
                'aprovado_por' => $status === 'aprovado' ? ($urgente ? $master->id : $aprovador->id) : null,
                'aprovado_em' => $status === 'aprovado' ? now()->subDays(rand(1, 5)) : null,
                'comentarios' => $status === 'aprovado' ? 'Pagamento aprovado conforme cronograma.' : null,
                'nivel_aprovacao' => 1,
                'nivel_maximo' => $urgente ? 2 : 1,
                'valor' => $pagamento->valor_pagamento,
                'prazo_aprovacao' => now()->addDays(2),
                'urgente' => $urgente,
                'created_at' => now()->subDays(rand(1, 7)),
                'updated_at' => now()->subDays(rand(0, 3)),
            ]);
        }

        // Criar alguns workflows vencidos para teste
        WorkflowAprovacao::create([
            'model_type' => Medicao::class,
            'model_id' => $medicoes->first()->id,
            'tipo' => 'medicao',
            'status' => 'pendente',
            'solicitante_id' => $solicitante->id,
            'aprovador_id' => $aprovador->id,
            'nivel_aprovacao' => 1,
            'nivel_maximo' => 1,
            'valor' => 25000,
            'prazo_aprovacao' => now()->subDays(2), // Vencido há 2 dias
            'urgente' => false,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $this->command->info('Dados de teste do workflow criados com sucesso!');
        $this->command->info('Total de workflows criados: ' . WorkflowAprovacao::count());
    }
}