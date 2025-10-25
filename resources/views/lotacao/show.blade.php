@extends('layouts.admin')

@section('title', 'Visualizar Lotação')
@section('page-title', 'Visualizar Lotação')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('lotacao.index') }}">Lotações</a></li>
<li class="breadcrumb-item active">Visualizar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i> Visualizar Lotação: {{ $lotacao->nome }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nome">Nome</label>
                            <input type="text" class="form-control bg-light" id="nome" name="nome"
                                   value="{{ $lotacao->nome }}"
                                   readonly disabled>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control bg-light" id="descricao" name="descricao" rows="4"
                                      readonly disabled>{{ $lotacao->descricao }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control bg-light" name="status" id="status" disabled>
                                <option value="ativo" {{ $lotacao->status == 'ativo' ? 'selected' : ''}}>Ativo</option>
                                <option value="inativo" {{ $lotacao->status == 'inativo' ? 'selected' : ''}}>Inativo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('lotacao.edit', $lotacao) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('lotacao.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
