<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primeiro, criar algumas lotações se não existirem
        $lotacoes = [
            ['nome' => 'Administração', 'descricao' => 'Setor administrativo da empresa'],
            ['nome' => 'Recursos Humanos', 'descricao' => 'Departamento de recursos humanos'],
            ['nome' => 'Financeiro', 'descricao' => 'Setor financeiro e contábil'],
            ['nome' => 'Comercial', 'descricao' => 'Departamento comercial'],
            ['nome' => 'Operacional', 'descricao' => 'Setor operacional'],
        ];

        foreach ($lotacoes as $lotacao) {
            DB::table('lotacoes')->updateOrInsert(
                ['nome' => $lotacao['nome']],
                array_merge($lotacao, [
                    'status' => 'ativo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Buscar lotações criadas
        $lotacoesIds = DB::table('lotacoes')->pluck('id')->toArray();

        // CPFs válidos para teste (gerados com algoritmo correto)
        $cpfsValidos = [
            '111.444.777-35',
            '222.555.888-46',
            '333.666.999-57',
            '444.777.000-68',
            '555.888.111-79',
            '666.999.222-80',
            '777.000.333-91',
            '888.111.444-02',
            '999.222.555-13',
            '000.333.666-24',
        ];

        $pessoas = [
            [
                'nome' => 'João Silva Santos',
                'cpf' => $cpfsValidos[0],
                'lotacao_id' => $lotacoesIds[0] ?? 1,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Maria Oliveira Costa',
                'cpf' => $cpfsValidos[1],
                'lotacao_id' => $lotacoesIds[1] ?? 2,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Pedro Henrique Almeida',
                'cpf' => $cpfsValidos[2],
                'lotacao_id' => $lotacoesIds[2] ?? 3,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Ana Carolina Ferreira',
                'cpf' => $cpfsValidos[3],
                'lotacao_id' => $lotacoesIds[3] ?? 4,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Carlos Eduardo Rodrigues',
                'cpf' => $cpfsValidos[4],
                'lotacao_id' => $lotacoesIds[4] ?? 5,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Fernanda Lima Souza',
                'cpf' => $cpfsValidos[5],
                'lotacao_id' => $lotacoesIds[0] ?? 1,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Rafael Mendes Pereira',
                'cpf' => $cpfsValidos[6],
                'lotacao_id' => $lotacoesIds[1] ?? 2,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Juliana Barbosa Martins',
                'cpf' => $cpfsValidos[7],
                'lotacao_id' => $lotacoesIds[2] ?? 3,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Lucas Gabriel Nascimento',
                'cpf' => $cpfsValidos[8],
                'lotacao_id' => $lotacoesIds[3] ?? 4,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
            [
                'nome' => 'Camila Beatriz Rocha',
                'cpf' => $cpfsValidos[9],
                'lotacao_id' => $lotacoesIds[4] ?? 5,
                'status' => 'ativo',
                'status_validacao' => 'validado',
                'observacoes_validacao' => 'CPF validado com sucesso',
                'data_validacao' => now(),
                'tentativas_validacao' => 1,
            ],
        ];

        foreach ($pessoas as $pessoa) {
            DB::table('pessoas')->updateOrInsert(
                ['cpf' => $pessoa['cpf']],
                array_merge($pessoa, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        echo "10 pessoas cadastradas com sucesso!\n";
        echo "Lotações criadas: " . count($lotacoes) . "\n";
        echo "Pessoas cadastradas: " . count($pessoas) . "\n";
    }
}
