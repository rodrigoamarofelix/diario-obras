@extends('layouts.admin')

@section('title', 'Registrar Material - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-plus text-info"></i>
                        Registrar Material
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.materiais.index') }}">Materiais</a></li>
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
                                <i class="fas fa-boxes text-primary"></i>
                                Dados do Material
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('diario-obras.materiais.store') }}" method="POST">
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
                                            <label for="tipo_movimento">Tipo de Movimento *</label>
                                            <select class="form-control @error('tipo_movimento') is-invalid @enderror" id="tipo_movimento" name="tipo_movimento" required>
                                                <option value="">Selecione o tipo</option>
                                                <option value="entrada" {{ old('tipo_movimento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                                <option value="saida" {{ old('tipo_movimento') == 'saida' ? 'selected' : '' }}>Saída</option>
                                                <option value="transferencia" {{ old('tipo_movimento') == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                                            </select>
                                            @error('tipo_movimento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="nome_material">Nome do Material *</label>
                                            <input type="text" class="form-control @error('nome_material') is-invalid @enderror"
                                                   id="nome_material" name="nome_material"
                                                   value="{{ old('nome_material') }}"
                                                   placeholder="Ex: Cimento, Tijolos, Ferro..." required>
                                            @error('nome_material')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="unidade_medida">Unidade de Medida *</label>
                                            <input type="text" class="form-control @error('unidade_medida') is-invalid @enderror"
                                                   id="unidade_medida" name="unidade_medida"
                                                   value="{{ old('unidade_medida') }}"
                                                   placeholder="Ex: kg, m², unidades..." required>
                                            @error('unidade_medida')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                              id="descricao" name="descricao" rows="3"
                                              placeholder="Descrição detalhada do material...">{{ old('descricao') }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="quantidade">Quantidade *</label>
                                            <input type="number" class="form-control @error('quantidade') is-invalid @enderror"
                                                   id="quantidade" name="quantidade"
                                                   value="{{ old('quantidade') }}"
                                                   min="0" step="0.001" required>
                                            @error('quantidade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="valor_unitario">Valor Unitário (R$)</label>
                                            <input type="number" class="form-control @error('valor_unitario') is-invalid @enderror"
                                                   id="valor_unitario" name="valor_unitario"
                                                   value="{{ old('valor_unitario') }}"
                                                   min="0" step="0.01">
                                            @error('valor_unitario')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="data_movimento">Data do Movimento *</label>
                                            <input type="date" class="form-control @error('data_movimento') is-invalid @enderror"
                                                   id="data_movimento" name="data_movimento"
                                                   value="{{ old('data_movimento', now()->format('Y-m-d')) }}" required>
                                            @error('data_movimento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fornecedor">Fornecedor</label>
                                            <input type="text" class="form-control @error('fornecedor') is-invalid @enderror"
                                                   id="fornecedor" name="fornecedor"
                                                   value="{{ old('fornecedor') }}"
                                                   placeholder="Nome do fornecedor">
                                            @error('fornecedor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nota_fiscal">Nota Fiscal</label>
                                            <input type="text" class="form-control @error('nota_fiscal') is-invalid @enderror"
                                                   id="nota_fiscal" name="nota_fiscal"
                                                   value="{{ old('nota_fiscal') }}"
                                                   placeholder="Número da nota fiscal">
                                            @error('nota_fiscal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="responsavel_id">Responsável *</label>
                                    <select class="form-control @error('responsavel_id') is-invalid @enderror" id="responsavel_id" name="responsavel_id" required>
                                        <option value="">Selecione o responsável</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ old('responsavel_id') == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
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
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i>
                                        Salvar Material
                                    </button>
                                    <a href="{{ route('diario-obras.materiais.index') }}" class="btn btn-secondary">
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

