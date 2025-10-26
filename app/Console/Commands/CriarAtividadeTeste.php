<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AtividadeObra;
use App\Models\Projeto;
use App\Models\User;

class CriarAtividadeTeste extends Command
{
    protected $signature = 'equipe:criar-atividade-teste';
    protected $description = 'Cria uma atividade de teste para o módulo de equipe';

    public function handle()
    {
        try {
            // Verificar se já existe
            $existente = AtividadeObra::where('titulo', 'Instalação Elétrica - Sala Principal')->first();
            if ($existente) {
                $this->info("✓ Atividade já existe! ID: {$existente->id}");
                return 0;
            }

            // Pegar primeiro projeto
            $projeto = Projeto::whereNull('deleted_at')->first();
            if (!$projeto) {
                $this->error('✗ Nenhum projeto encontrado!');
                return 1;
            }

            // Pegar primeiro usuário
            $user = User::where('profile', '!=', 'pending')->first();
            if (!$user) {
                $this->error('✗ Nenhum usuário encontrado!');
                return 1;
            }

            // Criar atividade
            $atividade = AtividadeObra::create([
                'projeto_id' => $projeto->id,
                'data_atividade' => now()->format('Y-m-d'),
                'titulo' => 'Instalação Elétrica - Sala Principal',
                'descricao' => 'Instalação da fiação elétrica completa para a sala principal, incluindo iluminação e tomadas.',
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

            $this->info("✓ Atividade criada com sucesso!");
            $this->info("  ID: {$atividade->id}");
            $this->info("  Título: {$atividade->titulo}");
            $this->info("  Projeto: {$projeto->nome}");
            $this->info("  Status: {$atividade->status}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("✗ ERRO: " . $e->getMessage());
            return 1;
        }
    }
}

