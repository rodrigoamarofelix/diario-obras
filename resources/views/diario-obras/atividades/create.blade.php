@extends('layouts.admin')

@section('title', 'Nova Atividade - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-plus text-success"></i>
                        Nova Atividade
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.atividades.index') }}">Atividades</a></li>
                        <li class="breadcrumb-item active">Nova</li>
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
                                <i class="fas fa-tasks text-primary"></i>
                                Dados da Atividade
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('diario-obras.atividades.store') }}" method="POST">
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
                                            <label for="data_atividade">Data da Atividade *</label>
                                            <input type="date" class="form-control @error('data_atividade') is-invalid @enderror"
                                                   id="data_atividade" name="data_atividade"
                                                   value="{{ old('data_atividade', now()->format('Y-m-d')) }}" required>
                                            @error('data_atividade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="titulo">Título *</label>
                                            <input type="text" class="form-control @error('titulo') is-invalid @enderror"
                                                   id="titulo" name="titulo"
                                                   value="{{ old('titulo') }}"
                                                   placeholder="Ex: Construção da parede principal" required>
                                            @error('titulo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tipo">Tipo *</label>
                                            <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                                <option value="">Selecione o tipo</option>
                                                <option value="construcao" {{ old('tipo') == 'construcao' ? 'selected' : '' }}>Construção</option>
                                                <option value="demolicao" {{ old('tipo') == 'demolicao' ? 'selected' : '' }}>Demolição</option>
                                                <option value="reforma" {{ old('tipo') == 'reforma' ? 'selected' : '' }}>Reforma</option>
                                                <option value="manutencao" {{ old('tipo') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                                                <option value="limpeza" {{ old('tipo') == 'limpeza' ? 'selected' : '' }}>Limpeza</option>
                                                <option value="outros" {{ old('tipo') == 'outros' ? 'selected' : '' }}>Outros</option>
                                            </select>
                                            @error('tipo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição *</label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                              id="descricao" name="descricao" rows="4"
                                              placeholder="Descreva detalhadamente a atividade realizada..." required>{{ old('descricao') }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status *</label>
                                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="planejado" {{ old('status') == 'planejado' ? 'selected' : '' }}>Planejado</option>
                                                <option value="em_andamento" {{ old('status') == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                                <option value="concluido" {{ old('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                                <option value="cancelado" {{ old('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora_inicio">Hora de Início</label>
                                            <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                                   id="hora_inicio" name="hora_inicio"
                                                   value="{{ old('hora_inicio') }}">
                                            @error('hora_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora_fim">Hora de Fim</label>
                                            <input type="time" class="form-control @error('hora_fim') is-invalid @enderror"
                                                   id="hora_fim" name="hora_fim"
                                                   value="{{ old('hora_fim') }}">
                                            @error('hora_fim')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="responsavel_id">Responsável *</label>
                                    <select class="form-control @error('responsavel_id') is-invalid @enderror" id="responsavel_id" name="responsavel_id" required>
                                        <option value="">Selecione o responsável</option>
                                        @foreach($pessoas as $pessoa)
                                            <option value="{{ $pessoa->id }}" {{ old('responsavel_id') == $pessoa->id ? 'selected' : '' }}>
                                                {{ $pessoa->nome }}
                                                @if($pessoa->funcao)
                                                    - {{ $pessoa->funcao->nome }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsavel_id')
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
                                    <label for="problemas_encontrados">Problemas Encontrados</label>
                                    <textarea class="form-control @error('problemas_encontrados') is-invalid @enderror"
                                              id="problemas_encontrados" name="problemas_encontrados" rows="3"
                                              placeholder="Descreva os problemas encontrados durante a atividade...">{{ old('problemas_encontrados') }}</textarea>
                                    @error('problemas_encontrados')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="solucoes_aplicadas">Soluções Aplicadas</label>
                                    <textarea class="form-control @error('solucoes_aplicadas') is-invalid @enderror"
                                              id="solucoes_aplicadas" name="solucoes_aplicadas" rows="3"
                                              placeholder="Descreva as soluções aplicadas para resolver os problemas...">{{ old('solucoes_aplicadas') }}</textarea>
                                    @error('solucoes_aplicadas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i>
                                        Salvar Atividade
                                    </button>
                                    <a href="{{ route('diario-obras.atividades.index') }}" class="btn btn-secondary">
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

