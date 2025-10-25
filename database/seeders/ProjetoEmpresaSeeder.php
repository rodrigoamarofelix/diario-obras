<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Projeto;
use App\Models\Empresa;

class ProjetoEmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar projetos e empresas existentes
        $projetos = Projeto::all();
        $empresas = Empresa::all();

        if ($projetos->count() > 0 && $empresas->count() > 0) {
            // Associar algumas empresas aos projetos
            foreach ($projetos as $projeto) {
                // Cada projeto pode ter 1-3 empresas associadas
                $numEmpresas = rand(1, min(3, $empresas->count()));
                $empresasSelecionadas = $empresas->random($numEmpresas);

                foreach ($empresasSelecionadas as $empresa) {
                    $tiposParticipacao = [
                        'construtora',
                        'fornecedor',
                        'subcontratada',
                        'consultoria',
                        'fiscalizacao',
                        'outros'
                    ];

                    $projeto->empresas()->attach($empresa->id, [
                        'tipo_participacao' => $tiposParticipacao[array_rand($tiposParticipacao)],
                        'observacoes' => 'Associação criada automaticamente pelo seeder',
                        'ativo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            echo "Associações projeto-empresa criadas com sucesso!\n";
        } else {
            echo "Nenhum projeto ou empresa encontrado para associar.\n";
        }
    }
}
