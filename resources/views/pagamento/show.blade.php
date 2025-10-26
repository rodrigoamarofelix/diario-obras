@extends('layouts.admin')

@section('title', 'Detalhes do Pagamento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Detalhes do Pagamento
                    </h3>
                    <div class="card-tools">
                        @if($pagamento->status === 'pendente')
                            <a href="{{ route('pagamento.edit', $pagamento) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @else
                            <button class="btn btn-secondary btn-sm" disabled title="Este pagamento não pode ser editado pois não está mais pendente">
                                <i class="fas fa-lock"></i> Não Editável
                            </button>
                        @endif
                        <a href="{{ route('pagamento.index') }}" class="btn btn-secondary btn-sm">
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
                                    <td>{{ $pagamento->id }}</td>
                                </tr>
                                <tr>
                                    <th>Número:</th>
                                    <td>{{ $pagamento->numero_pagamento }}</td>
                                </tr>
                                <tr>
                                    <th>Data:</th>
                                    <td>{{ is_object($pagamento->data_pagamento) ? $pagamento->data_pagamento->format('d/m/Y') : ($pagamento->data_pagamento ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Valor:</th>
                                    <td><strong>{{ $pagamento->valor_pagamento_formatado }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $pagamento->status == 'pago' ? 'success' : ($pagamento->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                            {{ $pagamento->status_name }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Medição:</th>
                                    <td>{{ $pagamento->medicao->numero_medicao }}</td>
                                </tr>
                                <tr>
                                    <th>Catálogo:</th>
                                    <td>{{ $pagamento->medicao->catalogo->nome }} ({{ $pagamento->medicao->catalogo->codigo }})</td>
                                </tr>
                                <tr>
                                    <th>Contrato:</th>
                                    <td>{{ $pagamento->medicao->contrato->numero }}</td>
                                </tr>
                                <tr>
                                    <th>Lotação:</th>
                                    <td>{{ $pagamento->medicao->lotacao->nome }}</td>
                                </tr>
                                <tr>
                                    <th>Criado por:</th>
                                    <td>{{ $pagamento->usuario->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pagamento->documento_redmine)
                        <div class="row">
                            <div class="col-12">
                                <h5>Documento Redmine:</h5>
                                <p>
                                    <a href="{{ $pagamento->link_documento_redmine }}" target="_blank" class="btn btn-outline-info">
                                        <i class="fas fa-external-link-alt"></i> Abrir Documento
                                    </a>
                                    <small class="text-muted ml-2">{{ $pagamento->documento_redmine }}</small>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($pagamento->observacoes)
                        <div class="row">
                            <div class="col-12">
                                <h5>Observações:</h5>
                                <p>{{ $pagamento->observacoes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Criado em:</th>
                                    <td>{{ is_object($pagamento->created_at) ? $pagamento->created_at->format('d/m/Y H:i') : ($pagamento->created_at ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Atualizado em:</th>
                                    <td>{{ is_object($pagamento->updated_at) ? $pagamento->updated_at->format('d/m/Y H:i') : ($pagamento->updated_at ?? 'N/A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalhes da Medição Relacionada -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Detalhes da Medição
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Número:</th>
                                    <td>{{ $pagamento->medicao->numero_medicao }}</td>
                                </tr>
                                <tr>
                                    <th>Data:</th>
                                    <td>{{ is_object($pagamento->medicao->data_medicao) ? $pagamento->medicao->data_medicao->format('d/m/Y') : ($pagamento->medicao->data_medicao ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Quantidade:</th>
                                    <td>{{ number_format($pagamento->medicao->quantidade, 3, ',', '.') }} {{ $pagamento->medicao->catalogo->unidade_medida }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Valor Unitário:</th>
                                    <td>{{ $pagamento->medicao->valor_unitario_formatado }}</td>
                                </tr>
                                <tr>
                                    <th>Valor Total:</th>
                                    <td><strong>{{ $pagamento->medicao->valor_total_formatado }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $pagamento->medicao->status == 'aprovado' ? 'success' : ($pagamento->medicao->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                            {{ $pagamento->medicao->status_name }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pagamento->medicao->observacoes)
                        <div class="row">
                            <div class="col-12">
                                <h6>Observações da Medição:</h6>
                                <p>{{ $pagamento->medicao->observacoes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


