@extends('exports.pdf.layout')

@section('content')
@php
    $titulo = 'Relatório de Medições';
@endphp

@if(isset($resumo) && $resumo)
<div class="summary-box">
    <h3><i class="fas fa-chart-bar"></i> Resumo</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">{{ $medicoes->count() }}</div>
            <div class="summary-label">Total de Medições</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($medicoes->sum('valor_total'), 2, ',', '.') }}</div>
            <div class="summary-label">Valor Total</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $medicoes->where('status', 'aprovado')->count() }}</div>
            <div class="summary-label">Medições Aprovadas</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $medicoes->where('status', 'pendente')->count() }}</div>
            <div class="summary-label">Medições Pendentes</div>
        </div>
    </div>
</div>
@endif

@if($medicoes->count() > 0)
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Contrato</th>
            <th>Catálogo</th>
            <th>Lotação</th>
            <th>Data Medição</th>
            <th>Quantidade</th>
            <th>Valor Unitário</th>
            <th>Valor Total</th>
            <th>Status</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        @foreach($medicoes as $medicao)
        <tr>
            <td>{{ $medicao->numero_medicao }}</td>
            <td>{{ $medicao->contrato->numero ?? 'N/A' }}</td>
            <td>{{ Str::limit($medicao->catalogo->nome ?? 'N/A', 20) }}</td>
            <td>{{ Str::limit($medicao->lotacao->nome ?? 'N/A', 15) }}</td>
            <td class="text-center">{{ $medicao->data_medicao->format('d/m/Y') }}</td>
            <td class="text-right">{{ number_format($medicao->quantidade, 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ number_format($medicao->valor_unitario, 2, ',', '.') }}</td>
            <td class="text-right">R$ {{ number_format($medicao->valor_total, 2, ',', '.') }}</td>
            <td class="text-center">
                <span class="status-badge status-{{ $medicao->status }}">
                    {{ ucfirst($medicao->status) }}
                </span>
            </td>
            <td>{{ $medicao->usuario->name ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #e9ecef; font-weight: bold;">
            <td colspan="7" class="text-right">TOTAL:</td>
            <td class="text-right">R$ {{ number_format($medicoes->sum('valor_total'), 2, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@else
<div class="info-box">
    <h3><i class="fas fa-info-circle"></i> Nenhuma Medição Encontrada</h3>
    <p>Não foram encontradas medições com os filtros aplicados.</p>
</div>
@endif
@endsection




