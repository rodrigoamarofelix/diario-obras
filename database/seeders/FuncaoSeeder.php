<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuncaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcoes = [
            [
                'nome' => 'Pedreiro',
                'descricao' => 'Profissional responsável pela construção e reparos em alvenaria, concreto e estruturas básicas',
                'categoria' => 'construcao',
                'ativo' => true,
            ],
            [
                'nome' => 'Eletricista',
                'descricao' => 'Profissional especializado em instalações elétricas, manutenção e reparos elétricos',
                'categoria' => 'tecnica',
                'ativo' => true,
            ],
            [
                'nome' => 'Encanador',
                'descricao' => 'Profissional responsável por instalações hidráulicas, reparos de encanamentos e sistemas de água',
                'categoria' => 'tecnica',
                'ativo' => true,
            ],
            [
                'nome' => 'Pintor',
                'descricao' => 'Profissional especializado em pintura de paredes, estruturas e acabamentos',
                'categoria' => 'construcao',
                'ativo' => true,
            ],
            [
                'nome' => 'Carpinteiro',
                'descricao' => 'Profissional responsável por trabalhos em madeira, estruturas de madeira e acabamentos',
                'categoria' => 'construcao',
                'ativo' => true,
            ],
            [
                'nome' => 'Ajudante',
                'descricao' => 'Profissional auxiliar que presta suporte aos demais profissionais da obra',
                'categoria' => 'construcao',
                'ativo' => true,
            ],
            [
                'nome' => 'Engenheiro',
                'descricao' => 'Profissional responsável pelo planejamento, coordenação técnica e supervisão de obras',
                'categoria' => 'supervisao',
                'ativo' => true,
            ],
            [
                'nome' => 'Arquiteto',
                'descricao' => 'Profissional responsável pelo projeto arquitetônico, design e coordenação de projetos',
                'categoria' => 'supervisao',
                'ativo' => true,
            ],
            [
                'nome' => 'Supervisor',
                'descricao' => 'Profissional responsável pela supervisão e coordenação das atividades da obra',
                'categoria' => 'supervisao',
                'ativo' => true,
            ],
            [
                'nome' => 'Outros',
                'descricao' => 'Outras funções não especificadas nas categorias anteriores',
                'categoria' => 'outros',
                'ativo' => true,
            ],
        ];

        foreach ($funcoes as $funcao) {
            DB::table('funcoes')->updateOrInsert(
                ['nome' => $funcao['nome']],
                array_merge($funcao, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        echo "Funções cadastradas com sucesso!\n";
        echo "Total de funções: " . count($funcoes) . "\n";
    }
}
