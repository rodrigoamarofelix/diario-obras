@extends('layouts.admin')

@section('title', 'Criar Contrato')
@section('page-title', 'Cadastrar Novo Contrato')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrato.index') }}">Contratos</a></li>
<li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Formulário de Cadastro de Contrato</h3>
            </div>
            <form method="POST" action="{{ route('contrato.store') }}" accept-charset="UTF-8" class="form-horizontal">
                @csrf
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero">Número do Contrato:</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="{{ old('numero') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Selecione o Status</option>
                            <option value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                            <option value="vencido" {{ old('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                            <option value="suspenso" {{ old('status') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                        </select>
                    </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição:</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required>{{ old('descricao') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_inicio">Data de Início:</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ old('data_inicio') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_fim">Data de Fim:</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ old('data_fim') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gestor_id">Gestor:</label>
                                <select class="form-control" id="gestor_id" name="gestor_id" required>
                                    <option value="">Selecione o Gestor</option>
                                    @foreach($pessoas as $pessoa)
                                        <option value="{{ $pessoa->id }}" {{ old('gestor_id') == $pessoa->id ? 'selected' : '' }}>
                                            {{ $pessoa->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fiscal_id">Fiscal:</label>
                                <select class="form-control" id="fiscal_id" name="fiscal_id" required>
                                    <option value="">Selecione o Fiscal</option>
                                    @foreach($pessoas as $pessoa)
                                        <option value="{{ $pessoa->id }}" {{ old('fiscal_id') == $pessoa->id ? 'selected' : '' }}>
                                            {{ $pessoa->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#uploadModal">
                        <i class="fas fa-paperclip"></i> Adicionar Anexos
                    </button>
                    <a href="{{ route('contrato.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para adicionar anexos -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Anexos</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
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
@endsection

@section('scripts')
<script>
    // Validação de datas
    document.getElementById('data_inicio').addEventListener('change', function() {
        const dataInicio = new Date(this.value);
        const dataFimInput = document.getElementById('data_fim');

        if (this.value && dataFimInput.value) {
            const dataFim = new Date(dataFimInput.value);
            if (dataFim <= dataInicio) {
                alert('A data de fim deve ser posterior à data de início.');
                dataFimInput.value = '';
            }
        }
    });

    document.getElementById('data_fim').addEventListener('change', function() {
        const dataFim = new Date(this.value);
        const dataInicioInput = document.getElementById('data_inicio');

        if (this.value && dataInicioInput.value) {
            const dataInicio = new Date(dataInicioInput.value);
            if (dataFim <= dataInicio) {
                alert('A data de fim deve ser posterior à data de início.');
                this.value = '';
            }
        }
    });

    // Modal de upload de anexos
    $(document).ready(function() {
        $('#uploadModal').on('shown.bs.modal', function() {
            console.log('Modal opened, initializing drag & drop');

            const dropZone = document.getElementById('drop-zone-modal');
            const fileInput = document.getElementById('anexos');
            const fileList = document.getElementById('file-list-modal');

            // Prevenir comportamento padrão de drag & drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                document.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            // Eventos de drag & drop
            dropZone.addEventListener('dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('dragover');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('dragover');
            });

            dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
                dropZone.classList.remove('dragover');

                const files = e.dataTransfer.files;
                updateFileInput(files);
            });

            // Clique na área de drop para abrir seletor de arquivos
            dropZone.addEventListener('click', function(e) {
                if (e.target === dropZone || e.target.closest('.drop-zone')) {
                    fileInput.click();
                }
            });

            // Quando arquivos são selecionados
            fileInput.addEventListener('change', function(e) {
                updateFileInput(e.target.files);
            });

            // Função para atualizar o input de arquivos
            function updateFileInput(files) {
                if (!files || files.length === 0) return;

                // Limpar input anterior
                fileInput.value = '';

                // Tentar usar DataTransfer para adicionar arquivos
                try {
                    const dt = new DataTransfer();
                    Array.from(files).forEach(file => dt.items.add(file));
                    fileInput.files = dt.files;
                } catch (error) {
                    console.log('DataTransfer não suportado, usando método alternativo');
                    // Fallback: apenas mostrar os arquivos selecionados
                }

                // Mostrar arquivos selecionados
                showSelectedFiles(files);
            }

            // Função para mostrar arquivos selecionados
            function showSelectedFiles(files) {
        fileList.innerHTML = '';

                Array.from(files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center p-3 mb-2 border rounded bg-light';

                    fileItem.innerHTML = `
                        <div class="d-flex align-items-center">
                <i class="fas fa-file mr-3 text-primary"></i>
                <div>
                    <div class="font-weight-bold">${file.name}</div>
                    <small class="text-muted">${formatFileSize(file.size)}</small>
                </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;

            fileList.appendChild(fileItem);
        });
    }

            // Função para remover arquivo
            window.removeFile = function(index) {
                try {
                    const dt = new DataTransfer();
                    const files = Array.from(fileInput.files);
                    files.splice(index, 1);
                    files.forEach(file => dt.items.add(file));
                    fileInput.files = dt.files;
                    showSelectedFiles(fileInput.files);
                } catch (error) {
                    console.log('Erro ao remover arquivo:', error);
                }
            };

    // Função para formatar tamanho do arquivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

            // Submissão do formulário de upload
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                // Na página de criação, primeiro salvar o contrato
                const contratoForm = document.querySelector('form[action="{{ route("contrato.store") }}"]');
                const contratoData = new FormData(contratoForm);

                // Adicionar os anexos ao formulário principal
                const anexosInput = document.getElementById('anexos');
                if (anexosInput.files.length > 0) {
                    Array.from(anexosInput.files).forEach((file, index) => {
                        contratoData.append('anexos[]', file);
                    });
                    contratoData.append('anexar_arquivos', '1');
                }

                // Enviar formulário principal com anexos
                $.ajax({
                    url: '{{ route("contrato.store") }}',
                    type: 'POST',
                    data: contratoData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#uploadModal').modal('hide');
                        window.location.href = '{{ route("contrato.index") }}';
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorMessage = 'Erro de validação:\n';
                            Object.values(xhr.responseJSON.errors).forEach(errors => {
                                errors.forEach(error => errorMessage += '- ' + error + '\n');
                            });
                            alert(errorMessage);
                        } else {
                            alert('Erro ao salvar contrato: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                        }
                    }
                });
            });
        });
    });

</script>

<style>
/* Estilos para drag & drop */
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
</style>

@endsection
