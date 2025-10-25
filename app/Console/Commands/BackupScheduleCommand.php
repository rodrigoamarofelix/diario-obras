<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:schedule {--type=full : Tipo de backup (full, database, files)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa backup agendado do sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        $this->info("Iniciando backup agendado do tipo: {$type}");

        try {
            switch ($type) {
                case 'database':
                    $this->createDatabaseBackup();
                    break;
                case 'files':
                    Artisan::call('backup:run', ['--only-files' => true]);
                    break;
                case 'full':
                default:
                    // Backup completo: arquivos + banco
                    Artisan::call('backup:run', ['--only-files' => true]);
                    $this->createDatabaseBackup();
                    break;
            }

            $this->info('Backup agendado concluído com sucesso!');

            // Limpar backups antigos (manter apenas os últimos 30 dias)
            $this->cleanupOldBackups();

        } catch (\Exception $e) {
            $this->error('Erro ao executar backup agendado: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Criar backup do banco de dados
     */
    private function createDatabaseBackup(): void
    {
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $filename = 'database-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $filepath = $backupPath . '/' . $filename;

        $this->info("Criando backup do banco de dados: {$filename}");

        // Usar Laravel para fazer o dump
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();

        $sql = "-- Database dump for {$databaseName}\n";
        $sql .= "-- Generated on " . now()->toDateTimeString() . "\n";
        $sql .= "-- Backup agendado automático\n\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET AUTOCOMMIT = 0;\n";
        $sql .= "START TRANSACTION;\n";
        $sql .= "SET time_zone = \"+00:00\";\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];

            // Dump da estrutura da tabela
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sql .= "-- Table structure for table `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable->{'Create Table'} . ";\n\n";

            // Dump dos dados da tabela
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dumping data for table `{$tableName}`\n";
                $sql .= "INSERT INTO `{$tableName}` VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function($value) {
                        return $value === null ? 'NULL' : "'" . addslashes($value) . "'";
                    }, $rowArray);
                    $values[] = '(' . implode(',', $escapedValues) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        $sql .= "COMMIT;\n";

        File::put($filepath, $sql);

        $this->info("Backup do banco criado: " . $this->formatBytes(File::size($filepath)));
    }

    /**
     * Limpar backups antigos
     */
    private function cleanupOldBackups(): void
    {
        $this->info('Limpando backups antigos...');

        $backupPaths = [
            storage_path('app/backups'),
            storage_path('app/private/Laravel'),
        ];

        $deletedCount = 0;
        $cutoffDate = Carbon::now()->subDays(30);

        foreach ($backupPaths as $backupPath) {
            if (!File::exists($backupPath)) {
                continue;
            }

            $files = File::files($backupPath);

            foreach ($files as $file) {
                $fileDate = Carbon::createFromTimestamp($file->getMTime());

                if ($fileDate->lt($cutoffDate)) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                    $this->line("Removido: " . $file->getFilename());
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Removidos {$deletedCount} backups antigos.");
        } else {
            $this->info('Nenhum backup antigo encontrado para remoção.');
        }
    }

    /**
     * Formatar bytes em formato legível
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}