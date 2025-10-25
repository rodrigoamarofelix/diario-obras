@extends('layouts.admin')

@section('title', 'Detalhes do Catálogo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Detalhes do Catálogo
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('catalogo.edit', $catalogo) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('catalogo.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td>{{ $catalogo->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nome:</th>
                                    <td>{{ $catalogo->nome }}</td>
                                </tr>
                                <tr>
                                    <th>Código:</th>
                                    <td><span class="badge badge-info">{{ $catalogo->codigo }}</span></td>
                                </tr>
                                <tr>
                                    <th>Valor Unitário:</th>
                                    <td>{{ $catalogo->valor_unitario_formatado }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Unidade:</th>
                                    <td>{{ $catalogo->unidade_medida }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $catalogo->status == 'ativo' ? 'success' : 'secondary' }}">
                                            {{ $catalogo->status_name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Criado em:</th>
                                    <td>{{ $catalogo->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Atualizado em:</th>
                                    <td>{{ $catalogo->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($catalogo->descricao)
                        <div class="row">
                            <div class="col-12">
                                <h5>Descrição:</h5>
                                <p>{{ $catalogo->descricao }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Medições relacionadas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Medições Relacionadas
                    </h3>
                </div>
                <div class="card-body">
                    @if($catalogo->medicoes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Data</th>
                                        <th>Contrato</th>
                                        <th>Lotação</th>
                                        <th>Quantidade</th>
                                        <th>Valor Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($catalogo->medicoes as $medicao)
                                        <tr>
                                            <td>{{ $medicao->numero_medicao }}</td>
                                            <td>{{ $medicao->data_medicao->format('d/m/Y') }}</td>
                                            <td>{{ $medicao->contrato->numero }}</td>
                                            <td>{{ $medicao->lotacao->nome }}</td>
                                            <td>{{ number_format($medicao->quantidade, 3, ',', '.') }} {{ $catalogo->unidade_medida }}</td>
                                            <td>{{ $medicao->valor_total_formatado }}</td>
                                            <td>
                                                <span class="badge badge-{{ $medicao->status == 'aprovado' ? 'success' : ($medicao->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                                    {{ $medicao->status_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('medicao.show', $medicao) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhuma medição encontrada para este catálogo.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


