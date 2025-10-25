<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Projeto;
use App\Models\User;

class ProjetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar um usuário para ser responsável
        $responsavel = User::first();

        if (!$responsavel) {
            echo "Nenhum usuário encontrado. Criando usuário padrão...\n";
            $responsavel = User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $projetos = [
            [
                'nome' => 'Residencial Jardim das Flores',
                'descricao' => 'Construção de condomínio residencial com 50 unidades',
                'endereco' => 'Rua das Flores, 123',
                'complemento' => 'Loteamento Jardim',
                'bairro' => 'Jardim das Flores',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01234-567',
                'cliente' => 'Construtora ABC Ltda',
                'contrato' => 'CT-2024-001',
                'valor_total' => 2500000.00,
                'data_inicio' => '2024-01-15',
                'data_fim_prevista' => '2024-12-15',
                'status' => 'em_andamento',
                'prioridade' => 'alta',
                'observacoes' => 'Projeto prioritário da empresa',
                'responsavel_id' => $responsavel->id,
                'created_by' => $responsavel->id,
            ],
            [
                'nome' => 'Shopping Center Norte',
                'descricao' => 'Construção de shopping center com 200 lojas',
                'endereco' => 'Av. Paulista, 1000',
                'complemento' => 'Torre Norte',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01310-100',
                'cliente' => 'Shopping Norte S/A',
                'contrato' => 'CT-2024-002',
                'valor_total' => 5000000.00,
                'data_inicio' => '2024-03-01',
                'data_fim_prevista' => '2025-06-30',
                'status' => 'planejamento',
                'prioridade' => 'media',
                'observacoes' => 'Projeto em fase de planejamento',
                'responsavel_id' => $responsavel->id,
                'created_by' => $responsavel->id,
            ],
            [
                'nome' => 'Hospital Municipal',
                'descricao' => 'Construção de hospital público com 100 leitos',
                'endereco' => 'Rua da Saúde, 456',
                'complemento' => 'Setor Hospitalar',
                'bairro' => 'Centro',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'cep' => '20000-000',
                'cliente' => 'Prefeitura Municipal',
                'contrato' => 'CT-2024-003',
                'valor_total' => 8000000.00,
                'data_inicio' => '2024-02-01',
                'data_fim_prevista' => '2025-12-31',
                'status' => 'em_andamento',
                'prioridade' => 'urgente',
                'observacoes' => 'Projeto de interesse público',
                'responsavel_id' => $responsavel->id,
                'created_by' => $responsavel->id,
            ],
            [
                'nome' => 'Torre Empresarial',
                'descricao' => 'Construção de torre comercial com 30 andares',
                'endereco' => 'Av. Faria Lima, 2000',
                'complemento' => 'Torre Sul',
                'bairro' => 'Itaim Bibi',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '04538-132',
                'cliente' => 'Empresa XYZ Ltda',
                'contrato' => 'CT-2024-004',
                'valor_total' => 12000000.00,
                'data_inicio' => '2024-04-01',
                'data_fim_prevista' => '2026-03-31',
                'status' => 'planejamento',
                'prioridade' => 'media',
                'observacoes' => 'Projeto de grande porte',
                'responsavel_id' => $responsavel->id,
                'created_by' => $responsavel->id,
            ],
            [
                'nome' => 'Escola Municipal',
                'descricao' => 'Construção de escola pública para 500 alunos',
                'endereco' => 'Rua da Educação, 789',
                'complemento' => 'Quadra 15',
                'bairro' => 'Vila Nova',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'cep' => '30000-000',
                'cliente' => 'Secretaria de Educação',
                'contrato' => 'CT-2024-005',
                'valor_total' => 1500000.00,
                'data_inicio' => '2024-01-01',
                'data_fim_prevista' => '2024-11-30',
                'status' => 'concluido',
                'prioridade' => 'alta',
                'observacoes' => 'Projeto concluído com sucesso',
                'responsavel_id' => $responsavel->id,
                'created_by' => $responsavel->id,
            ],
        ];

        foreach ($projetos as $projetoData) {
            Projeto::create($projetoData);
        }

        echo "Projetos criados com sucesso!\n";
    }
}
