<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pessoa;
use App\Models\Lotacao;
use App\Models\Contrato;
use App\Models\Catalogo;
use App\Models\Medicao;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuários de teste
        $users = [
            [
                'name' => 'Admin Master',
                'email' => 'master@test.com',
                'password' => Hash::make('password'),
                'profile' => 'master',
                'approval_status' => 'approved',
                'approved_at' => now(),
            ],
            [
                'name' => 'Admin Sistema',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'profile' => 'admin',
                'approval_status' => 'approved',
                'approved_at' => now(),
            ],
            [
                'name' => 'Usuário Teste',
                'email' => 'user@test.com',
                'password' => Hash::make('password'),
                'profile' => 'user',
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Criar pessoas de teste
        $pessoas = [
            [
                'nome' => 'João Silva Santos',
                'cpf' => '111.444.777-35',
                'lotacao_id' => 1, // Será atualizado depois
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'data_validacao' => now(),
            ],
            [
                'nome' => 'Maria Oliveira Costa',
                'cpf' => '123.456.789-01',
                'lotacao_id' => 1, // Será atualizado depois
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'data_validacao' => now(),
            ],
            [
                'nome' => 'Pedro Souza Lima',
                'cpf' => '987.654.321-00',
                'lotacao_id' => 1, // Será atualizado depois
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'data_validacao' => now(),
            ]
        ];

        // Criar lotações de teste primeiro
        $lotacoes = [
            [
                'nome' => 'Diretoria de Tecnologia',
                'descricao' => 'Diretoria responsável por tecnologia da informação',
                'status' => 'ativo',
            ],
            [
                'nome' => 'Diretoria Financeira',
                'descricao' => 'Diretoria responsável por questões financeiras',
                'status' => 'ativo',
            ],
            [
                'nome' => 'Diretoria Administrativa',
                'descricao' => 'Diretoria responsável por questões administrativas',
                'status' => 'ativo',
            ]
        ];

        $lotacoesCriadas = [];
        foreach ($lotacoes as $lotacaoData) {
            $lotacoesCriadas[] = Lotacao::firstOrCreate(['nome' => $lotacaoData['nome']], $lotacaoData);
        }

        // Atualizar pessoas com lotacao_id correto
        $pessoasCriadas = [];
        foreach ($pessoas as $index => $pessoaData) {
            $pessoaData['lotacao_id'] = $lotacoesCriadas[$index]->id;
            $pessoasCriadas[] = Pessoa::firstOrCreate(['cpf' => $pessoaData['cpf']], $pessoaData);
        }

        // Criar contratos de teste
        $contratos = [
            [
                'numero' => 'CTR-2024-001',
                'descricao' => 'Contrato de Desenvolvimento de Software',
                'data_inicio' => Carbon::now()->subMonths(6),
                'data_fim' => Carbon::now()->addMonths(6),
                'gestor_id' => $pessoasCriadas[0]->id,
                'fiscal_id' => $pessoasCriadas[1]->id,
                'status' => 'ativo',
            ],
            [
                'numero' => 'CTR-2024-002',
                'descricao' => 'Contrato de Consultoria em TI',
                'data_inicio' => Carbon::now()->subMonths(3),
                'data_fim' => Carbon::now()->addMonths(9),
                'gestor_id' => $pessoasCriadas[1]->id,
                'fiscal_id' => $pessoasCriadas[2]->id,
                'status' => 'ativo',
            ],
            [
                'numero' => 'CTR-2023-001',
                'descricao' => 'Contrato de Manutenção de Equipamentos',
                'data_inicio' => Carbon::now()->subYear(),
                'data_fim' => Carbon::now()->subDays(30),
                'gestor_id' => $pessoasCriadas[2]->id,
                'fiscal_id' => $pessoasCriadas[0]->id,
                'status' => 'vencido',
            ],
            [
                'numero' => 'CTR-2024-003',
                'descricao' => 'Contrato de Suporte Técnico',
                'data_inicio' => Carbon::now()->subMonth(),
                'data_fim' => Carbon::now()->addMonths(11),
                'gestor_id' => $pessoasCriadas[0]->id,
                'fiscal_id' => $pessoasCriadas[1]->id,
                'status' => 'suspenso',
            ]
        ];

        $contratosCriados = [];
        foreach ($contratos as $contratoData) {
            $contratosCriados[] = Contrato::firstOrCreate(['numero' => $contratoData['numero']], $contratoData);
        }

        // Criar catálogos de teste
        $catalogos = [
            [
                'nome' => 'Desenvolvimento de Software',
                'codigo' => 'CAT-001',
                'descricao' => 'Desenvolvimento de Software',
                'unidade_medida' => 'Hora',
                'valor_unitario' => 150.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Consultoria em TI',
                'codigo' => 'CAT-002',
                'descricao' => 'Consultoria em TI',
                'unidade_medida' => 'Hora',
                'valor_unitario' => 200.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Manutenção de Equipamentos',
                'codigo' => 'CAT-003',
                'descricao' => 'Manutenção de Equipamentos',
                'unidade_medida' => 'Unidade',
                'valor_unitario' => 500.00,
                'status' => 'ativo',
            ],
            [
                'nome' => 'Suporte Técnico',
                'codigo' => 'CAT-004',
                'descricao' => 'Suporte Técnico',
                'unidade_medida' => 'Hora',
                'valor_unitario' => 100.00,
                'status' => 'ativo',
            ]
        ];

        $catalogosCriados = [];
        foreach ($catalogos as $catalogoData) {
            $catalogosCriados[] = Catalogo::firstOrCreate(['codigo' => $catalogoData['codigo']], $catalogoData);
        }

        // Criar medições de teste (últimos 6 meses)
        $usuario = User::where('email', 'user@test.com')->first();

        for ($i = 0; $i < 6; $i++) {
            $mes = Carbon::now()->subMonths($i);

            // Criar 3-5 medições por mês
            $quantidadeMedicoes = rand(3, 5);

            for ($j = 0; $j < $quantidadeMedicoes; $j++) {
                $contrato = $contratosCriados[array_rand($contratosCriados)];
                $catalogo = $catalogosCriados[array_rand($catalogosCriados)];
                $lotacao = $lotacoesCriadas[array_rand($lotacoesCriadas)];

                $quantidade = rand(10, 100);
                $valorUnitario = $catalogo->valor_unitario;
                $valorTotal = $quantidade * $valorUnitario;

                $statuses = ['pendente', 'aprovado', 'rejeitado'];
                $status = $statuses[array_rand($statuses)];

                Medicao::create([
                    'catalogo_id' => $catalogo->id,
                    'contrato_id' => $contrato->id,
                    'lotacao_id' => $lotacao->id,
                    'numero_medicao' => 'MED' . str_pad(($i * 10) + $j + 1, 3, '0', STR_PAD_LEFT),
                    'data_medicao' => $mes->copy()->addDays(rand(1, 28)),
                    'quantidade' => $quantidade,
                    'valor_unitario' => $valorUnitario,
                    'valor_total' => $valorTotal,
                    'observacoes' => 'Medição de teste gerada automaticamente',
                    'status' => $status,
                    'usuario_id' => $usuario->id,
                ]);
            }
        }

        // Criar pagamentos de teste
        $medicoes = Medicao::where('status', 'aprovado')->get();

        foreach ($medicoes as $medicao) {
            if (rand(0, 1)) { // 50% chance de ter pagamento
                $statuses = ['pendente', 'pago'];
                $status = $statuses[array_rand($statuses)];

                Pagamento::create([
                    'medicao_id' => $medicao->id,
                    'numero_pagamento' => 'PAG' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'data_pagamento' => $medicao->data_medicao->addDays(rand(1, 30)),
                    'valor_pagamento' => $medicao->valor_total,
                    'observacoes' => 'Pagamento de teste gerado automaticamente',
                    'documento_redmine' => 'RED-' . rand(1000, 9999),
                    'status' => $status,
                    'usuario_id' => $usuario->id,
                ]);
            }
        }

        $this->command->info('Dados de teste para o dashboard criados com sucesso!');
        $this->command->info('Usuários criados:');
        $this->command->info('- master@test.com (senha: password)');
        $this->command->info('- admin@test.com (senha: password)');
        $this->command->info('- user@test.com (senha: password)');
    }
}
