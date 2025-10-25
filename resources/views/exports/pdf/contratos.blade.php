@extends('exports.pdf.layout')

@section('content')
@php
    $titulo = 'Relatório de Contratos';
@endphp

@if(isset($resumo) && $resumo)
<div class="summary-box">
    <h3><i class="fas fa-chart-bar"></i> Resumo</h3>
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">{{ $contratos->count() }}</div>
            <div class="summary-label">Total de Contratos</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $contratos->where('status', 'ativo')->count() }}</div>
            <div class="summary-label">Contratos Ativos</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $contratos->where('status', 'vencido')->count() }}</div>
            <div class="summary-label">Contratos Vencidos</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">{{ $contratos->where('status', 'suspenso')->count() }}</div>
            <div class="summary-label">Contratos Suspensos</div>
        </div>
    </div>
</div>
@endif

@if($contratos->count() > 0)
<table>
    <thead>
        <tr>
            <th>Número</th>
            <th>Descrição</th>
            <th>Data Início</th>
            <th>Data Fim</th>
            <th>Status</th>
            <th>Gestor</th>
            <th>Fiscal</th>
            <th>Criado em</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contratos as $contrato)
        <tr>
            <td>{{ $contrato->numero }}</td>
            <td>{{ Str::limit($contrato->descricao, 50) }}</td>
            <td class="text-center">{{ $contrato->data_inicio->format('d/m/Y') }}</td>
            <td class="text-center">{{ $contrato->data_fim->format('d/m/Y') }}</td>
            <td class="text-center">
                <span class="status-badge status-{{ $contrato->status }}">
                    {{ ucfirst($contrato->status) }}
                </span>
            </td>
            <td>{{ $contrato->gestor->nome ?? 'N/A' }}</td>
            <td>{{ $contrato->fiscal->nome ?? 'N/A' }}</td>
            <td class="text-center">{{ $contrato->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="info-box">
    <h3><i class="fas fa-info-circle"></i> Nenhum Contrato Encontrado</h3>
    <p>Não foram encontrados contratos com os filtros aplicados.</p>
</div>
@endif
@endsection




