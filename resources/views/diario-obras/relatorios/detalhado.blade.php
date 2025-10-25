@extends('layouts.admin')

@section('title', 'Relatório Detalhado - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-file-alt text-primary"></i>
                        Relatório Detalhado - {{ $projeto->nome ?? 'Projeto' }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.relatorios') }}">Relatórios</a></li>
                        <li class="breadcrumb-item active">Detalhado</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Informações do Projeto -->
            @if(isset($projeto))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle text-info"></i>
                                Informações da Obra
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Status:</strong>
                                    <span class="badge badge-{{
                                        $projeto->status == 'em_andamento' ? 'success' :
                                        ($projeto->status == 'planejamento' ? 'warning' :
                                        ($projeto->status == 'concluido' ? 'info' :
                                        ($projeto->status == 'pausado' ? 'secondary' : 'danger')))
                                    }} ml-2">
                                        {{ ucfirst(str_replace('_', ' ', $projeto->status)) }}
                                    </span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Número do contrato:</strong> {{ $projeto->contrato ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Endereço:</strong> {{ $projeto->cidade }}, {{ $projeto->estado }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Prazo decorrido:</strong>
                                    <div class="progress mt-1" style="height: 20px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $projeto->progresso }}%">
                                            {{ $projeto->progresso }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <strong>Prazo contratual:</strong> {{ $projeto->data_fim_prevista ? $projeto->data_fim_prevista->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Prazo decorrido:</strong> {{ $projeto->data_inicio ? $projeto->data_inicio->diffInDays(now()) : 0 }} dias
                                </div>
                                <div class="col-md-3">
                                    <strong>Prazo a vencer:</strong> {{ $projeto->dias_restantes ?? 0 }} dias
                                </div>
                                <div class="col-md-3">
                                    <strong>Responsável:</strong> {{ $projeto->responsavel->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Seção: Atividades / Tarefas -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tasks text-primary"></i>
                                Atividades / Tarefas ({{ $atividades->count() }})
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($atividades->count() > 0)
                                @foreach($atividades as $atividade)
                                <div class="row mb-3 p-3 border rounded">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">{{ $atividade->titulo }}</h6>
                                        <p class="mb-1 text-muted">{{ $atividade->descricao }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $atividade->data_atividade->format('d/m/Y') }}
                                            @if($atividade->hora_inicio && $atividade->hora_fim)
                                                | <i class="fas fa-clock"></i> {{ $atividade->hora_inicio }} - {{ $atividade->hora_fim }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="badge badge-{{
                                            $atividade->status == 'concluido' ? 'success' :
                                            ($atividade->status == 'em_andamento' ? 'warning' :
                                            ($atividade->status == 'planejado' ? 'info' : 'secondary'))
                                        }} badge-lg">
                                            {{ ucfirst(str_replace('_', ' ', $atividade->status)) }}
                                        </span>
                                        @if($atividade->fotos->count() > 0)
                                        <div class="mt-2">
                                            <i class="fas fa-camera text-info"></i>
                                            <span class="badge badge-info">{{ $atividade->fotos->count() }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma atividade registrada</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção: Ocorrências / Observações -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Ocorrências / Observações ({{ $ocorrencias->count() }})
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($ocorrencias->count() > 0)
                                @foreach($ocorrencias as $ocorrencia)
                                <div class="row mb-3 p-3 border rounded">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">{{ $ocorrencia->titulo ?? 'Ocorrência' }}</h6>
                                        <p class="mb-1">{{ $ocorrencia->problemas_encontrados ?? $ocorrencia->observacoes }}</p>
                                        @if($ocorrencia->solucoes_aplicadas)
                                        <div class="mt-2">
                                            <strong>Soluções aplicadas:</strong>
                                            <p class="mb-0">{{ $ocorrencia->solucoes_aplicadas }}</p>
                                        </div>
                                        @endif
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $ocorrencia->data_atividade->format('d/m/Y') }}
                                            @if($ocorrencia->responsavel)
                                                | <i class="fas fa-user"></i> {{ $ocorrencia->responsavel->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        @if($ocorrencia->solucoes_aplicadas)
                                            <span class="badge badge-success badge-lg">Resolvida</span>
                                        @else
                                            <span class="badge badge-warning badge-lg">Pendente</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma ocorrência registrada</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção: Comentários -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-comments text-info"></i>
                                Comentários ({{ $comentarios->count() }})
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($comentarios->count() > 0)
                                @foreach($comentarios as $comentario)
                                <div class="row mb-3 p-3 border rounded">
                                    <div class="col-md-8">
                                        <p class="mb-1">{{ $comentario->observacoes }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $comentario->data_atividade->format('d/m/Y') }}
                                            @if($comentario->responsavel)
                                                | <i class="fas fa-user"></i> {{ $comentario->responsavel->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="badge badge-info badge-lg">Comentário</span>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhum comentário registrado</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção: Galeria de Fotos -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-camera text-secondary"></i>
                                Galeria de Fotos ({{ $fotos->count() }})
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($fotos->count() > 0)
                                <div class="row">
                                    @foreach($fotos->take(8) as $foto)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                        <div class="card">
                                            <img src="{{ asset('storage/' . $foto->caminho_arquivo) }}"
                                                 class="card-img-top"
                                                 style="height: 200px; object-fit: cover;"
                                                 alt="Foto da obra">
                                            <div class="card-body p-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> {{ $foto->data_foto->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if($fotos->count() > 8)
                                <div class="text-center mt-3">
                                    <button class="btn btn-outline-primary" onclick="verTodasFotos()">
                                        <i class="fas fa-images"></i> Ver todas as fotos ({{ $fotos->count() }})
                                    </button>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma foto registrada</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações do Relatório -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="{{ route('diario-obras.relatorios') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-arrow-left"></i> Voltar aos Relatórios
                            </a>
                            <button class="btn btn-primary mr-2" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir Relatório
                            </button>
                            <a href="{{ request()->fullUrlWithQuery(['formato' => 'pdf']) }}" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </a>
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
    @media print {
        .btn, .card-header, .breadcrumb {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
function verTodasFotos() {
    // Implementar modal ou página para ver todas as fotos
    alert('Funcionalidade de visualização completa de fotos será implementada em breve.');
}
</script>
@endpush



