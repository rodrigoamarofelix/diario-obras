@extends('layouts.admin')

@section('title', 'Detalhes da Medição')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Detalhes da Medição
                    </h3>
                    <div class="card-tools">
                        @if($medicao->status === 'pendente')
                            <a href="{{ route('medicao.edit', $medicao) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="Esta medição não pode ser editada pois não está mais pendente">
                                <i class="fas fa-lock"></i> Não Editável
                            </button>
                        @endif
                        <a href="{{ route('medicao.index') }}" class="btn btn-secondary btn-sm">
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
                                    <td>{{ $medicao->id }}</td>
                                </tr>
                                <tr>
                                    <th>Número:</th>
                                    <td>{{ $medicao->numero_medicao }}</td>
                                </tr>
                                <tr>
                                    <th>Data:</th>
                                    <td>{{ is_object($medicao->data_medicao) ? $medicao->data_medicao->format('d/m/Y') : ($medicao->data_medicao ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Catálogo:</th>
                                    <td>{{ $medicao->catalogo->nome }} ({{ $medicao->catalogo->codigo }})</td>
                                </tr>
                                <tr>
                                    <th>Contrato:</th>
                                    <td>{{ $medicao->contrato->numero }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Lotação:</th>
                                    <td>{{ $medicao->lotacao->nome }}</td>
                                </tr>
                                <tr>
                                    <th>Quantidade:</th>
                                    <td>{{ number_format($medicao->quantidade, 3, ',', '.') }} {{ $medicao->catalogo->unidade_medida }}</td>
                                </tr>
                                <tr>
                                    <th>Valor Unitário:</th>
                                    <td>{{ $medicao->valor_unitario_formatado }}</td>
                                </tr>
                                <tr>
                                    <th>Valor Total:</th>
                                    <td><strong>{{ $medicao->valor_total_formatado }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $medicao->status == 'aprovado' ? 'success' : ($medicao->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                            {{ $medicao->status_name }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($medicao->observacoes)
                        <div class="row">
                            <div class="col-12">
                                <h5>Observações:</h5>
                                <p>{{ $medicao->observacoes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Criado por:</th>
                                    <td>{{ $medicao->usuario->name }}</td>
                                </tr>
                                <tr>
                                    <th>Criado em:</th>
                                    <td>{{ is_object($medicao->created_at) ? $medicao->created_at->format('d/m/Y H:i') : ($medicao->created_at ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Atualizado em:</th>
                                    <td>{{ is_object($medicao->updated_at) ? $medicao->updated_at->format('d/m/Y H:i') : ($medicao->updated_at ?? 'N/A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagamentos relacionados -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i> Pagamentos Relacionados
                    </h3>
                </div>
                <div class="card-body">
                    @if($medicao->pagamentos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Documento Redmine</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($medicao->pagamentos as $pagamento)
                                        <tr>
                                            <td>{{ $pagamento->numero_pagamento }}</td>
                                            <td>{{ is_object($pagamento->data_pagamento) ? $pagamento->data_pagamento->format('d/m/Y') : ($pagamento->data_pagamento ?? 'N/A') }}</td>
                                            <td>{{ $pagamento->valor_pagamento_formatado }}</td>
                                            <td>
                                                <span class="badge badge-{{ $pagamento->status == 'pago' ? 'success' : ($pagamento->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                                    {{ $pagamento->status_name }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($pagamento->tem_documento_redmine)
                                                    <a href="{{ $pagamento->link_documento_redmine }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-external-link-alt"></i> Ver
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('pagamento.show', $pagamento) }}" class="btn btn-info btn-sm">
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
                            <i class="fas fa-info-circle"></i> Nenhum pagamento encontrado para esta medição.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


