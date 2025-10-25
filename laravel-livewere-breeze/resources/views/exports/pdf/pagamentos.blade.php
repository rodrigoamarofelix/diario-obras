@extends('exports.pdf.layout')

@section('content')
@php
    $titulo = 'Relatório de Pagamentos';
@endphp

@if(isset($resumo) && $resumo)
<div class="summary-box">
    <h3><i class="fas fa-chart-bar"></i> Resumo</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">{{ $pagamentos->count() }}</div>
            <div class="summary-label">Total de Pagamentos</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($pagamentos->sum('valor_pagamento'), 2, ',', '.') }}</div>
            <div class="summary-label">Valor Total Pago</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $pagamentos->where('status', 'pago')->count() }}</div>
            <div class="summary-label">Pagamentos Realizados</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $pagamentos->where('status', 'pendente')->count() }}</div>
            <div class="summary-label">Pagamentos Pendentes</div>
        </div>
    </div>
</div>
@endif

@if($pagamentos->count() > 0)
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Medição</th>
            <th>Contrato</th>
            <th>Data Pagamento</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Documento Redmine</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagamentos as $pagamento)
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
            <td>{{ $pagamento->documento_redmine ?? 'N/A' }}</td>
            <td>{{ $pagamento->usuario->name ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #e9ecef; font-weight: bold;">
            <td colspan="4" class="text-right">TOTAL:</td>
            <td class="text-right">R$ {{ number_format($pagamentos->sum('valor_pagamento'), 2, ',', '.') }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
@else
<div class="info-box">
    <h3><i class="fas fa-info-circle"></i> Nenhum Pagamento Encontrado</h3>
    <p>Não foram encontrados pagamentos com os filtros aplicados.</p>
</div>
@endif
@endsection




