@extends('layouts.admin')

@section('title', 'Alertas e Notificações - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-bell text-warning"></i>
                        Alertas e Notificações
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Alertas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Alertas Críticos -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                Alertas Críticos
                            </h3>
                        </div>
                        <div class="card-body">
                            @php
                                $projetosAtrasados = \App\Models\Projeto::where('data_fim_prevista', '<', now())
                                    ->whereIn('status', ['planejamento', 'em_andamento'])
                                    ->get();
                            @endphp

                            @if($projetosAtrasados->count() > 0)
                                @foreach($projetosAtrasados as $projeto)
                                <div class="alert alert-danger alert-dismissible">
                                    <h5><i class="icon fas fa-ban"></i> Projeto Atrasado!</h5>
                                    <strong>{{ $projeto->nome }}</strong> está atrasado em
                                    <strong>{{ now()->diffInDays($projeto->data_fim_prevista) }} dias</strong>.
                                    <br>
                                    <small class="text-muted">
                                        Prazo original: {{ $projeto->data_fim_prevista->format('d/m/Y') }} |
                                        Cliente: {{ $projeto->cliente }}
                                    </small>
                                    <div class="mt-2">
                                        <a href="{{ route('diario-obras.projetos.show', $projeto) }}" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-eye"></i> Ver Projeto
                                        </a>
                                        <a href="{{ route('diario-obras.projetos.edit', $projeto) }}" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-edit"></i> Atualizar Prazo
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Ótimo!</strong> Nenhum projeto está atrasado no momento.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de Atenção -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle text-warning"></i>
                                Alertas de Atenção
                            </h3>
                        </div>
                        <div class="card-body">
                            @php
                                $atividadesSemResponsavel = \App\Models\AtividadeObra::whereNull('responsavel_id')
                                    ->where('status', '!=', 'concluido')
                                    ->count();

                                $projetosSemDataFim = \App\Models\Projeto::whereNull('data_fim_prevista')
                                    ->whereIn('status', ['planejamento', 'em_andamento'])
                                    ->count();

                                $atividadesAtrasadas = \App\Models\AtividadeObra::where('data_atividade', '<', now())
                                    ->where('status', '!=', 'concluido')
                                    ->count();
                            @endphp

                            @if($atividadesSemResponsavel > 0)
                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Atividades Sem Responsável</h5>
                                Existem <strong>{{ $atividadesSemResponsavel }}</strong> atividades sem responsável definido.
                                <a href="{{ route('diario-obras.atividades.index') }}" class="btn btn-sm btn-outline-warning ml-2">
                                    <i class="fas fa-tasks"></i> Ver Atividades
                                </a>
                            </div>
                            @endif

                            @if($projetosSemDataFim > 0)
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-calendar"></i> Projetos Sem Prazo</h5>
                                Existem <strong>{{ $projetosSemDataFim }}</strong> projetos sem data de conclusão prevista.
                                <a href="{{ route('diario-obras.projetos.index') }}" class="btn btn-sm btn-outline-info ml-2">
                                    <i class="fas fa-building"></i> Ver Projetos
                                </a>
                            </div>
                            @endif

                            @if($atividadesAtrasadas > 0)
                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-clock"></i> Atividades Atrasadas</h5>
                                Existem <strong>{{ $atividadesAtrasadas }}</strong> atividades com data passada que não foram concluídas.
                                <a href="{{ route('diario-obras.atividades.index') }}" class="btn btn-sm btn-outline-warning ml-2">
                                    <i class="fas fa-tasks"></i> Ver Atividades
                                </a>
                            </div>
                            @endif

                            @if($atividadesSemResponsavel == 0 && $projetosSemDataFim == 0 && $atividadesAtrasadas == 0)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Perfeito!</strong> Não há alertas de atenção no momento.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notificações do Sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bell text-primary"></i>
                                Notificações do Sistema
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @php
                                    $notificacoes = collect([
                                        [
                                            'tipo' => 'success',
                                            'icone' => 'fas fa-check-circle',
                                            'titulo' => 'Sistema Atualizado',
                                            'mensagem' => 'O sistema de Diário de Obras foi atualizado com novas funcionalidades.',
                                            'data' => now()->subHours(2)
                                        ],
                                        [
                                            'tipo' => 'info',
                                            'icone' => 'fas fa-info-circle',
                                            'titulo' => 'Backup Automático',
                                            'mensagem' => 'Backup automático realizado com sucesso.',
                                            'data' => now()->subDays(1)
                                        ],
                                        [
                                            'tipo' => 'warning',
                                            'icone' => 'fas fa-exclamation-triangle',
                                            'titulo' => 'Manutenção Programada',
                                            'mensagem' => 'Manutenção programada para domingo às 02:00.',
                                            'data' => now()->subDays(3)
                                        ]
                                    ]);
                                @endphp

                                @foreach($notificacoes as $index => $notificacao)
                                <div class="time-label">
                                    <span class="bg-{{ $notificacao['tipo'] }}">
                                        {{ $notificacao['data']->format('d M') }}
                                    </span>
                                </div>
                                <div>
                                    <i class="{{ $notificacao['icone'] }} bg-{{ $notificacao['tipo'] }}"></i>
                                    <div class="timeline-item">
                                        <span class="time">
                                            <i class="fas fa-clock"></i>
                                            {{ $notificacao['data']->format('H:i') }}
                                        </span>
                                        <h3 class="timeline-header">
                                            {{ $notificacao['titulo'] }}
                                        </h3>
                                        <div class="timeline-body">
                                            {{ $notificacao['mensagem'] }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <div>
                                    <i class="fas fa-clock bg-gray"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Alertas -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog text-secondary"></i>
                                Configurações de Alertas
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Alertas de Projetos Atrasados:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label">
                                                    Notificar quando projeto estiver atrasado
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label">
                                                    Notificar 7 dias antes do prazo
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Alertas de Atividades:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label">
                                                    Notificar atividades sem responsável
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label">
                                                    Notificar atividades atrasadas
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Frequência de Verificação:</label>
                                            <select class="form-control">
                                                <option value="diario">Diariamente</option>
                                                <option value="semanal">Semanalmente</option>
                                                <option value="mensal">Mensalmente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Método de Notificação:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label">
                                                    Notificação no sistema
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox">
                                                <label class="form-check-label">
                                                    Email
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

