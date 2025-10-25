@extends('layouts.admin')

@section('title', 'Detalhes do Material - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-eye text-primary"></i>
                        Detalhes do Material
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.materiais.index') }}">Materiais</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-boxes text-primary"></i>
                                {{ $material->nome_material }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('diario-obras.materiais.edit', $material) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Material:</strong></td>
                                            <td>{{ $material->nome_material }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Projeto:</strong></td>
                                            <td>{{ $material->projeto->nome ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Quantidade:</strong></td>
                                            <td>{{ number_format($material->quantidade, 3) }} {{ $material->unidade_medida }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo de Movimento:</strong></td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $material->tipo_movimento == 'entrada' ? 'success' :
                                                    ($material->tipo_movimento == 'saida' ? 'danger' : 'warning')
                                                }}">
                                                    {{ ucfirst($material->tipo_movimento) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data:</strong></td>
                                            <td>{{ $material->data_movimento->format('d/m/Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Valor Unitário:</strong></td>
                                            <td>
                                                @if($material->valor_unitario)
                                                    R$ {{ number_format($material->valor_unitario, 2, ',', '.') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Valor Total:</strong></td>
                                            <td>
                                                @if($material->valor_total)
                                                    R$ {{ number_format($material->valor_total, 2, ',', '.') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fornecedor:</strong></td>
                                            <td>{{ $material->fornecedor ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nota Fiscal:</strong></td>
                                            <td>{{ $material->nota_fiscal ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Responsável:</strong></td>
                                            <td>{{ $material->responsavel->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($material->descricao)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-align-left"></i> Descrição</h5>
                                    <p class="text-muted">{{ $material->descricao }}</p>
                                </div>
                            </div>
                            @endif

                            @if($material->observacoes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                    <p class="text-muted">{{ $material->observacoes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

