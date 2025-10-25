@extends('layouts.admin')

@section('title', 'Detalhes do Projeto/Obra - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-eye text-primary"></i>
                        Detalhes do Projeto/Obra
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.projetos.index') }}">Projetos/Obras</a></li>
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
                                <i class="fas fa-building text-primary"></i>
                                {{ $projeto->nome }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('diario-obras.fotos.por-projeto', $projeto) }}" class="btn btn-info btn-sm mr-2">
                                    <i class="fas fa-images"></i>
                                    Fotos
                                </a>
                                <a href="{{ route('diario-obras.projetos.edit', $projeto) }}" class="btn btn-warning btn-sm">
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
                                            <td><strong>Nome:</strong></td>
                                            <td>{{ $projeto->nome }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cliente:</strong></td>
                                            <td>{{ $projeto->cliente }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $projeto->status == 'concluido' ? 'success' :
                                                    ($projeto->status == 'em_andamento' ? 'warning' :
                                                    ($projeto->status == 'planejamento' ? 'secondary' :
                                                    ($projeto->status == 'pausado' ? 'info' : 'danger')))
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $projeto->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prioridade:</strong></td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $projeto->prioridade == 'urgente' ? 'danger' :
                                                    ($projeto->prioridade == 'alta' ? 'warning' :
                                                    ($projeto->prioridade == 'media' ? 'info' : 'secondary'))
                                                }}">
                                                    {{ ucfirst($projeto->prioridade) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Responsável:</strong></td>
                                            <td>{{ $projeto->responsavel->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Data de Início:</strong></td>
                                            <td>{{ $projeto->data_inicio->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data Prevista:</strong></td>
                                            <td>{{ $projeto->data_fim_prevista ? $projeto->data_fim_prevista->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data Real:</strong></td>
                                            <td>{{ $projeto->data_fim_real ? $projeto->data_fim_real->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Valor Total:</strong></td>
                                            <td>
                                                @if($projeto->valor_total)
                                                    R$ {{ number_format($projeto->valor_total, 2, ',', '.') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contrato:</strong></td>
                                            <td>{{ $projeto->contrato ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-map-marker-alt"></i> Endereço</h5>
                                    <p class="text-muted">
                                        {{ $projeto->endereco }}<br>
                                        @if($projeto->complemento)
                                            {{ $projeto->complemento }}<br>
                                        @endif
                                        @if($projeto->bairro)
                                            {{ $projeto->bairro }}<br>
                                        @endif
                                        {{ $projeto->cidade }}/{{ $projeto->estado }}<br>
                                        @if($projeto->cep)
                                            CEP: {{ $projeto->cep }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-chart-line"></i> Progresso</h5>
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $projeto->progresso }}%"
                                             aria-valuenow="{{ $projeto->progresso }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $projeto->progresso }}%
                                        </div>
                                    </div>
                                    @if($projeto->diasRestantes > 0)
                                        <small class="text-muted">{{ $projeto->diasRestantes }} dias restantes</small>
                                    @elseif($projeto->diasRestantes < 0)
                                        <small class="text-danger">{{ abs($projeto->diasRestantes) }} dias de atraso</small>
                                    @endif
                                </div>
                            </div>

                            @if($projeto->descricao)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-align-left"></i> Descrição</h5>
                                    <p class="text-muted">{{ $projeto->descricao }}</p>
                                </div>
                            </div>
                            @endif

                            @if($projeto->observacoes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                    <p class="text-muted">{{ $projeto->observacoes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Empresas Relacionadas -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-building"></i> Empresas Relacionadas ({{ $projeto->empresas->count() }})</h5>
                                    @if($projeto->empresas->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Empresa</th>
                                                        <th>Tipo de Participação</th>
                                                        <th>CNPJ</th>
                                                        <th>Contato</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($projeto->empresas as $empresa)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $empresa->nome }}</strong>
                                                            @if($empresa->razao_social)
                                                                <br><small class="text-muted">{{ $empresa->razao_social }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info">
                                                                {{ ucfirst(str_replace('_', ' ', $empresa->pivot->tipo_participacao)) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $empresa->cnpj_formatado }}</td>
                                                        <td>
                                                            @if($empresa->email)
                                                                <a href="mailto:{{ $empresa->email }}" class="text-primary">
                                                                    <i class="fas fa-envelope"></i> {{ $empresa->email }}
                                                                </a><br>
                                                            @endif
                                                            @if($empresa->whatsapp)
                                                                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $empresa->whatsapp) }}"
                                                                   target="_blank" class="text-success">
                                                                    <i class="fab fa-whatsapp"></i> {{ $empresa->whatsapp_formatado }}
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <a href="{{ route('empresas.show', $empresa) }}"
                                                                   class="btn btn-sm btn-primary" title="Ver empresa">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('empresas.edit', $empresa) }}"
                                                                   class="btn btn-sm btn-warning" title="Editar empresa">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Nenhuma empresa associada a este projeto.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
