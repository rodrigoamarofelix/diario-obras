@extends('exports.pdf.layout')

@section('content')
@php
    $titulo = 'Relatório Financeiro Completo';
@endphp

<!-- Resumo Executivo -->
<div class="summary-box">
    <h3><i class="fas fa-chart-line"></i> Resumo Executivo</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">{{ $resumo['total_medicoes'] }}</div>
            <div class="summary-label">Total de Medições</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($resumo['valor_total_medicoes'], 2, ',', '.') }}</div>
            <div class="summary-label">Valor Total Medições</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $resumo['total_pagamentos'] }}</div>
            <div class="summary-label">Total de Pagamentos</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($resumo['valor_total_pagamentos'], 2, ',', '.') }}</div>
            <div class="summary-label">Valor Total Pago</div>
        </div>
    </div>
</div>

<!-- Medições por Status -->
@if($resumo['medicoes_por_status']->count() > 0)
<div class="page-break"></div>
<h3><i class="fas fa-ruler"></i> Medições por Status</h3>
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th>Quantidade</th>
            <th>Valor Total</th>
            <th>Valor Médio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resumo['medicoes_por_status'] as $status)
        <tr>
            <td>
                <span class="status-badge status-{{ $status->status }}">
                    {{ ucfirst($status->status) }}
                </span>
            </td>
            <td class="text-center">{{ $status->total }}</td>
            <td class="text-right">R$ {{ number_format($status->valor, 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ number_format($status->valor / $status->total, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- Pagamentos por Status -->
@if($resumo['pagamentos_por_status']->count() > 0)
<div class="page-break"></div>
<h3><i class="fas fa-money-bill"></i> Pagamentos por Status</h3>
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th>Quantidade</th>
            <th>Valor Total</th>
            <th>Valor Médio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resumo['pagamentos_por_status'] as $status)
        <tr>
            <td>
                <span class="status-badge status-{{ $status->status }}">
                    {{ ucfirst($status->status) }}
                </span>
            </td>
            <td class="text-center">{{ $status->total }}</td>
            <td class="text-right">R$ {{ number_format($status->valor, 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ number_format($status->valor / $status->total, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- Detalhamento de Contratos -->
@if($contratos->count() > 0)
<div class="page-break"></div>
<h3><i class="fas fa-file-contract"></i> Detalhamento de Contratos</h3>
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Descrição</th>
            <th>Data Início</th>
            <th>Data Fim</th>
            <th>Status</th>
            <th>Gestor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contratos as $contrato)
        <tr>
            <td>{{ $contrato->numero }}</td>
            <td>{{ Str::limit($contrato->descricao, 40) }}</td>
            <td class="text-center">{{ $contrato->data_inicio->format('d/m/Y') }}</td>
            <td class="text-center">{{ $contrato->data_fim->format('d/m/Y') }}</td>
            <td class="text-center">
                <span class="status-badge status-{{ $contrato->status }}">
                    {{ ucfirst($contrato->status) }}
                </span>
            </td>
            <td>{{ $contrato->gestor->nome ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<!-- Detalhamento de Medições -->
@if($medicoes->count() > 0)
<div class="page-break"></div>
<h3><i class="fas fa-ruler"></i> Detalhamento de Medições</h3>
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Contrato</th>
            <th>Catálogo</th>
            <th>Data</th>
            <th>Quantidade</th>
            <th>Valor Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($medicoes->take(50) as $medicao)
        <tr>
            <td>{{ $medicao->numero_medicao }}</td>
            <td>{{ $medicao->contrato->numero ?? 'N/A' }}</td>
            <td>{{ Str::limit($medicao->catalogo->nome ?? 'N/A', 20) }}</td>
            <td class="text-center">{{ $medicao->data_medicao->format('d/m/Y') }}</td>
            <td class="text-right">{{ number_format($medicao->quantidade, 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ number_format($medicao->valor_total, 2, ',', '.') }}</td>
            <td class="text-center">
                <span class="status-badge status-{{ $medicao->status }}">
                    {{ ucfirst($medicao->status) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@if($medicoes->count() > 50)
<div class="info-box">
    <p><i class="fas fa-info-circle"></i> Mostrando apenas os primeiros 50 registros de {{ $medicoes->count() }} medições encontradas.</p>
</div>
@endif
@endif

<!-- Detalhamento de Pagamentos -->
@if($pagamentos->count() > 0)
<div class="page-break"></div>
<h3><i class="fas fa-money-bill"></i> Detalhamento de Pagamentos</h3>
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Medição</th>
            <th>Contrato</th>
            <th>Data</th>
            <th>Valor</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagamentos->take(50) as $pagamento)
        <tr>
            <td>{{ $pagamento->numero_pagamento }}</td>
            <td>{{ $pagamento->medicao->numero_medicao ?? 'N/A' }}</td>
            <td>{{ $pagamento->medicao->contrato->numero ?? 'N/A' }}</td>
            <td class="text-center">{{ $pagamento->data_pagamento->format('d/m/Y') }}</td>
            <td class="text-right">R$ {{ number_format($pagamento->valor_pagamento, 2, ',', '.') }}</td>
            <td class="text-center">
                <span class="status-badge status-{{ $pagamento->status }}">
                    {{ ucfirst($pagamento->status) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@if($pagamentos->count() > 50)
<div class="info-box">
    <p><i class="fas fa-info-circle"></i> Mostrando apenas os primeiros 50 registros de {{ $pagamentos->count() }} pagamentos encontrados.</p>
</div>
@endif
@endif

<!-- Análise Financeira -->
<div class="page-break"></div>
<div class="summary-box">
    <h3><i class="fas fa-calculator"></i> Análise Financeira</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">{{ number_format(($resumo['valor_total_pagamentos'] / max($resumo['valor_total_medicoes'], 1)) * 100, 1) }}%</div>
            <div class="summary-label">Taxa de Pagamento</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($resumo['valor_total_medicoes'] - $resumo['valor_total_pagamentos'], 2, ',', '.') }}</div>
            <div class="summary-label">Saldo Pendente</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $resumo['total_medicoes'] > 0 ? number_format($resumo['valor_total_medicoes'] / $resumo['total_medicoes'], 2, ',', '.') : '0,00' }}</div>
            <div class="summary-label">Valor Médio por Medição</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $resumo['total_pagamentos'] > 0 ? number_format($resumo['valor_total_pagamentos'] / $resumo['total_pagamentos'], 2, ',', '.') : '0,00' }}</div>
            <div class="summary-label">Valor Médio por Pagamento</div>
        </div>
    </div>
</div>
@endsection




