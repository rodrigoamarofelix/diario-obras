@extends('layouts.admin')

@section('title', 'Detalhes da Atividade - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-eye text-primary"></i>
                        Detalhes da Atividade
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.atividades.index') }}">Atividades</a></li>
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
                                <i class="fas fa-tasks text-primary"></i>
                                {{ $atividade->titulo }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('diario-obras.atividades.edit', $atividade) }}" class="btn btn-warning btn-sm">
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
                                            <td><strong>Projeto:</strong></td>
                                            <td>{{ $atividade->projeto->nome ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data:</strong></td>
                                            <td>{{ $atividade->data_atividade->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($atividade->tipo) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $atividade->status == 'concluido' ? 'success' :
                                                    ($atividade->status == 'em_andamento' ? 'warning' :
                                                    ($atividade->status == 'planejado' ? 'secondary' : 'danger'))
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $atividade->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Responsável:</strong></td>
                                            <td>{{ $atividade->responsavel->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Horário:</strong></td>
                                            <td>
                                                @if($atividade->hora_inicio && $atividade->hora_fim)
                                                    {{ $atividade->hora_inicio }} - {{ $atividade->hora_fim }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tempo Gasto:</strong></td>
                                            <td>
                                                @if($atividade->tempo_gasto_minutos)
                                                    {{ $atividade->tempo_gasto_minutos }} minutos
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Criado em:</strong></td>
                                            <td>{{ $atividade->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Atualizado em:</strong></td>
                                            <td>{{ $atividade->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($atividade->descricao)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-align-left"></i> Descrição</h5>
                                    <p class="text-muted">{{ $atividade->descricao }}</p>
                                </div>
                            </div>
                            @endif

                            @if($atividade->observacoes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                    <p class="text-muted">{{ $atividade->observacoes }}</p>
                                </div>
                            </div>
                            @endif

                            @if($atividade->problemas_encontrados)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-exclamation-triangle text-warning"></i> Problemas Encontrados</h5>
                                    <p class="text-muted">{{ $atividade->problemas_encontrados }}</p>
                                </div>
                            </div>
                            @endif

                            @if($atividade->solucoes_aplicadas)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-check-circle text-success"></i> Soluções Aplicadas</h5>
                                    <p class="text-muted">{{ $atividade->solucoes_aplicadas }}</p>
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

