@extends('layouts.admin')

@section('title', 'Editar Equipe - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Equipe
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.equipe.index') }}">Equipe</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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
                                Editar Registro: {{ $equipe->pessoa->nome ?? ($equipe->funcionario->name ?? 'Funcionário') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('diario-obras.equipe.update', $equipe->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="projeto_id">Projeto *</label>
                                            <select class="form-control @error('projeto_id') is-invalid @enderror" id="projeto_id" name="projeto_id" required>
                                                <option value="">Selecione um projeto</option>
                                                @foreach($projetos as $projeto)
                                                    <option value="{{ $projeto->id }}" {{ old('projeto_id', $equipe->projeto_id) == $projeto->id ? 'selected' : '' }}>
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
                                            <label for="pessoa_id">Funcionário *</label>
                                            <select class="form-control @error('pessoa_id') is-invalid @enderror" id="pessoa_id" name="pessoa_id" required>
                                                <option value="">Selecione um funcionário</option>
                                                @foreach($pessoas as $pessoa)
                                                    <option value="{{ $pessoa->id }}" {{ old('pessoa_id', $equipe->pessoa_id) == $pessoa->id ? 'selected' : '' }}>
                                                        {{ $pessoa->nome }} - {{ $pessoa->funcao->nome ?? 'Sem função' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('pessoa_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="data_trabalho">Data do Trabalho *</label>
                                            <input type="date" class="form-control @error('data_trabalho') is-invalid @enderror"
                                                   id="data_trabalho" name="data_trabalho"
                                                   value="{{ old('data_trabalho', is_object($equipe->data_trabalho) ? $equipe->data_trabalho->format('Y-m-d') : $equipe->data_trabalho) }}" required>
                                            @error('data_trabalho')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="atividade_id">Atividade</label>
                                            <select class="form-control @error('atividade_id') is-invalid @enderror" id="atividade_id" name="atividade_id">
                                                <option value="">Selecione uma atividade</option>
                                                @foreach($atividades as $atividade)
                                                    <option value="{{ $atividade->id }}" {{ old('atividade_id', $equipe->atividade_id) == $atividade->id ? 'selected' : '' }}>
                                                        {{ $atividade->titulo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('atividade_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="funcao">Função *</label>
                                            <select class="form-control @error('funcao') is-invalid @enderror" id="funcao" name="funcao" required>
                                                <option value="">Selecione a função</option>
                                                @foreach($funcoes as $funcao)
                                                    <option value="{{ $funcao->nome }}" {{ old('funcao', $equipe->funcao) == $funcao->nome ? 'selected' : '' }}>
                                                        {{ $funcao->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('funcao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="hora_entrada">Hora de Entrada</label>
                                            <input type="time" class="form-control @error('hora_entrada') is-invalid @enderror"
                                                   id="hora_entrada" name="hora_entrada"
                                                   value="{{ old('hora_entrada', $equipe->hora_entrada ? (is_object($equipe->hora_entrada) ? $equipe->hora_entrada->format('H:i') : $equipe->hora_entrada) : '') }}">
                                            @error('hora_entrada')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="hora_saida_almoco">Hora Saída Almoço</label>
                                            <input type="time" class="form-control @error('hora_saida_almoco') is-invalid @enderror"
                                                   id="hora_saida_almoco" name="hora_saida_almoco"
                                                   value="{{ old('hora_saida_almoco', $equipe->hora_saida_almoco ? (is_object($equipe->hora_saida_almoco) ? $equipe->hora_saida_almoco->format('H:i') : $equipe->hora_saida_almoco) : '') }}">
                                            @error('hora_saida_almoco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="hora_retorno_almoco">Hora Retorno Almoço</label>
                                            <input type="time" class="form-control @error('hora_retorno_almoco') is-invalid @enderror"
                                                   id="hora_retorno_almoco" name="hora_retorno_almoco"
                                                   value="{{ old('hora_retorno_almoco', $equipe->hora_retorno_almoco ? (is_object($equipe->hora_retorno_almoco) ? $equipe->hora_retorno_almoco->format('H:i') : $equipe->hora_retorno_almoco) : '') }}">
                                            @error('hora_retorno_almoco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="hora_saida">Hora de Saída</label>
                                            <input type="time" class="form-control @error('hora_saida') is-invalid @enderror"
                                                   id="hora_saida" name="hora_saida"
                                                   value="{{ old('hora_saida', $equipe->hora_saida ? (is_object($equipe->hora_saida) ? $equipe->hora_saida->format('H:i') : $equipe->hora_saida) : '') }}">
                                            @error('hora_saida')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tipo_almoco">Tipo de Almoço</label>
                                            <select class="form-control @error('tipo_almoco') is-invalid @enderror" id="tipo_almoco" name="tipo_almoco">
                                                <option value="integral" {{ old('tipo_almoco', $equipe->tipo_almoco) == 'integral' ? 'selected' : '' }}>Integral</option>
                                                <option value="reduzido" {{ old('tipo_almoco', $equipe->tipo_almoco) == 'reduzido' ? 'selected' : '' }}>Reduzido</option>
                                            </select>
                                            @error('tipo_almoco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="horas_trabalhadas">Horas Trabalhadas</label>
                                            <input type="number" class="form-control @error('horas_trabalhadas') is-invalid @enderror"
                                                   id="horas_trabalhadas" name="horas_trabalhadas"
                                                   value="{{ old('horas_trabalhadas', $equipe->horas_trabalhadas) }}" min="0" max="24" step="0.5" readonly>
                                            <small class="form-text text-muted">Calculado automaticamente</small>
                                            @error('horas_trabalhadas')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="presente" name="presente" value="1"
                                                       {{ old('presente', $equipe->presente) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="presente">
                                                    Funcionário presente
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="atividades_realizadas">Atividades Realizadas</label>
                                    <textarea class="form-control @error('atividades_realizadas') is-invalid @enderror"
                                              id="atividades_realizadas" name="atividades_realizadas" rows="3">{{ old('atividades_realizadas', $equipe->atividades_realizadas) }}</textarea>
                                    @error('atividades_realizadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="observacoes">Observações</label>
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                              id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $equipe->observacoes) }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i>
                                        Atualizar Registro
                                    </button>
                                    <a href="{{ route('diario-obras.equipe.show', $equipe->id) }}" class="btn btn-secondary">
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
    const projetoSelect = document.getElementById('projeto_id');
    const atividadeSelect = document.getElementById('atividade_id');

    // Carregar atividades quando projeto for selecionado
    if (projetoSelect && atividadeSelect) {
        projetoSelect.addEventListener('change', function() {
            const projetoId = this.value;
            if (projetoId) {
                carregarAtividades(projetoId);
            } else {
                atividadeSelect.innerHTML = '<option value="">Selecione uma atividade</option>';
            }
        });
    }

    function carregarAtividades(projetoId) {
        fetch(`/diario-obras/api/atividades/projeto/${projetoId}`)
            .then(response => response.json())
            .then(data => {
                const selectedValue = atividadeSelect.value;
                atividadeSelect.innerHTML = '<option value="">Selecione uma atividade</option>';
                data.forEach(atividade => {
                    const option = document.createElement('option');
                    option.value = atividade.id;
                    option.textContent = atividade.titulo;
                    if (atividade.id == selectedValue) {
                        option.selected = true;
                    }
                    atividadeSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erro ao carregar atividades:', error);
            });
    }
});
</script>
@endsection

