<?php

namespace App\Console\Commands;

use App\Models\Pessoa;
use App\Services\ReceitaFederalService;
use Illuminate\Console\Command;

class ProcessarCadastrosPendentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pessoas:processar-pendentes
                            {--limit=10 : Número máximo de cadastros para processar}
                            {--max-tentativas=3 : Número máximo de tentativas por CPF}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa cadastros pendentes de validação de CPF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $maxTentativas = $this->option('max-tentativas');

        $this->info("Processando cadastros pendentes (máximo: {$limit})...");

        // Buscar cadastros pendentes que não excederam o limite de tentativas
        $pessoasPendentes = Pessoa::pendenteValidacao()
            ->where('tentativas_validacao', '<', $maxTentativas)
            ->limit($limit)
            ->get();

        if ($pessoasPendentes->isEmpty()) {
            $this->info('Nenhum cadastro pendente encontrado.');
            return;
        }

        $receitaService = new ReceitaFederalService();
        $processadas = 0;
        $validadas = 0;
        $rejeitadas = 0;
        $erros = 0;

        foreach ($pessoasPendentes as $pessoa) {
            $this->info("Processando: {$pessoa->nome} ({$pessoa->cpf_formatado})");

            try {
                // Incrementar tentativas
                $pessoa->incrementarTentativas();

                // Consultar CPF na Receita Federal
                $resultado = $receitaService->consultarCpf($pessoa->cpf);

                if ($resultado['success']) {
                    // CPF válido e regular
                    $pessoa->marcarComoValidada();
                    $validadas++;
                    $this->info("  ✅ Validado: {$resultado['nome']}");
                } else {
                    // CPF irregular ou erro na consulta
                    $motivo = $resultado['message'];
                    $pessoa->marcarComoRejeitada($motivo);
                    $rejeitadas++;
                    $this->warn("  ❌ Rejeitado: {$motivo}");
                }

                $processadas++;

            } catch (\Exception $e) {
                $erros++;
                $this->error("  ⚠️ Erro ao processar {$pessoa->nome}: " . $e->getMessage());
            }
        }

        // Relatório final
        $this->newLine();
        $this->info("=== RELATÓRIO ===");
        $this->info("Processadas: {$processadas}");
        $this->info("Validadas: {$validadas}");
        $this->info("Rejeitadas: {$rejeitadas}");
        $this->info("Erros: {$erros}");

        // Mostrar próximos cadastros pendentes
        $proximosPendentes = Pessoa::pendenteValidacao()
            ->where('tentativas_validacao', '<', $maxTentativas)
            ->count();

        if ($proximosPendentes > 0) {
            $this->info("Ainda restam {$proximosPendentes} cadastros pendentes.");
        } else {
            $this->info("Todos os cadastros pendentes foram processados!");
        }
    }
}
