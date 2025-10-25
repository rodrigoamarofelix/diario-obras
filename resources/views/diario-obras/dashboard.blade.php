@extends('layouts.admin')

@section('title', 'Dashboard - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-hard-hat text-warning"></i>
                        Diário de Obras - Dashboard Avançado
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Diário de Obras</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Alertas -->
            @if(isset($alertas) && count($alertas) > 0)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bell text-warning"></i>
                                Alertas e Notificações
                            </h3>
                        </div>
                        <div class="card-body">
                            @foreach($alertas as $alerta)
                            <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible">
                                <i class="{{ $alerta['icone'] }}"></i>
                                <strong>{{ $alerta['titulo'] }}:</strong> {{ $alerta['mensagem'] }}
                                @if(isset($alerta['acao']))
                                <a href="{{ $alerta['acao'] }}" class="btn btn-sm btn-outline-{{ $alerta['tipo'] }} ml-2">
                                    Ver Detalhes
                                </a>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Cards de Estatísticas Avançadas - Estilo Moderno -->
            <div class="row">
                <!-- Relatórios -->
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="fas fa-file-alt fa-2x"></i>
                                <h3 class="mb-0">{{ $stats['relatorios_total'] ?? 0 }}</h3>
                            </div>
                            <h6 class="card-title">Relatórios</h6>
                            <small class="opacity-75">{{ $stats['relatorios_mes'] ?? 0 }} este mês</small>
                        </div>
                        <div class="card-footer bg-primary-light">
                            <a href="{{ route('diario-obras.relatorios') }}" class="text-white text-decoration-none">
                                <small>Ver todos <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Atividades -->
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="fas fa-tasks fa-2x"></i>
                                <h3 class="mb-0">{{ $stats['atividades_total'] ?? 0 }}</h3>
                            </div>
                            <h6 class="card-title">Atividades</h6>
                            <small class="opacity-75">{{ $stats['atividades_hoje'] ?? 0 }} hoje</small>
                        </div>
                        <div class="card-footer bg-success-light">
                            <a href="{{ route('diario-obras.atividades.index') }}" class="text-white text-decoration-none">
                                <small>Ver todas <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ocorrências -->
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                <h3 class="mb-0">{{ $stats['ocorrencias_total'] ?? 0 }}</h3>
                            </div>
                            <h6 class="card-title">Ocorrências</h6>
                            <small class="opacity-75">{{ $stats['ocorrencias_pendentes'] ?? 0 }} pendentes</small>
                        </div>
                        <div class="card-footer bg-warning-light">
                            <a href="{{ route('diario-obras.atividades.index') }}?status=pendente" class="text-white text-decoration-none">
                                <small>Ver todas <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Comentários -->
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="fas fa-comments fa-2x"></i>
                                <h3 class="mb-0">{{ $stats['comentarios_total'] ?? 0 }}</h3>
                            </div>
                            <h6 class="card-title">Comentários</h6>
                            <small class="opacity-75">{{ $stats['comentarios_semana'] ?? 0 }} esta semana</small>
                        </div>
                        <div class="card-footer bg-info-light">
                            <a href="{{ route('diario-obras.atividades.index') }}" class="text-white text-decoration-none">
                                <small>Ver todos <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Fotos -->
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-secondary text-white h-100">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="fas fa-camera fa-2x"></i>
                                <h3 class="mb-0">{{ $stats['fotos_total'] ?? 0 }}</h3>
                            </div>
                            <h6 class="card-title">Fotos</h6>
                            <small class="opacity-75">{{ $stats['fotos_hoje'] ?? 0 }} hoje</small>
                        </div>
                        <div class="card-footer bg-secondary-light">
                            <a href="{{ route('diario-obras.fotos.index') }}" class="text-white text-decoration-none">
                                <small>Ver todas <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vídeos -->
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="card bg-dark text-white h-100">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <i class="fas fa-video fa-2x"></i>
                                <h3 class="mb-0">{{ $stats['videos_total'] ?? 0 }}</h3>
                            </div>
                            <h6 class="card-title">Vídeos</h6>
                            <small class="opacity-75">{{ $stats['videos_mes'] ?? 0 }} este mês</small>
                        </div>
                        <div class="card-footer bg-dark-light">
                            <a href="{{ route('diario-obras.fotos.index') }}?tipo=video" class="text-white text-decoration-none">
                                <small>Ver todos <i class="fas fa-arrow-right"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos e Métricas -->
            <div class="row">
                <!-- Gráfico de Projetos por Status -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie text-primary"></i>
                                Projetos por Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="projetosStatusChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Atividades por Mês -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line text-success"></i>
                                Atividades por Mês
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="atividadesMesChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Custos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar text-warning"></i>
                                Custos de Materiais por Mês
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="custosMesChart" width="400" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas Avançadas -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt text-warning"></i>
                                Ações Rápidas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <a href="{{ route('diario-obras.projetos.create') }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus"></i>
                                        Novo Projeto
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <a href="{{ route('diario-obras.atividades.create') }}" class="btn btn-success btn-block">
                                        <i class="fas fa-tasks"></i>
                                        Nova Atividade
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <a href="{{ route('diario-obras.equipe.create') }}" class="btn btn-warning btn-block">
                                        <i class="fas fa-user-plus"></i>
                                        Registrar Equipe
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <a href="{{ route('diario-obras.materiais.create') }}" class="btn btn-info btn-block">
                                        <i class="fas fa-box"></i>
                                        Registrar Material
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <a href="{{ route('diario-obras.fotos.create') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-camera"></i>
                                        Upload Foto
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-2">
                                    <a href="{{ route('diario-obras.relatorios') }}" class="btn btn-dark btn-block">
                                        <i class="fas fa-chart-bar"></i>
                                        Relatórios
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projetos Recentes com Mais Detalhes -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building text-primary"></i>
                                Projetos Recentes
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(isset($projetosRecentes) && $projetosRecentes->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Cliente</th>
                                                <th>Status</th>
                                                <th>Progresso</th>
                                                <th>Responsável</th>
                                                <th>Valor</th>
                                                <th>Prazo</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($projetosRecentes as $projeto)
                                            <tr>
                                                <td>
                                                    <strong>{{ $projeto->nome }}</strong>
                                                    @if($projeto->descricao)
                                                        <br><small class="text-muted">{{ Str::limit($projeto->descricao, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $projeto->cliente }}</td>
                                                <td>
                                                    <span class="badge badge-{{
                                                        $projeto->status == 'em_andamento' ? 'success' :
                                                        ($projeto->status == 'planejamento' ? 'warning' :
                                                        ($projeto->status == 'concluido' ? 'info' :
                                                        ($projeto->status == 'pausado' ? 'secondary' : 'danger')))
                                                    }}">
                                                        {{ ucfirst(str_replace('_', ' ', $projeto->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-primary" style="width: {{ $projeto->progresso }}%"></div>
                                                    </div>
                                                    <small>{{ $projeto->progresso }}%</small>
                                                </td>
                                                <td>{{ $projeto->responsavel->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($projeto->valor_total)
                                                        R$ {{ number_format($projeto->valor_total, 2, ',', '.') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($projeto->data_fim_prevista)
                                                        {{ $projeto->data_fim_prevista->format('d/m/Y') }}
                                                        @if($projeto->dias_restantes < 0)
                                                            <br><small class="text-danger">Atrasado</small>
                                                        @elseif($projeto->dias_restantes <= 7)
                                                            <br><small class="text-warning">Próximo do prazo</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('diario-obras.projetos.show', $projeto) }}" class="btn btn-sm btn-primary" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('diario-obras.relatorios.projeto', $projeto) }}" class="btn btn-sm btn-info" title="Relatório">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Nenhum projeto cadastrado</h4>
                                    <p class="text-muted">Comece criando seu primeiro projeto de obra.</p>
                                    <a href="{{ route('diario-obras.projetos.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Criar Primeiro Projeto
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atividades de Hoje com Mais Detalhes -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-day text-success"></i>
                                Atividades de Hoje
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(isset($atividadesHoje) && $atividadesHoje->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Projeto</th>
                                                <th>Tipo</th>
                                                <th>Status</th>
                                                <th>Horário</th>
                                                <th>Responsável</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($atividadesHoje as $atividade)
                                            <tr>
                                                <td>
                                                    <strong>{{ $atividade->titulo }}</strong>
                                                    @if($atividade->descricao)
                                                        <br><small class="text-muted">{{ Str::limit($atividade->descricao, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $atividade->projeto->nome ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ ucfirst($atividade->tipo) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $atividade->status == 'concluido' ? 'success' : ($atividade->status == 'em_andamento' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $atividade->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($atividade->hora_inicio && $atividade->hora_fim)
                                                        {{ $atividade->hora_inicio }} - {{ $atividade->hora_fim }}
                                                        @if($atividade->tempo_gasto_minutos)
                                                            <br><small class="text-muted">{{ $atividade->tempo_gasto_minutos }} min</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $atividade->responsavel->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('diario-obras.atividades.show', $atividade) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Nenhuma atividade registrada para hoje</h4>
                                    <p class="text-muted">Registre as atividades do dia para acompanhar o progresso.</p>
                                    <a href="{{ route('diario-obras.atividades.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i>
                                        Registrar Atividade
                                    </a>
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

@push('styles')
<style>
    .bg-primary-light {
        background-color: rgba(0, 123, 255, 0.8) !important;
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.8) !important;
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.8) !important;
    }
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.8) !important;
    }
    .bg-secondary-light {
        background-color: rgba(108, 117, 125, 0.8) !important;
    }
    .bg-dark-light {
        background-color: rgba(52, 58, 64, 0.8) !important;
    }
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .opacity-75 {
        opacity: 0.75;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Projetos por Status
    const projetosStatusCtx = document.getElementById('projetosStatusChart').getContext('2d');
    new Chart(projetosStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Em Andamento', 'Planejamento', 'Concluído', 'Pausado', 'Cancelado'],
            datasets: [{
                data: [{{ $stats['projetos_ativos'] ?? 0 }}, 0, {{ $stats['projetos_concluidos'] ?? 0 }}, 0, 0],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#6c757d',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Atividades por Mês
    const atividadesMesCtx = document.getElementById('atividadesMesChart').getContext('2d');
    new Chart(atividadesMesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Atividades',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, {{ $stats['atividades_hoje'] ?? 0 }}, 0, 0],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Custos por Mês
    const custosMesCtx = document.getElementById('custosMesChart').getContext('2d');
    new Chart(custosMesCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Custos (R$)',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, {{ $stats['valor_materiais_mes'] ?? 0 }}, 0, 0],
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: '#ffc107',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
