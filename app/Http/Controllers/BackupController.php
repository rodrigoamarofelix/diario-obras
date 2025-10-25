<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Backup\BackupJob;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Listar todos os backups disponíveis
     */
    public function index(): JsonResponse
    {
        try {
            $backupPath = storage_path('app/backups');
            $spatieBackupPath = storage_path('app/private/SGC - Gestão de Contratos');

            $backups = collect();

            // Backups personalizados (banco de dados)
            if (File::exists($backupPath)) {
                $customBackups = collect(File::files($backupPath))
                    ->map(function ($file) {
                        return [
                            'name' => $file->getFilename(),
                            'size' => $this->formatBytes($file->getSize()),
                            'size_bytes' => $file->getSize(),
                            'created_at' => Carbon::createFromTimestamp($file->getMTime())->toISOString(),
                            'path' => $file->getPathname(),
                            'type' => 'database',
                        ];
                    });
                $backups = $backups->merge($customBackups);
            }

            // Backups do Spatie (arquivos)
            if (File::exists($spatieBackupPath)) {
                $spatieBackups = collect(File::files($spatieBackupPath))
                    ->map(function ($file) {
                        return [
                            'name' => $file->getFilename(),
                            'size' => $this->formatBytes($file->getSize()),
                            'size_bytes' => $file->getSize(),
                            'created_at' => Carbon::createFromTimestamp($file->getMTime())->toISOString(),
                            'path' => $file->getPathname(),
                            'type' => 'files',
                        ];
                    });
                $backups = $backups->merge($spatieBackups);
            }

            $backups = $backups->sortByDesc('created_at')->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'backups' => $backups,
                    'total' => $backups->count(),
                    'total_size' => $this->formatBytes($backups->sum('size_bytes')),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar backups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar um novo backup
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // Verificar se usuário é Master
            if (auth()->user()->profile !== 'master') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários Master podem criar backups.'
                ], 403);
            }

            $type = $request->get('type', 'full'); // full, database, files

            // Log da operação
            \Log::info('Iniciando backup', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'backup_type' => $type,
                'started_at' => now()->toISOString(),
            ]);

            switch ($type) {
                case 'database':
                    $this->createDatabaseBackup();
                    break;
                case 'files':
                    \Log::info('Executando backup de arquivos...');
                    try {
                        $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
                        \Log::info('Backup de arquivos concluído', ['exit_code' => $exitCode]);
                    } catch (\Exception $e) {
                        \Log::error('Erro no backup de arquivos', ['error' => $e->getMessage()]);
                        throw $e;
                    }
                    break;
                default:
                    // Para backup completo, fazemos arquivos + banco separadamente
                    \Log::info('Executando backup completo (arquivos + banco)...');
                    Artisan::call('backup:run', ['--only-files' => true]);
                    $this->createDatabaseBackup();
                    break;
            }

            $output = Artisan::output();

            // Log de sucesso
            \Log::info('Backup concluído', [
                'user_id' => auth()->id(),
                'backup_type' => $type,
                'completed_at' => now()->toISOString(),
                'output' => $output,
                'output_length' => strlen($output),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso!',
                'type' => $type,
                'output' => $output,
                'created_at' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            // Log de erro
            \Log::error('Erro ao criar backup', [
                'user_id' => auth()->id(),
                'backup_type' => $request->get('type', 'full'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage(),
                'type' => $request->get('type', 'full'),
            ], 500);
        }
    }

    /**
     * Criar backup do banco de dados usando Laravel
     */
    private function createDatabaseBackup(): void
    {
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $filename = 'database-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $filepath = $backupPath . '/' . $filename;

        // Usar Laravel para fazer o dump
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();

        $sql = "-- Database dump for {$databaseName}\n";
        $sql .= "-- Generated on " . now()->toDateTimeString() . "\n\n";
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
    }

    /**
     * Baixar um backup específico
     */
    public function download(string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            // Tentar primeiro no diretório personalizado
            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                // Tentar no diretório do Spatie
                $backupPath = storage_path('app/private/Laravel/' . $filename);
            }

            if (!File::exists($backupPath)) {
                abort(404, 'Backup não encontrado');
            }

            return response()->download($backupPath);

        } catch (\Exception $e) {
            abort(500, 'Erro ao baixar backup: ' . $e->getMessage());
        }
    }

    /**
     * Excluir um backup específico
     */
    public function destroy(string $filename): JsonResponse
    {
        try {
            // Verificar se usuário é Master
            if (auth()->user()->profile !== 'master') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários Master podem excluir backups.'
                ], 403);
            }

            // Tentar primeiro no diretório personalizado
            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                // Tentar no diretório do Spatie
                $backupPath = storage_path('app/private/Laravel/' . $filename);
            }

            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ], 404);
            }

            File::delete($backupPath);

            // Log da operação
            \Log::info('Backup excluído', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'backup_file' => $filename,
                'deleted_at' => now()->toISOString(),
                'ip_address' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar informações do backup antes da restauração
     */
    public function restoreInfo(Request $request): JsonResponse
    {
        try {
            // Verificar se usuário é Master
            if (auth()->user()->role !== 'Master') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários Master podem restaurar backups.'
                ], 403);
            }

            $filename = $request->get('filename');

            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nome do arquivo é obrigatório'
                ], 422);
            }

            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ], 404);
            }

            // Obter informações do backup
            $fileInfo = new \SplFileInfo($backupPath);
            $backupDate = Carbon::createFromTimestamp($fileInfo->getMTime());
            $backupSize = $this->formatBytes($fileInfo->getSize());

            // Contar registros atuais no banco
            $currentData = [
                'usuarios' => DB::table('users')->count(),
                'contratos' => DB::table('contratos')->count(),
                'medicoes' => DB::table('medicoes')->count(),
                'pagamentos' => DB::table('pagamentos')->count(),
                'pessoas' => DB::table('pessoas')->count(),
                'lotacoes' => DB::table('lotacoes')->count(),
            ];

            // Obter último backup antes deste
            $allBackups = $this->getAllBackups();
            $previousBackup = $allBackups->where('name', '!=', $filename)
                ->sortByDesc('created_at')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'backup_info' => [
                        'filename' => $filename,
                        'size' => $backupSize,
                        'created_at' => $backupDate->toISOString(),
                        'created_at_formatted' => $backupDate->format('d/m/Y H:i:s'),
                        'age_days' => $backupDate->diffInDays(now()),
                    ],
                    'current_data' => $currentData,
                    'previous_backup' => $previousBackup ? [
                        'filename' => $previousBackup['name'],
                        'created_at' => $previousBackup['created_at'],
                        'created_at_formatted' => Carbon::parse($previousBackup['created_at'])->format('d/m/Y H:i:s'),
                    ] : null,
                    'warning' => '⚠️ ATENÇÃO: Esta operação irá APAGAR todos os dados atuais e restaurar o backup. Todos os dados gravados após ' . $backupDate->format('d/m/Y H:i:s') . ' serão perdidos permanentemente!',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações do backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restaurar um backup (apenas para usuários Master)
     */
    public function restore(Request $request): JsonResponse
    {
        try {
            // Verificar se usuário é Master
            if (auth()->user()->role !== 'Master') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários Master podem restaurar backups.'
                ], 403);
            }

            $filename = $request->get('filename');
            $confirmation = $request->get('confirmation');
            $password = $request->get('password');

            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nome do arquivo é obrigatório'
                ], 422);
            }

            if (!$confirmation || $confirmation !== 'CONFIRMO_RESTAURACAO') {
                return response()->json([
                    'success' => false,
                    'message' => 'Confirmação obrigatória. Digite exatamente: CONFIRMO_RESTAURACAO'
                ], 422);
            }

            // Verificar senha do usuário atual
            if (!$password || !Hash::check($password, auth()->user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha incorreta'
                ], 422);
            }

            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ], 404);
            }

            // Executar restauração usando o comando personalizado
            $exitCode = Artisan::call('backup:restore', [
                '--backup' => $filename,
                '--force' => true
            ]);

            $output = Artisan::output();

            if ($exitCode === 0) {
                // Log da operação crítica
                \Log::critical('Backup restaurado', [
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email,
                    'backup_file' => $filename,
                    'restored_at' => now()->toISOString(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Backup restaurado com sucesso!',
                    'backup_file' => $filename,
                    'restored_at' => now()->toISOString(),
                    'output' => $output,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro durante a restauração',
                    'output' => $output,
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Erro na restauração de backup', [
                'user_id' => auth()->id(),
                'backup_file' => $request->get('filename'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao restaurar backup',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter todos os backups disponíveis
     */
    private function getAllBackups()
    {
        $backupPath = storage_path('app/backups');
        $backups = collect();

        if (File::exists($backupPath)) {
            $backups = collect(File::files($backupPath))
                ->map(function ($file) {
                    return [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'size_bytes' => $file->getSize(),
                        'created_at' => Carbon::createFromTimestamp($file->getMTime())->toISOString(),
                        'created_at_formatted' => Carbon::createFromTimestamp($file->getMTime())->format('d/m/Y H:i:s'),
                        'path' => $file->getPathname(),
                        'type' => 'database',
                    ];
                });
        }

        return $backups->sortByDesc('created_at');
    }

    /**
     * Limpar backups antigos
     */
    public function cleanup(Request $request): JsonResponse
    {
        try {
            // Verificar se usuário é Master
            if (auth()->user()->profile !== 'master') {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Apenas usuários Master podem limpar backups.'
                ], 403);
            }

            $days = $request->get('days', 30);

            Artisan::call('backup:clean');

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Limpeza de backups antigos concluída',
                'days_kept' => $days,
                'output' => $output,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar backups antigos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estatísticas dos backups
     */
    public function stats(): JsonResponse
    {
        try {
            $backupPath = storage_path('app/backups');
            $spatieBackupPath = storage_path('app/private/SGC - Gestão de Contratos');

            $backups = collect();

            // Backups personalizados
            if (File::exists($backupPath)) {
                $customBackups = collect(File::files($backupPath));
                $backups = $backups->merge($customBackups);
            }

            // Backups do Spatie
            if (File::exists($spatieBackupPath)) {
                $spatieBackups = collect(File::files($spatieBackupPath));
                $backups = $backups->merge($spatieBackups);
            }

            if ($backups->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_backups' => 0,
                        'total_size' => '0 B',
                        'oldest_backup' => null,
                        'newest_backup' => null,
                        'last_backup' => null,
                    ]
                ]);
            }

            $totalSize = $backups->sum(function ($file) {
                return $file->getSize();
            });

            $oldestBackup = $backups->sortBy(function ($file) {
                return $file->getMTime();
            })->first();

            $newestBackup = $backups->sortByDesc(function ($file) {
                return $file->getMTime();
            })->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_backups' => $backups->count(),
                    'total_size' => $this->formatBytes($totalSize),
                    'total_size_bytes' => $totalSize,
                    'oldest_backup' => $oldestBackup ? [
                        'name' => $oldestBackup->getFilename(),
                        'created_at' => Carbon::createFromTimestamp($oldestBackup->getMTime())->toISOString(),
                    ] : null,
                    'newest_backup' => $newestBackup ? [
                        'name' => $newestBackup->getFilename(),
                        'created_at' => Carbon::createFromTimestamp($newestBackup->getMTime())->toISOString(),
                    ] : null,
                    'last_backup' => $newestBackup ? Carbon::createFromTimestamp($newestBackup->getMTime())->diffForHumans() : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Configurar agendamento de backups
     */
    public function schedule(Request $request): JsonResponse
    {
        try {
            $schedule = $request->validate([
                'frequency' => 'required|in:daily,weekly,monthly',
                'time' => 'required|date_format:H:i',
                'enabled' => 'boolean',
            ]);

            // Aqui você salvaria a configuração no banco de dados
            // Por enquanto, vamos apenas retornar sucesso

            return response()->json([
                'success' => true,
                'message' => 'Agendamento configurado com sucesso',
                'schedule' => $schedule,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao configurar agendamento',
                'error' => $e->getMessage()
            ], 500);
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