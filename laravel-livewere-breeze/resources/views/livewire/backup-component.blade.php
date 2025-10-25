<div>
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_backups'] ?? '-' }}</h3>
                    <p>Total de Backups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_size'] ?? '-' }}</h3>
                    <p>Tamanho Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['last_backup'] ?? '-' }}</h3>
                    <p>Último Backup</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['oldest_backup'] ?? '-' }}</h3>
                    <p>Backup Mais Antigo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    @if(auth()->user()->profile === 'master')
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-rocket"></i> Ações Rápidas
                    <small class="text-muted">(Apenas Master)</small>
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-primary btn-block" onclick="createBackup('full')">
                            <i class="fas fa-database"></i> Backup Completo
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-info btn-block" onclick="createBackup('database')">
                            <i class="fas fa-database"></i> Backup Banco
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-success btn-block" onclick="createBackup('files')">
                            <i class="fas fa-folder"></i> Backup Arquivos
                        </button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-warning btn-block" onclick="cleanupBackups()">
                            <i class="fas fa-trash"></i> Limpar Antigos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Acesso Limitado</h5>
            <p class="mb-0">
                <strong>Usuário {{ ucfirst(auth()->user()->profile) }}:</strong>
                Você pode visualizar e baixar backups, mas apenas usuários <strong>Master</strong> podem criar, restaurar ou excluir backups.
            </p>
        </div>
    @endif

    <!-- Lista de Backups -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-list"></i> Lista de Backups
            </h3>
            <div class="card-tools">
                <button class="btn btn-tool" wire:click="refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($loading)
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Carregando backups...
                </div>
            @elseif(count($backups) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nome do Arquivo</th>
                                <th>Tipo</th>
                                <th>Tamanho</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>{{ $backup['name'] }}</td>
                                    <td>
                                        <span class="badge {{ $backup['type'] === 'database' ? 'badge-info' : 'badge-success' }}">
                                            {{ $backup['type'] === 'database' ? 'Banco de Dados' : 'Arquivos' }}
                                        </span>
                                    </td>
                                    <td>{{ $backup['size'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($backup['created_at'])->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="downloadBackup('{{ $backup['name'] }}')" title="Baixar">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        @if(auth()->user()->profile === 'master')
                                            <button class="btn btn-sm btn-warning" onclick="showRestoreModal('{{ $backup['name'] }}')" title="Restaurar">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteBackup('{{ $backup['name'] }}')" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted">
                    <i class="fas fa-inbox"></i> Nenhum backup encontrado
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Ação</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmButton">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Criar backup
function createBackup(type) {
    console.log('Iniciando backup do tipo:', type);
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando...';
    button.disabled = true;

    fetch('/backup/create', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => {
        console.log('Resposta recebida:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Dados recebidos:', data);
        if (data.success) {
            showAlert('success', 'Backup criado com sucesso!');
            @this.call('refresh');
        } else {
            showAlert('error', 'Erro ao criar backup: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('error', 'Erro ao criar backup');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Baixar backup
function downloadBackup(filename) {
    window.open(`/backup/download/${filename}`, '_blank');
}

// Excluir backup
function deleteBackup(filename) {
    showConfirmModal(
        'Tem certeza que deseja excluir este backup?',
        () => {
            fetch(`/backup/delete/${filename}`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Backup excluído com sucesso!');
                    @this.call('refresh');
                } else {
                    showAlert('error', 'Erro ao excluir backup: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('error', 'Erro ao excluir backup');
            });
        }
    );
}

// Limpar backups antigos
function cleanupBackups() {
    showConfirmModal(
        'Tem certeza que deseja limpar backups antigos?',
        () => {
            fetch('/backup/cleanup', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ days: 30 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Limpeza concluída com sucesso!');
                    @this.call('refresh');
                } else {
                    showAlert('error', 'Erro na limpeza: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('error', 'Erro na limpeza');
            });
        }
    );
}

// Mostrar modal de confirmação
function showConfirmModal(message, callback) {
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmButton').onclick = () => {
        $('#confirmModal').modal('hide');
        callback();
    };
    $('#confirmModal').modal('show');
}

// Mostrar alerta
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;

    document.body.insertBefore(alert, document.body.firstChild);

    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Função para mostrar modal de restauração
function showRestoreModal(backupName) {
    @this.call('showRestoreModal', backupName);
}
</script>

<!-- Modal de Restauração Segura -->
@if($showRestoreModal)
<div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h4 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    ⚠️ RESTAURAÇÃO DE BACKUP - OPERAÇÃO CRÍTICA
                </h4>
                <button type="button" class="close text-white" wire:click="closeRestoreModal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @if($restoreInfo)
                    <!-- Informações do Backup -->
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-info-circle"></i> Informações do Backup</h5>
                        <ul class="mb-0">
                            <li><strong>Arquivo:</strong> {{ $restoreInfo['backup_info']['filename'] }}</li>
                            <li><strong>Tipo:</strong>
                                @if(isset($restoreInfo['backup_info']['type']) && $restoreInfo['backup_info']['type'] === 'files')
                                    <span class="badge badge-success">Arquivos do Sistema</span>
                                @else
                                    <span class="badge badge-info">Banco de Dados</span>
                                @endif
                            </li>
                            <li><strong>Tamanho:</strong> {{ $restoreInfo['backup_info']['size'] }}</li>
                            <li><strong>Data do Backup:</strong> {{ $restoreInfo['backup_info']['created_at_formatted'] }}</li>
                            <li><strong>Idade:</strong> {{ $restoreInfo['backup_info']['age_days'] }} dias</li>
                        </ul>
                    </div>

                    <!-- Dados Atuais -->
                    @if(isset($restoreInfo['backup_info']['type']) && $restoreInfo['backup_info']['type'] === 'files')
                        <div class="alert alert-info">
                            <h5><i class="fas fa-folder"></i> Arquivos do Sistema</h5>
                            <p class="mb-0">Esta restauração irá substituir os arquivos do sistema pelos arquivos do backup.</p>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><i class="fas fa-database"></i> Dados Atuais no Sistema</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>Usuários:</strong> {{ $restoreInfo['current_data']['usuarios'] }}</li>
                                        <li><strong>Contratos:</strong> {{ $restoreInfo['current_data']['contratos'] }}</li>
                                        <li><strong>Medições:</strong> {{ $restoreInfo['current_data']['medicoes'] }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>Pagamentos:</strong> {{ $restoreInfo['current_data']['pagamentos'] }}</li>
                                        <li><strong>Pessoas:</strong> {{ $restoreInfo['current_data']['pessoas'] }}</li>
                                        <li><strong>Lotações:</strong> {{ $restoreInfo['current_data']['lotacoes'] }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Aviso Crítico -->
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> {{ $restoreInfo['warning'] }}</h5>
                    </div>

                    <!-- Formulário de Confirmação -->
                    <form wire:submit.prevent="confirmRestore">
                        <div class="form-group">
                            <label for="restoreConfirmation">
                                <strong>Confirmação de Segurança:</strong>
                            </label>
                            <input type="text"
                                   class="form-control @error('restoreConfirmation') is-invalid @enderror"
                                   id="restoreConfirmation"
                                   wire:model="restoreConfirmation"
                                   placeholder="Digite exatamente: CONFIRMO_RESTAURACAO"
                                   required>
                            @error('restoreConfirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Digite exatamente: <code>CONFIRMO_RESTAURACAO</code>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="restorePassword">
                                <strong>Sua Senha Atual:</strong>
                            </label>
                            <input type="password"
                                   class="form-control @error('restorePassword') is-invalid @enderror"
                                   id="restorePassword"
                                   wire:model="restorePassword"
                                   placeholder="Digite sua senha atual"
                                   required>
                            @error('restorePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Necessário para confirmar sua identidade
                            </small>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeRestoreModal">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit"
                                    class="btn btn-danger"
                                    @if($restoreLoading) disabled @endif>
                                @if($restoreLoading)
                                    <i class="fas fa-spinner fa-spin"></i> Restaurando...
                                @else
                                    <i class="fas fa-exclamation-triangle"></i> CONFIRMAR RESTAURAÇÃO
                                @endif
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin"></i> Carregando informações do backup...
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

