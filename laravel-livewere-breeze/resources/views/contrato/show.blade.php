@extends('layouts.admin')

@section('title', 'Detalhes do Contrato')
@section('page-title', 'Detalhes do Contrato')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrato.index') }}">Contratos</a></li>
<li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Informações do Contrato</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número do Contrato:</label>
                            <p class="form-control-static">{{ $contrato->numero }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status:</label>
                            <p class="form-control-static">
                                @if($contrato->status === 'ativo')
                                    <span class="badge badge-success">Ativo</span>
                                @elseif($contrato->status === 'inativo')
                                    <span class="badge badge-secondary">Inativo</span>
                                @elseif($contrato->status === 'vencido')
                                    <span class="badge badge-danger">Vencido</span>
                                @elseif($contrato->status === 'suspenso')
                                    <span class="badge badge-warning">Suspenso</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descrição:</label>
                    <p class="form-control-static">{{ $contrato->descricao }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data de Início:</label>
                            <p class="form-control-static">{{ $contrato->data_inicio->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Data de Fim:</label>
                            <p class="form-control-static">{{ $contrato->data_fim->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gestor Atual:</label>
                            <p class="form-control-static">
                                @if($contrato->gestor_atual)
                                    <span class="badge badge-success">{{ $contrato->gestor_atual->nome }}</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fiscal Atual:</label>
                            <p class="form-control-static">
                                @if($contrato->fiscal_atual)
                                    <span class="badge badge-success">{{ $contrato->fiscal_atual->nome }}</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Criado em:</label>
                            <p class="form-control-static">{{ $contrato->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Última Atualização:</label>
                            <p class="form-control-static">{{ $contrato->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>

                @if($contrato->deleted_at)
                    <div class="form-group">
                        <label>Excluído em (Soft Delete):</label>
                        <p class="form-control-static text-danger">{{ $contrato->deleted_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                @endif

                @if($contrato->esta_vencido)
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Contrato Vencido!</h5>
                        Este contrato está vencido desde {{ $contrato->data_fim->format('d/m/Y') }}.
                    </div>
                @elseif($contrato->dias_restantes <= 30)
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Contrato Próximo do Vencimento!</h5>
                        Este contrato vence em {{ $contrato->dias_restantes }} dias ({{ $contrato->data_fim->format('d/m/Y') }}).
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('contrato.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                @if(!$contrato->trashed())
                    <a href="{{ route('contrato.edit', $contrato->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Anexos do Contrato -->
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-paperclip"></i>
                    Anexos do Contrato
                </h3>
                @if(!$contrato->trashed())
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                            <i class="fas fa-plus"></i> Adicionar Anexos
                        </button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                @if($contrato->anexos->count() > 0)
                    <div class="row">
                        @foreach($contrato->anexos as $anexo)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 anexo-card">
                                    <div class="card-body text-center d-flex flex-column">
                                        <div class="mb-3">
                                            <i class="{{ $anexo->icone }} {{ $anexo->cor_icone }} fa-4x"></i>
                                        </div>
                                        <h6 class="card-title text-truncate mb-2" title="{{ $anexo->nome_original }}" style="font-size: 0.9rem;">
                                            {{ $anexo->nome_original }}
                                        </h6>
                                        <div class="text-muted mb-3 flex-grow-1">
                                            <small>
                                                <i class="fas fa-weight-hanging"></i> {{ $anexo->tamanho_formatado }}<br>
                                                <i class="fas fa-calendar"></i> {{ $anexo->created_at->format('d/m/Y H:i') }}<br>
                                                @if($anexo->descricao)
                                                    <i class="fas fa-comment"></i> {{ Str::limit($anexo->descricao, 30) }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('contrato.anexo.download', $anexo->id) }}"
                                               class="btn btn-sm btn-success flex-fill" title="Download">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            @if(!$contrato->trashed())
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="excluirAnexo({{ $anexo->id }})" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Estatísticas dos anexos -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Total de anexos:</strong> {{ $contrato->anexos->count() }} arquivo(s) |
                                <strong>Tamanho total:</strong> {{ number_format($contrato->anexos->sum('tamanho') / 1024 / 1024, 2) }} MB
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-paperclip fa-4x mb-3 text-secondary"></i>
                        <h5 class="mb-2">Nenhum anexo encontrado</h5>
                        <p class="mb-0">Este contrato ainda não possui arquivos anexados.</p>
                        @if(!$contrato->trashed())
                            <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#uploadModal">
                                <i class="fas fa-plus"></i> Adicionar Primeiro Anexo
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para Upload de Anexos -->
@if(!$contrato->trashed())
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Anexos</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('contrato.anexos.upload', $contrato->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="anexos">Arquivos para Anexar:</label>

                        <!-- Área de Drag & Drop -->
                        <div id="drop-zone-modal" class="drop-zone" style="border: 2px dashed #ccc; padding: 30px; text-align: center; margin-bottom: 15px; border-radius: 8px; background-color: #f8f9fa; transition: all 0.3s ease;">
                            <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                            <h5 class="mb-2">Arraste e solte os arquivos aqui</h5>
                            <p class="text-muted mb-3">ou</p>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('anexos').click()">
                                <i class="fas fa-folder-open"></i> Selecionar Arquivos
                            </button>
                            <input type="file" class="form-control-file d-none" id="anexos" name="anexos[]" multiple required accept="*/*">
                        </div>

                        <!-- Lista de arquivos selecionados -->
                        <div id="file-list-modal" class="file-list"></div>

                        <small class="form-text text-muted">
                            Tamanho máximo: 10MB por arquivo
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição (opcional):</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="2"
                                  placeholder="Descrição dos arquivos anexados..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Enviar Anexos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Histórico de Responsáveis -->
@if($historicoResponsaveis->count() > 1)
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Histórico de Responsáveis</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Período</th>
                                <th>Gestor</th>
                                <th>Fiscal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historicoResponsaveis as $responsavel)
                                <tr class="{{ $responsavel->esta_ativo ? 'table-success' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $responsavel->periodo_formatado }}</td>
                                    <td>
                                        @if($responsavel->gestor)
                                            <span class="badge badge-info">{{ $responsavel->gestor->nome }}</span>
                                        @else
                                            <span class="badge badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($responsavel->fiscal)
                                            <span class="badge badge-info">{{ $responsavel->fiscal->nome }}</span>
                                        @else
                                            <span class="badge badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($responsavel->esta_ativo)
                                            <span class="badge badge-success">Atual</span>
                                        @else
                                            <span class="badge badge-secondary">Histórico</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
function excluirAnexo(anexoId) {
    if (confirm('Tem certeza que deseja excluir este anexo?')) {
        // Criar formulário para exclusão
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("contrato.anexo.excluir", ":id") }}'.replace(':id', anexoId);

        // Adicionar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Adicionar método DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        // Submeter formulário
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-hide messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 3000);

// Drag & Drop functionality for modal - versão simplificada e robusta
let modalSelectedFiles = [];

// Initialize modal drag & drop when modal is shown
$(document).ready(function() {
    $('#uploadModal').on('shown.bs.modal', function() {
        console.log('Modal opened, initializing drag & drop');

    const dropZoneModal = document.getElementById('drop-zone-modal');
    const fileInputModal = document.getElementById('anexos');
    const fileListModal = document.getElementById('file-list-modal');

    // Reset files when modal opens
    modalSelectedFiles = [];
    fileListModal.innerHTML = '';
    fileInputModal.value = '';

    // Função para prevenir comportamentos padrão
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // Função para destacar a área de drop
    function highlight(e) {
        dropZoneModal.classList.add('dragover');
    }

    // Função para remover destaque
    function unhighlight(e) {
        dropZoneModal.classList.remove('dragover');
    }

    // Função para processar arquivos
    function handleFiles(files) {
        console.log('=== HANDLE FILES ===');
        console.log('Files received:', files.length);

        const fileArray = Array.from(files);
        console.log('File array:', fileArray);

        // Filtrar arquivos válidos (máximo 10MB)
        const validFiles = fileArray.filter(file => {
            console.log(`Checking file: ${file.name}, size: ${file.size} bytes`);
            if (file.size > 10 * 1024 * 1024) {
                alert(`Arquivo "${file.name}" é muito grande. Tamanho máximo: 10MB`);
                return false;
            }
            return true;
        });

        console.log('Valid files:', validFiles.length);

        // Adicionar arquivos válidos à lista
        modalSelectedFiles = [...modalSelectedFiles, ...validFiles];
        console.log('Total selected files:', modalSelectedFiles.length);

        updateFileList();
        updateFileInput();
    }

    // Função para atualizar a lista visual de arquivos
    function updateFileList() {
        fileListModal.innerHTML = '';

        if (modalSelectedFiles.length === 0) {
            return;
        }

        modalSelectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center p-3 mb-2 border rounded bg-light';

            const fileInfo = document.createElement('div');
            fileInfo.className = 'd-flex align-items-center';
            fileInfo.innerHTML = `
                <i class="fas fa-file mr-3 text-primary"></i>
                <div>
                    <div class="font-weight-bold">${file.name}</div>
                    <small class="text-muted">${formatFileSize(file.size)}</small>
                </div>
            `;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = () => removeFile(index);

            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            fileListModal.appendChild(fileItem);
        });
    }

    // Função para atualizar o input file
    function updateFileInput() {
        console.log('=== UPDATE FILE INPUT ===');
        console.log('Selected files:', modalSelectedFiles.length);

        // Usar DataTransfer para definir os arquivos
        if (window.DataTransfer) {
            try {
                const dataTransfer = new DataTransfer();
                modalSelectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInputModal.files = dataTransfer.files;
                console.log('DataTransfer method successful');
            } catch (error) {
                console.error('DataTransfer error:', error);
            }
        }

        console.log('Arquivos no input modal:', fileInputModal.files.length);
    }

    // Função para remover arquivo da lista
    function removeFile(index) {
        modalSelectedFiles.splice(index, 1);
        updateFileList();
        updateFileInput();
    }

    // Função para formatar tamanho do arquivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Prevenir comportamento padrão em toda a página para drag & drop
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }, false);

    document.addEventListener('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }, false);

    document.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }, false);

    document.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }, false);

    // Desabilitar drag em imagens e outros elementos
    document.querySelectorAll('img, a').forEach(element => {
        element.draggable = false;
    });

    // Remover event listeners anteriores se existirem
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZoneModal.removeEventListener(eventName, preventDefaults);
        dropZoneModal.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZoneModal.removeEventListener(eventName, highlight);
        dropZoneModal.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZoneModal.removeEventListener(eventName, unhighlight);
        dropZoneModal.addEventListener(eventName, unhighlight, false);
    });

    // Event listener para drop
    dropZoneModal.removeEventListener('drop', handleDrop);
    dropZoneModal.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        console.log('Drop event triggered');
        const files = e.dataTransfer.files;
        console.log('Files dropped:', files.length);
        handleFiles(files);
    }

    // Event listener para clique na área de drop
    dropZoneModal.removeEventListener('click', handleClick);
    dropZoneModal.addEventListener('click', handleClick);

    function handleClick(e) {
        if (e.target === dropZoneModal || e.target.closest('.drop-zone')) {
            fileInputModal.click();
        }
    }

    // Event listener para mudança no input file
    fileInputModal.removeEventListener('change', handleInputChange);
    fileInputModal.addEventListener('change', handleInputChange);

    function handleInputChange(e) {
        console.log('Input change event triggered');
        const files = e.target.files;
        handleFiles(files);
    }
    });
});
</script>

<style>
/* Estilos para os cards de anexos */
.anexo-card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.anexo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    border-color: #007bff;
}

.anexo-card .card-body {
    padding: 1.5rem;
}

.anexo-card .fa-4x {
    margin-bottom: 1rem;
}

.anexo-card .btn-group .btn {
    border-radius: 0.375rem;
}

.anexo-card .btn-group .btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.anexo-card .btn-group .btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Estilos para a área de drag & drop */
.drop-zone {
    transition: all 0.3s ease;
}

.drop-zone.dragover {
    background-color: #e3f2fd !important;
    border-color: #2196f3 !important;
    transform: scale(1.02);
}

.drop-zone:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

/* Estilos para a lista de arquivos */
.file-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.file-item:hover {
    background-color: #e9ecef;
}

/* Responsividade melhorada */
@media (max-width: 768px) {
    .anexo-card .btn-group {
        flex-direction: column;
    }

    .anexo-card .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }

    .anexo-card .btn-group .btn:last-child {
        margin-bottom: 0;
    }
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.anexo-card {
    animation: fadeInUp 0.5s ease-out;
}
</style>
@endsection
