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
            <form method="POST" action="{{ route('contrato.store') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
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

                    <div class="form-group">
                        <label for="anexos">Anexos (opcional):</label>
                        <input type="file" class="form-control-file" id="anexos" name="anexos[]" multiple accept="*/*">
                        <small class="form-text text-muted">Tamanho máximo: 10MB por arquivo</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="{{ route('contrato.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    }); // fim DOMContentLoaded
</script>
@endsection

