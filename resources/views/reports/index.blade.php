@extends('layouts.admin')

@section('title', 'Relatórios')
@section('page-title', 'Sistema de Relatórios')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Relatórios</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter"></i> Filtros do Relatório
        </h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('reports.generate') }}">
            @csrf
            <div class="row">
                <!-- Tipo de Relatório -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="reportType">Tipo de Relatório:</label>
                        <select name="reportType" class="form-control" id="reportType">
                            <option value="contratos" {{ $reportType ?? '' == 'contratos' ? 'selected' : '' }}>Contratos</option>
                            <option value="medicoes" {{ $reportType ?? '' == 'medicoes' ? 'selected' : '' }}>Medições</option>
                            <option value="pagamentos" {{ $reportType ?? '' == 'pagamentos' ? 'selected' : '' }}>Pagamentos</option>
                            <option value="usuarios" {{ $reportType ?? '' == 'usuarios' ? 'selected' : '' }}>Usuários</option>
                        </select>
                    </div>
                </div>

                <!-- Data Inicial -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dateFrom">Data Inicial:</label>
                        <input type="date" name="dateFrom" value="{{ $dateFrom ?? '' }}" class="form-control" id="dateFrom">
                    </div>
                </div>

                <!-- Data Final -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="dateTo">Data Final:</label>
                        <input type="date" name="dateTo" value="{{ $dateTo ?? '' }}" class="form-control" id="dateTo">
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" class="form-control" id="status">
                            <option value="">Todos</option>
                            @if(($reportType ?? '') == 'contratos')
                                <option value="ativo" {{ ($status ?? '') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ ($status ?? '') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                <option value="vencido" {{ ($status ?? '') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                                <option value="suspenso" {{ ($status ?? '') == 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                            @elseif(($reportType ?? '') == 'pagamentos')
                                <option value="pendente" {{ ($status ?? '') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="pago" {{ ($status ?? '') == 'pago' ? 'selected' : '' }}>Pago</option>
                                <option value="cancelado" {{ ($status ?? '') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            @elseif(($reportType ?? '') == 'usuarios')
                                <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Gerar Relatório
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resultados do Relatório -->
@if(isset($results) && !empty($results))
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar"></i> Resultados do Relatório
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <h5><i class="fas fa-check-circle"></i> Relatório Gerado com Sucesso!</h5>
            <p>Total de registros encontrados: <strong>{{ count($results) }}</strong></p>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        @if($reportType == 'contratos')
                            <th>Número</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th>Gestor</th>
                            <th>Fiscal</th>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                        @elseif($reportType == 'medicoes')
                            <th>ID</th>
                            <th>Contrato</th>
                            <th>Catálogo</th>
                            <th>Quantidade</th>
                            <th>Valor Unitário</th>
                            <th>Valor Total</th>
                            <th>Data</th>
                        @elseif($reportType == 'pagamentos')
                            <th>ID</th>
                            <th>Medição</th>
                            <th>Contrato</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Data Vencimento</th>
                            <th>Data Pagamento</th>
                        @elseif($reportType == 'usuarios')
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Data Criação</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $item)
                    <tr>
                        @if($reportType == 'contratos')
                            <td>{{ $item->numero }}</td>
                            <td>{{ Str::limit($item->descricao, 50) }}</td>
                            <td><span class="badge badge-{{ $item->status == 'ativo' ? 'success' : ($item->status == 'inativo' ? 'secondary' : ($item->status == 'vencido' ? 'danger' : 'warning')) }}">{{ ucfirst($item->status) }}</span></td>
                            <td>{{ $item->gestor->nome ?? '-' }}</td>
                            <td>{{ $item->fiscal->nome ?? '-' }}</td>
                            <td>{{ $item->data_inicio ? $item->data_inicio->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->data_fim ? $item->data_fim->format('d/m/Y') : '-' }}</td>
                        @elseif($reportType == 'medicoes')
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->contrato->numero ?? '-' }}</td>
                            <td>{{ $item->catalogo->descricao ?? '-' }}</td>
                            <td>{{ $item->quantidade }}</td>
                            <td>R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        @elseif($reportType == 'pagamentos')
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->medicao->id ?? '-' }}</td>
                            <td>{{ $item->medicao->contrato->numero ?? '-' }}</td>
                            <td>R$ {{ number_format($item->valor, 2, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $item->status == 'pago' ? 'success' : ($item->status == 'pendente' ? 'warning' : 'danger') }}">{{ ucfirst($item->status) }}</span></td>
                            <td>{{ $item->data_vencimento ? $item->data_vencimento->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->data_pagamento ? $item->data_pagamento->format('d/m/Y') : '-' }}</td>
                        @elseif($reportType == 'usuarios')
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->profile_name }}</td>
                            <td><span class="badge badge-{{ $item->approval_status == 'approved' ? 'success' : ($item->approval_status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($item->approval_status) }}</span></td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

