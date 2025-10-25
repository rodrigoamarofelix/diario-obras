@extends('layouts.admin')

@section('title', 'Registrar Equipe - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-user-plus text-warning"></i>
                        Registrar Equipe
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.equipe.index') }}">Equipe</a></li>
                        <li class="breadcrumb-item active">Registrar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users text-primary"></i>
                                Dados da Equipe
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('diario-obras.equipe.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="projeto_id">Projeto *</label>
                                            <select class="form-control @error('projeto_id') is-invalid @enderror" id="projeto_id" name="projeto_id" required>
                                                <option value="">Selecione um projeto</option>
                                                @foreach($projetos as $projeto)
                                                    <option value="{{ $projeto->id }}" {{ old('projeto_id') == $projeto->id ? 'selected' : '' }}>
                                                        {{ $projeto->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('projeto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="data_trabalho">Data do Trabalho *</label>
                                            <input type="date" class="form-control @error('data_trabalho') is-invalid @enderror"
                                                   id="data_trabalho" name="data_trabalho"
                                                   value="{{ old('data_trabalho', now()->format('Y-m-d')) }}" required>
                                            @error('data_trabalho')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Seleção de Pessoas -->
                                <div class="form-group">
                                    <label for="pessoas_selecionadas">Pessoas da Equipe *</label>
                                    <select class="form-control @error('pessoas_selecionadas') is-invalid @enderror" 
                                            id="pessoas_selecionadas" name="pessoas_selecionadas[]" 
                                            multiple required>
                                        @foreach($pessoas as $pessoa)
                                            <option value="{{ $pessoa->id }}" 
                                                    {{ in_array($pessoa->id, old('pessoas_selecionadas', [])) ? 'selected' : '' }}>
                                                {{ $pessoa->nome }} - {{ $pessoa->funcao ? $pessoa->funcao->nome : 'Sem função' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Segure Ctrl (Windows) ou Cmd (Mac) para selecionar múltiplas pessoas
                                    </small>
                                    @error('pessoas_selecionadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campos para cada pessoa selecionada -->
                                <div id="campos-pessoas">
                                    <!-- Será preenchido dinamicamente via JavaScript -->
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i>
                                        Salvar Registro
                                    </button>
                                    <a href="{{ route('diario-obras.equipe.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Voltar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pessoasSelect = document.getElementById('pessoas_selecionadas');
    const camposPessoas = document.getElementById('campos-pessoas');
    
    // Dados das pessoas (vindos do PHP)
    const pessoas = @json($pessoas);
    
    function atualizarCamposPessoas() {
        const pessoasSelecionadas = Array.from(pessoasSelect.selectedOptions);
        camposPessoas.innerHTML = '';
        
        pessoasSelecionadas.forEach(function(option) {
            const pessoaId = option.value;
            const pessoa = pessoas.find(p => p.id == pessoaId);
            
            if (pessoa) {
                const card = document.createElement('div');
                card.className = 'card mb-3';
                card.innerHTML = `
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user text-primary"></i>
                            ${pessoa.nome} - ${pessoa.funcao ? pessoa.funcao.nome : 'Sem função'}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hora de Entrada</label>
                                    <input type="time" class="form-control" 
                                           name="pessoas[${pessoaId}][hora_entrada]"
                                           value="{{ old('pessoas.${pessoaId}.hora_entrada') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hora de Saída</label>
                                    <input type="time" class="form-control" 
                                           name="pessoas[${pessoaId}][hora_saida]"
                                           value="{{ old('pessoas.${pessoaId}.hora_saida') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Horas Trabalhadas</label>
                                    <input type="number" class="form-control" 
                                           name="pessoas[${pessoaId}][horas_trabalhadas]"
                                           value="{{ old('pessoas.${pessoaId}.horas_trabalhadas') }}"
                                           min="0" max="24" step="0.5">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tipo de Almoço</label>
                                    <select class="form-control" name="pessoas[${pessoaId}][tipo_almoco]">
                                        <option value="integral" {{ old('pessoas.${pessoaId}.tipo_almoco') == 'integral' ? 'selected' : '' }}>Integral</option>
                                        <option value="reduzido" {{ old('pessoas.${pessoaId}.tipo_almoco') == 'reduzido' ? 'selected' : '' }}>Reduzido</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hora Saída Almoço</label>
                                    <input type="time" class="form-control" 
                                           name="pessoas[${pessoaId}][hora_saida_almoco]"
                                           value="{{ old('pessoas.${pessoaId}.hora_saida_almoco') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hora Retorno Almoço</label>
                                    <input type="time" class="form-control" 
                                           name="pessoas[${pessoaId}][hora_retorno_almoco]"
                                           value="{{ old('pessoas.${pessoaId}.hora_retorno_almoco') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="pessoas[${pessoaId}][presente]" value="1" checked>
                                <label class="form-check-label">
                                    Funcionário presente
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Atividades Realizadas</label>
                            <textarea class="form-control" rows="2" 
                                      name="pessoas[${pessoaId}][atividades_realizadas]"
                                      placeholder="Descreva as atividades realizadas por ${pessoa.nome}...">{{ old('pessoas.${pessoaId}.atividades_realizadas') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea class="form-control" rows="2" 
                                      name="pessoas[${pessoaId}][observacoes]"
                                      placeholder="Observações sobre ${pessoa.nome}...">{{ old('pessoas.${pessoaId}.observacoes') }}</textarea>
                        </div>
                    </div>
                `;
                camposPessoas.appendChild(card);
            }
        });
    }
    
    // Atualizar campos quando a seleção mudar
    pessoasSelect.addEventListener('change', atualizarCamposPessoas);
    
    // Atualizar campos na carga inicial
    atualizarCamposPessoas();
});
</script>
@endsection

