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
                                            <label for="funcionario_id">Funcionário *</label>
                                            <select class="form-control @error('funcionario_id') is-invalid @enderror" id="funcionario_id" name="funcionario_id" required>
                                                <option value="">Selecione um funcionário</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}" {{ old('funcionario_id') == $usuario->id ? 'selected' : '' }}>
                                                        {{ $usuario->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('funcionario_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
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

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="funcao">Função *</label>
                                            <select class="form-control @error('funcao') is-invalid @enderror" id="funcao" name="funcao" required>
                                                <option value="">Selecione a função</option>
                                                <option value="pedreiro" {{ old('funcao') == 'pedreiro' ? 'selected' : '' }}>Pedreiro</option>
                                                <option value="eletricista" {{ old('funcao') == 'eletricista' ? 'selected' : '' }}>Eletricista</option>
                                                <option value="encanador" {{ old('funcao') == 'encanador' ? 'selected' : '' }}>Encanador</option>
                                                <option value="pintor" {{ old('funcao') == 'pintor' ? 'selected' : '' }}>Pintor</option>
                                                <option value="carpinteiro" {{ old('funcao') == 'carpinteiro' ? 'selected' : '' }}>Carpinteiro</option>
                                                <option value="ajudante" {{ old('funcao') == 'ajudante' ? 'selected' : '' }}>Ajudante</option>
                                                <option value="engenheiro" {{ old('funcao') == 'engenheiro' ? 'selected' : '' }}>Engenheiro</option>
                                                <option value="arquiteto" {{ old('funcao') == 'arquiteto' ? 'selected' : '' }}>Arquiteto</option>
                                                <option value="supervisor" {{ old('funcao') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                <option value="outros" {{ old('funcao') == 'outros' ? 'selected' : '' }}>Outros</option>
                                            </select>
                                            @error('funcao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora_entrada">Hora de Entrada</label>
                                            <input type="time" class="form-control @error('hora_entrada') is-invalid @enderror"
                                                   id="hora_entrada" name="hora_entrada"
                                                   value="{{ old('hora_entrada') }}">
                                            @error('hora_entrada')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora_saida">Hora de Saída</label>
                                            <input type="time" class="form-control @error('hora_saida') is-invalid @enderror"
                                                   id="hora_saida" name="hora_saida"
                                                   value="{{ old('hora_saida') }}">
                                            @error('hora_saida')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="horas_trabalhadas">Horas Trabalhadas</label>
                                            <input type="number" class="form-control @error('horas_trabalhadas') is-invalid @enderror"
                                                   id="horas_trabalhadas" name="horas_trabalhadas"
                                                   value="{{ old('horas_trabalhadas') }}" min="0" max="24" step="0.5">
                                            @error('horas_trabalhadas')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="presente" name="presente" value="1"
                                               {{ old('presente', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="presente">
                                            Funcionário presente
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="atividades_realizadas">Atividades Realizadas</label>
                                    <textarea class="form-control @error('atividades_realizadas') is-invalid @enderror"
                                              id="atividades_realizadas" name="atividades_realizadas" rows="3"
                                              placeholder="Descreva as atividades realizadas pelo funcionário...">{{ old('atividades_realizadas') }}</textarea>
                                    @error('atividades_realizadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="observacoes">Observações</label>
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                              id="observacoes" name="observacoes" rows="3"
                                              placeholder="Observações adicionais...">{{ old('observacoes') }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

