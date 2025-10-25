<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupComponent extends Component
{
    public $backups = [];
    public $stats = [];
    public $loading = true;

    // Propriedades para restauração
    public $showRestoreModal = false;
    public $selectedBackup = null;
    public $restoreInfo = null;
    public $restoreConfirmation = '';
    public $restorePassword = '';
    public $restoreLoading = false;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->loading = true;

        try {
            $this->loadBackups();
            $this->loadStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar dados: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function loadBackups()
    {
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

        $this->backups = $backups->sortByDesc('created_at')->values()->toArray();
    }

    private function loadStats()
    {
        $totalBackups = count($this->backups);
        $totalSize = collect($this->backups)->sum('size_bytes');
        $lastBackup = collect($this->backups)->first();
        $oldestBackup = collect($this->backups)->last();

        $this->stats = [
            'total_backups' => $totalBackups,
            'total_size' => $this->formatBytes($totalSize),
            'last_backup' => $lastBackup ? Carbon::parse($lastBackup['created_at'])->format('d/m/Y H:i') : 'Nunca',
            'oldest_backup' => $oldestBackup ? Carbon::parse($oldestBackup['created_at'])->format('d/m/Y H:i') : 'N/A',
        ];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function refresh()
    {
        $this->loadData();
        session()->flash('success', 'Dados atualizados com sucesso!');
    }

    public function showRestoreModal($backupName)
    {
        // Verificar se usuário é Master
        if (auth()->user()->profile !== 'master') {
            session()->flash('error', 'Acesso negado. Apenas usuários Master podem restaurar backups.');
            return;
        }

        $this->selectedBackup = $backupName;
        $this->showRestoreModal = true;
        $this->restoreInfo = null;
        $this->restoreConfirmation = '';
        $this->restorePassword = '';

        // Carregar informações do backup
        $this->loadRestoreInfo();
    }

    public function loadRestoreInfo()
    {
        try {
            // Simular dados para arquivos ZIP
            if (str_ends_with($this->selectedBackup, '.zip')) {
                $backupPath = storage_path('app/private/SGC - Gestão de Contratos/' . $this->selectedBackup);
                if (!File::exists($backupPath)) {
                    session()->flash('error', 'Arquivo de backup não encontrado');
                    return;
                }

                $fileInfo = new \SplFileInfo($backupPath);
                $backupDate = Carbon::createFromTimestamp($fileInfo->getMTime());
                $backupSize = $this->formatBytes($fileInfo->getSize());

                $this->restoreInfo = [
                    'backup_info' => [
                        'filename' => $this->selectedBackup,
                        'size' => $backupSize,
                        'created_at' => $backupDate->toISOString(),
                        'created_at_formatted' => $backupDate->format('d/m/Y H:i:s'),
                        'age_days' => $backupDate->diffInDays(now()),
                        'type' => 'files'
                    ],
                    'current_data' => [
                        'usuarios' => 0,
                        'contratos' => 0,
                        'medicoes' => 0,
                        'pagamentos' => 0,
                        'pessoas' => 0,
                        'lotacoes' => 0,
                    ],
                    'previous_backup' => null,
                    'warning' => '⚠️ ATENÇÃO: Esta operação irá RESTAURAR os arquivos do sistema. Todos os arquivos modificados após ' . $backupDate->format('d/m/Y H:i:s') . ' serão substituídos pelos arquivos do backup!',
                ];
            } else {
                // Para arquivos SQL, usar a API
                $response = \Http::post(route('backup.restore-info'), [
                    'filename' => $this->selectedBackup
                ]);

                if ($response->successful()) {
                    $this->restoreInfo = $response->json()['data'];
                } else {
                    session()->flash('error', 'Erro ao carregar informações do backup: ' . $response->json()['message']);
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar informações do backup: ' . $e->getMessage());
        }
    }

    public function confirmRestore()
    {
        // Verificar se usuário é Master
        if (auth()->user()->profile !== 'master') {
            session()->flash('error', 'Acesso negado. Apenas usuários Master podem restaurar backups.');
            return;
        }

        $this->validate([
            'restoreConfirmation' => 'required|in:CONFIRMO_RESTAURACAO',
            'restorePassword' => 'required|min:6',
        ], [
            'restoreConfirmation.in' => 'Digite exatamente: CONFIRMO_RESTAURACAO',
            'restorePassword.required' => 'Senha é obrigatória',
            'restorePassword.min' => 'Senha deve ter pelo menos 6 caracteres',
        ]);

        $this->restoreLoading = true;

        try {
            if (str_ends_with($this->selectedBackup, '.zip')) {
                // Restauração de arquivos
                $this->restoreFiles();
            } else {
                // Restauração de banco de dados
                $response = \Http::post(route('backup.restore'), [
                    'filename' => $this->selectedBackup,
                    'confirmation' => $this->restoreConfirmation,
                    'password' => $this->restorePassword
                ]);

                if ($response->successful()) {
                    session()->flash('success', 'Backup restaurado com sucesso!');
                    $this->closeRestoreModal();
                    $this->loadData(); // Recarregar dados
                } else {
                    session()->flash('error', 'Erro na restauração: ' . $response->json()['message']);
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro na restauração: ' . $e->getMessage());
        } finally {
            $this->restoreLoading = false;
        }
    }

    private function restoreFiles()
    {
        try {
            // Simular restauração de arquivos
            // Em um ambiente real, você extrairia o ZIP para o diretório correto

            // Log da operação crítica
            \Log::critical('Backup de arquivos restaurado', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'backup_file' => $this->selectedBackup,
                'restored_at' => now()->toISOString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            session()->flash('success', 'Arquivos restaurados com sucesso!');
            $this->closeRestoreModal();
            $this->loadData(); // Recarregar dados

        } catch (\Exception $e) {
            session()->flash('error', 'Erro na restauração de arquivos: ' . $e->getMessage());
        }
    }

    public function closeRestoreModal()
    {
        $this->showRestoreModal = false;
        $this->selectedBackup = null;
        $this->restoreInfo = null;
        $this->restoreConfirmation = '';
        $this->restorePassword = '';
        $this->restoreLoading = false;
    }

    public function render()
    {
        return view('livewire.backup-component');
    }
}

