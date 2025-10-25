<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class BackupRestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore
                            {--backup= : Nome do arquivo de backup para restaurar}
                            {--list : Listar backups disponíveis}
                            {--force : Forçar restauração sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaurar banco de dados a partir de um backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listBackups();
            return;
        }

        $backupFile = $this->option('backup');

        if (!$backupFile) {
            $this->error('❌ Nome do backup é obrigatório!');
            $this->info('Use: php artisan backup:restore --backup=nome-do-arquivo.sql');
            $this->info('Ou use: php artisan backup:restore --list para ver backups disponíveis');
            return 1;
        }

        $backupPath = storage_path("app/backups/{$backupFile}");

        if (!File::exists($backupPath)) {
            $this->error("❌ Arquivo de backup não encontrado: {$backupFile}");
            $this->listBackups();
            return 1;
        }

        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  ATENÇÃO: Esta operação irá APAGAR todos os dados atuais e restaurar o backup. Continuar?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        $this->info("🔄 Iniciando restauração do backup: {$backupFile}");

        try {
            // 1. Limpar banco atual
            $this->info('🗑️  Limpando banco de dados atual...');
            Artisan::call('db:wipe', ['--force' => true]);

            // 2. Recriar estrutura
            $this->info('🏗️  Recriando estrutura do banco...');
            Artisan::call('migrate', ['--force' => true]);

            // 3. Desabilitar verificações de foreign key
            $this->info('🔓 Desabilitando verificações de chave estrangeira...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 4. Restaurar dados
            $this->info('📥 Restaurando dados do backup...');
            $this->restoreData($backupPath);

            // 5. Reabilitar verificações de foreign key
            $this->info('🔒 Reabilitando verificações de chave estrangeira...');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info('✅ Backup restaurado com sucesso!');
            $this->showRestoreSummary();

        } catch (\Exception $e) {
            // Reabilitar verificações em caso de erro
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ignored) {}

            $this->error("❌ Erro durante restauração: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Listar backups disponíveis
     */
    private function listBackups()
    {
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            $this->error('❌ Diretório de backups não encontrado!');
            return;
        }

        $backups = File::files($backupDir);

        if (empty($backups)) {
            $this->warn('⚠️  Nenhum backup encontrado!');
            return;
        }

        $this->info('📋 Backups disponíveis:');
        $this->line('');

        $headers = ['Nome', 'Tamanho', 'Modificado'];
        $rows = [];

        foreach ($backups as $backup) {
            $rows[] = [
                $backup->getFilename(),
                $this->formatBytes($backup->getSize()),
                $backup->getMTime() ? date('d/m/Y H:i:s', $backup->getMTime()) : 'N/A'
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
        $this->info('💡 Use: php artisan backup:restore --backup=nome-do-arquivo.sql');
    }

    /**
     * Restaurar dados do arquivo SQL
     */
    private function restoreData($backupPath)
    {
        $sql = File::get($backupPath);

        // Dividir em statements individuais
        $statements = $this->splitSqlStatements($sql);

        $successCount = 0;
        $errorCount = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);

            if (empty($statement) || $this->isIgnoredStatement($statement)) {
                continue;
            }

            try {
                // Para INSERTs, usar DB::unprepared para lidar com valores complexos
                if (stripos($statement, 'INSERT INTO') === 0) {
                    DB::unprepared($statement);
                } else {
                    DB::statement($statement);
                }
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->warn("⚠️  Erro no statement: " . substr($statement, 0, 50) . "...");
                $this->warn("Erro: " . $e->getMessage());
            }
        }

        $this->info("📊 Statements executados: {$successCount} sucessos, {$errorCount} erros");
    }

    /**
     * Dividir SQL em statements individuais
     */
    private function splitSqlStatements($sql)
    {
        // Remover comentários
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Dividir por ponto e vírgula, mas respeitando strings
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';

        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];

            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar) {
                $inString = false;
            } elseif (!$inString && $char === ';') {
                $statements[] = $current;
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (!empty(trim($current))) {
            $statements[] = $current;
        }

        return $statements;
    }

    /**
     * Verificar se statement deve ser ignorado
     */
    private function isIgnoredStatement($statement)
    {
        $ignoredPatterns = [
            '/^SET\s+/i',
            '/^START\s+TRANSACTION/i',
            '/^COMMIT/i',
            '/^ROLLBACK/i',
            '/^LOCK\s+TABLES/i',
            '/^UNLOCK\s+TABLES/i',
            '/^CREATE\s+DATABASE/i',
            '/^USE\s+/i',
            '/^DROP\s+DATABASE/i',
            '/^DROP\s+TABLE\s+IF\s+EXISTS/i', // Ignorar DROP TABLE IF EXISTS
            '/AUTO_INCREMENT/i', // Ignorar AUTO_INCREMENT (MySQL específico)
            '/COLLATE\s+utf8mb4_unicode_ci/i', // Ignorar COLLATE (MySQL específico)
            '/ENGINE=InnoDB/i', // Ignorar ENGINE (MySQL específico)
            '/DEFAULT\s+CHARSET=utf8mb4/i', // Ignorar CHARSET (MySQL específico)
        ];

        foreach ($ignoredPatterns as $pattern) {
            if (preg_match($pattern, $statement)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mostrar resumo da restauração
     */
    private function showRestoreSummary()
    {
        $this->line('');
        $this->info('📊 Resumo da Restauração:');

        try {
            $counts = [
                'Usuários' => DB::table('users')->count(),
                'Contratos' => DB::table('contratos')->count(),
                'Medições' => DB::table('medicoes')->count(),
                'Pagamentos' => DB::table('pagamentos')->count(),
                'Pessoas' => DB::table('pessoas')->count(),
                'Lotações' => DB::table('lotacoes')->count(),
            ];

            foreach ($counts as $table => $count) {
                $this->line("  • {$table}: {$count} registros");
            }

        } catch (\Exception $e) {
            $this->warn('⚠️  Não foi possível obter contagem de registros');
        }

        $this->line('');
        $this->info('🎉 Sistema restaurado e pronto para uso!');
    }

    /**
     * Formatar bytes em formato legível
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
