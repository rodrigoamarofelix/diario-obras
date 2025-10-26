@extends('layouts.admin')

@section('title', 'Visualizar Pessoa')
@section('page-title', 'Detalhes da Pessoa')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pessoa.index') }}">Pessoas</a></li>
<li class="breadcrumb-item active">Visualizar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dados da Pessoa</h3>
                <div class="card-tools">
                    <a href="{{ route('pessoa.edit', $pessoa->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('pessoa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Nome Completo:</strong></label>
                            <p class="form-control-plaintext">{{ $pessoa->nome }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>CPF:</strong></label>
                            <p class="form-control-plaintext">{{ $pessoa->cpf_formatado }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Lotação:</strong></label>
                            <p class="form-control-plaintext">
                                @if($pessoa->lotacao)
                                    <span class="badge badge-info">{{ $pessoa->lotacao->nome }}</span>
                                @else
                                    <span class="text-muted">Não informado</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Status:</strong></label>
                            <p class="form-control-plaintext">
                                @if($pessoa->status === 'ativo')
                                    <span class="badge badge-success">Ativo</span>
                                @else
                                    <span class="badge badge-danger">Inativo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Data de Cadastro:</strong></label>
                            <p class="form-control-plaintext">
                                {{ is_object($pessoa->created_at) ? $pessoa->created_at->format('d/m/Y H:i') : ($pessoa->created_at ?? 'N/A') }}
                                @if($pessoa->trashed())
                                    <span class="badge badge-danger ml-2">Registro Excluído</span>
                                @else
                                    <span class="badge badge-success ml-2">Registro Ativo</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Última Atualização:</strong></label>
                            <p class="form-control-plaintext">{{ is_object($pessoa->updated_at) ? $pessoa->updated_at->format('d/m/Y H:i') : ($pessoa->updated_at ?? 'N/A') }}</p>
                        </div>
                    </div>
                </div>

                @if($pessoa->deleted_at)
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label><strong>Excluído em (Soft Delete):</strong></label>
                                <p class="form-control-plaintext text-danger">{{ is_object($pessoa->deleted_at) ? $pessoa->deleted_at->format('d/m/Y H:i') : ($pessoa->deleted_at ?? 'N/A') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($pessoa->foi_reativada && !$pessoa->trashed())
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Pessoa Reativada!</h5>
                        Esta pessoa foi reativada em {{ is_object($pessoa->created_at) ? $pessoa->created_at->format('d/m/Y H:i') : ($pessoa->created_at ?? 'N/A') }}.
                        A data de criação foi atualizada para controlar o histórico de entrada/saída.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection