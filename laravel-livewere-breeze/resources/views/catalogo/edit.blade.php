@extends('layouts.admin')

@section('title', 'Editar Catálogo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Catálogo
                    </h3>
                </div>
                <form action="{{ route('catalogo.update', $catalogo) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome:</label>
                                    <input type="text" class="form-control" id="nome" name="nome"
                                           value="{{ old('nome', $catalogo->nome) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigo">Código:</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo"
                                           value="{{ old('codigo', $catalogo->codigo) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição:</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao', $catalogo->descricao) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valor_unitario">Valor Unitário:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="number" class="form-control" id="valor_unitario" name="valor_unitario"
                                               step="0.01" min="0" value="{{ old('valor_unitario', $catalogo->valor_unitario) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unidade_medida">Unidade de Medida:</label>
                                    <select class="form-control" id="unidade_medida" name="unidade_medida" required>
                                        <option value="">Selecione...</option>
                                        <option value="un" {{ old('unidade_medida', $catalogo->unidade_medida) == 'un' ? 'selected' : '' }}>Unidade</option>
                                        <option value="kg" {{ old('unidade_medida', $catalogo->unidade_medida) == 'kg' ? 'selected' : '' }}>Quilograma</option>
                                        <option value="m" {{ old('unidade_medida', $catalogo->unidade_medida) == 'm' ? 'selected' : '' }}>Metro</option>
                                        <option value="m²" {{ old('unidade_medida', $catalogo->unidade_medida) == 'm²' ? 'selected' : '' }}>Metro Quadrado</option>
                                        <option value="m³" {{ old('unidade_medida', $catalogo->unidade_medida) == 'm³' ? 'selected' : '' }}>Metro Cúbico</option>
                                        <option value="l" {{ old('unidade_medida', $catalogo->unidade_medida) == 'l' ? 'selected' : '' }}>Litro</option>
                                        <option value="h" {{ old('unidade_medida', $catalogo->unidade_medida) == 'h' ? 'selected' : '' }}>Hora</option>
                                        <option value="dia" {{ old('unidade_medida', $catalogo->unidade_medida) == 'dia' ? 'selected' : '' }}>Dia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="ativo" {{ old('status', $catalogo->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                        <option value="inativo" {{ old('status', $catalogo->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar
                        </button>
                        <a href="{{ route('catalogo.show', $catalogo) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


