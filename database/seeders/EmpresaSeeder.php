<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\User;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o primeiro usuário para ser o criador
        $user = User::first();

        if (!$user) {
            $this->command->warn('Nenhum usuário encontrado. Execute primeiro o UserSeeder.');
            return;
        }

        $empresas = [
            [
                'nome' => 'Construtora ABC Ltda',
                'razao_social' => 'Construtora ABC Ltda',
                'cnpj' => '12345678000195',
                'email' => 'contato@construtoraabc.com.br',
                'telefone' => '1133334444',
                'whatsapp' => '11999998888',
                'cep' => '01310100',
                'endereco' => 'Av. Paulista',
                'numero' => '1000',
                'complemento' => 'Sala 101',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'pais' => 'Brasil',
                'site' => 'https://www.construtoraabc.com.br',
                'observacoes' => 'Empresa especializada em construção civil.',
                'ativo' => true,
                'created_by' => $user->id,
            ],
            [
                'nome' => 'Engenharia XYZ S.A.',
                'razao_social' => 'Engenharia XYZ S.A.',
                'cnpj' => '98765432000123',
                'email' => 'info@engenhariaxyz.com.br',
                'telefone' => '2133335555',
                'whatsapp' => '21988887777',
                'cep' => '20040020',
                'endereco' => 'Rua da Carioca',
                'numero' => '500',
                'complemento' => 'Andar 15',
                'bairro' => 'Centro',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'pais' => 'Brasil',
                'site' => 'https://www.engenhariaxyz.com.br',
                'observacoes' => 'Empresa focada em projetos de infraestrutura.',
                'ativo' => true,
                'created_by' => $user->id,
            ],
            [
                'nome' => 'Obras e Construções MG',
                'razao_social' => 'Obras e Construções MG Ltda',
                'cnpj' => '11223344000156',
                'email' => 'contato@obrasmg.com.br',
                'telefone' => '3133336666',
                'whatsapp' => '31977776666',
                'cep' => '30112000',
                'endereco' => 'Av. Afonso Pena',
                'numero' => '2000',
                'complemento' => 'Conjunto 301',
                'bairro' => 'Centro',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'pais' => 'Brasil',
                'site' => 'https://www.obrasmg.com.br',
                'observacoes' => 'Especializada em obras residenciais e comerciais.',
                'ativo' => true,
                'created_by' => $user->id,
            ]
        ];

        foreach ($empresas as $empresaData) {
            Empresa::create($empresaData);
        }

        $this->command->info('Empresas criadas com sucesso!');
    }
}



