@extends('layouts.admin')

@section('title', 'Projetos/Obras - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-building text-primary"></i>
                        Projetos/Obras
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Projetos/Obras</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Header com filtros e ações -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="fas fa-building text-primary"></i>
                        Obras ({{ $projetos->total() }})
                    </h2>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        <!-- Filtros -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-filter"></i> Todas as status
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">Todas</a>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'em_andamento']) }}">Em Andamento</a>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'planejamento']) }}">Planejamento</a>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'concluido']) }}">Concluído</a>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pausado']) }}">Pausado</a>
                            </div>
                        </div>

                        <!-- Botão de visualização -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="cardViewBtn">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="tableViewBtn">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>

                        <!-- Botão adicionar -->
                        <div class="dropdown">
                            <button class="btn btn-warning dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-plus"></i> ADICIONAR
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('diario-obras.relatorios') }}">
                                    <i class="fas fa-file-alt"></i> RELATÓRIO
                                </a>
                                <a class="dropdown-item" href="{{ route('diario-obras.projetos.create') }}">
                                    <i class="fas fa-building"></i> OBRA
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user"></i> USUÁRIO / LOGIN
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de pesquisa -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Pesquisa..." id="searchInput">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualização em Cartões -->
            <div id="cardView" class="row" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important; gap: 20px !important;">
                @if($projetos->count() > 0)
                    @foreach($projetos as $projeto)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 project-card" style="width: auto !important; margin: 0 !important; padding: 0 !important; max-width: 350px !important;" data-name="{{ strtolower($projeto->nome) }}" data-status="{{ $projeto->status }}">
                        <div class="card h-100 project-card-item">
                            <!-- Imagem de capa -->
                            <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                @if($projeto->fotos->count() > 0)
                                    <img src="{{ asset('storage/' . $projeto->fotos->first()->caminho_arquivo) }}"
                                         class="card-img-top h-100"
                                         style="object-fit: cover;"
                                         alt="{{ $projeto->nome }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                        <i class="fas fa-building fa-3x"></i>
                                    </div>
                                @endif

                                <!-- Status badge -->
                                <div class="position-absolute" style="top: 10px; left: 10px;">
                                    <span class="badge badge-{{
                                        $projeto->status == 'em_andamento' ? 'success' :
                                        ($projeto->status == 'planejamento' ? 'warning' :
                                        ($projeto->status == 'concluido' ? 'info' :
                                        ($projeto->status == 'pausado' ? 'secondary' : 'danger')))
                                    }} badge-lg">
                                        {{ ucfirst(str_replace('_', ' ', $projeto->status)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Métricas -->
                                <div class="metrics-container">
                                    <div class="metric-item">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file-alt text-primary mr-2"></i>
                                            <span class="font-weight-bold">{{ $projeto->atividades->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="metric-item">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-camera text-info mr-2"></i>
                                            <span class="font-weight-bold">{{ $projeto->fotos->count() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Título do projeto -->
                                <h5 class="card-title text-center">{{ $projeto->nome }}</h5>

                                <!-- Informações adicionais -->
                                <div class="text-center text-muted small">
                                    <div class="mb-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $projeto->cidade }}/{{ $projeto->estado }}
                                    </div>
                                    <div class="mb-1">
                                        <i class="fas fa-user"></i>
                                        {{ $projeto->responsavel->name ?? 'Sem responsável' }}
                                    </div>
                                    @if($projeto->data_fim_prevista)
                                    <div class="mb-1">
                                        <i class="fas fa-calendar"></i>
                                        {{ $projeto->data_fim_prevista->format('d/m/Y') }}
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100">
                                    <a href="{{ route('diario-obras.projetos.show', $projeto) }}"
                                       class="btn btn-primary btn-sm" title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('diario-obras.fotos.por-projeto', $projeto) }}"
                                       class="btn btn-info btn-sm" title="Fotos">
                                        <i class="fas fa-images"></i>
                                    </a>
                                    <a href="{{ route('diario-obras.projetos.edit', $projeto) }}"
                                       class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('diario-obras.relatorios.projeto', $projeto) }}"
                                       class="btn btn-secondary btn-sm" title="Relatório">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhum projeto cadastrado</h4>
                            <p class="text-muted">Comece criando seu primeiro projeto de obra.</p>
                            <a href="{{ route('diario-obras.projetos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Criar Primeiro Projeto
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Visualização em Tabela (oculta por padrão) -->
            <div id="tableView" class="row" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Projetos/Obras</h3>
                        </div>
                        <div class="card-body">
                            @if($projetos->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Cliente</th>
                                                <th>Endereço</th>
                                                <th>Status</th>
                                                <th>Prioridade</th>
                                                <th>Progresso</th>
                                                <th>Responsável</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($projetos as $projeto)
                                            <tr>
                                                <td>
                                                    <strong>{{ $projeto->nome }}</strong>
                                                    @if($projeto->descricao)
                                                        <br><small class="text-muted">{{ Str::limit($projeto->descricao, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $projeto->cliente }}</td>
                                                <td>
                                                    {{ $projeto->endereco }}<br>
                                                    <small class="text-muted">{{ $projeto->cidade }}/{{ $projeto->estado }}</small>
                                                </td>
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
                                                    <span class="badge badge-{{
                                                        $projeto->prioridade == 'urgente' ? 'danger' :
                                                        ($projeto->prioridade == 'alta' ? 'warning' :
                                                        ($projeto->prioridade == 'media' ? 'info' : 'secondary'))
                                                    }}">
                                                        {{ ucfirst($projeto->prioridade) }}
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
                                                    <div class="btn-group">
                                                        <a href="{{ route('diario-obras.projetos.show', $projeto) }}" class="btn btn-sm btn-primary" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('diario-obras.projetos.edit', $projeto) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('diario-obras.projetos.destroy', $projeto) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este projeto?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginação -->
            @if($projetos->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $projetos->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    /* Debug: borda SEMPRE visível na div container */
    #cardView {
        border: 3px solid red !important;
        background-color: rgba(255, 0, 0, 0.1) !important;
        min-height: 200px !important;
        padding: 20px !important;
    }

    /* Debug: borda SEMPRE visível nos cards individuais */
    .project-card {
        border: 2px solid blue !important;
        background-color: rgba(0, 0, 255, 0.1) !important;
    }

    /* Solução definitiva com CSS Grid - ajustada */
    #cardView {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
        gap: 20px !important;
        margin: 0 !important;
    }

    /* Resetar Bootstrap completamente */
    #cardView.row {
        display: grid !important;
        margin: 0 !important;
        padding: 20px !important;
    }

    #cardView .project-card {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: none !important;
        float: none !important;
    }

    /* CSS elegante para cards */
    .project-card {
        transition: all 0.3s ease-in-out;
    }

    .project-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .badge-lg {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    .gap-2 > * + * {
        margin-left: 0.5rem;
    }

    /* Transição suave entre visualizações */
    #cardView, #tableView {
        transition: opacity 0.3s ease-in-out;
    }

    /* Responsividade corrigida */
    @media (max-width: 576px) {
        #cardView {
            grid-template-columns: 1fr !important;
        }
    }

    @media (min-width: 577px) and (max-width: 768px) {
        #cardView {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (min-width: 769px) and (max-width: 1200px) {
        #cardView {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (min-width: 1201px) {
        #cardView {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }

    /* Forçar layout desktop por padrão */
    @media (min-width: 992px) {
        #cardView {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (min-width: 1400px) {
        #cardView {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variáveis globais para reutilização
    let projectCards = document.querySelectorAll('.project-card');
    let projectRows = document.querySelectorAll('.project-row');
    let currentSearchTerm = '';
    let currentStatus = '';

    // Função para atualizar elementos após mudança de visualização
    function updateElements() {
        projectCards = document.querySelectorAll('.project-card');
        projectRows = document.querySelectorAll('.project-row');
    }

    // Função para forçar reflow e realinhamento
    function forceReflow() {
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');

        // Forçar reflow do DOM
        if (cardView.style.display !== 'none') {
            cardView.style.display = 'none';
            cardView.offsetHeight; // Trigger reflow
            cardView.style.display = 'block';

            // Forçar reflow dos cards
            setTimeout(function() {
                const cards = cardView.querySelectorAll('.project-card');
                cards.forEach(function(card) {
                    card.style.display = 'block';
                    card.offsetHeight; // Trigger reflow
                });
            }, 10);
        }

        if (tableView.style.display !== 'none') {
            tableView.style.display = 'none';
            tableView.offsetHeight; // Trigger reflow
            tableView.style.display = 'block';
        }
    }

    // Alternância entre visualizações
    const cardViewBtn = document.getElementById('cardViewBtn');
    const tableViewBtn = document.getElementById('tableViewBtn');
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');

    cardViewBtn.addEventListener('click', function() {
        cardView.style.display = 'grid';
        tableView.style.display = 'none';
        cardViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');

        // Reaplicar filtros após mudança
        setTimeout(function() {
            updateElements();
            applyFilters();
        }, 100);
    });

    tableViewBtn.addEventListener('click', function() {
        // Adicionar classe de transição
        tableView.classList.add('view-transition');

        cardView.style.display = 'none';
        tableView.style.display = 'block';
        tableViewBtn.classList.add('active');
        cardViewBtn.classList.remove('active');

        // Forçar reflow profissional sem refresh
        setTimeout(function() {
            // Forçar reflow do container
            tableView.style.display = 'none';
            tableView.offsetHeight; // Trigger reflow
            tableView.style.display = 'block';

            // Remover classe de transição e mostrar
            tableView.classList.remove('view-transition');
            tableView.classList.add('show');

            // Atualizar elementos e reaplicar filtros
            updateElements();
            applyFilters();
        }, 50);
    });

    // Função combinada para aplicar todos os filtros
    function applyFilters() {
        projectCards.forEach(function(card) {
            const projectName = card.getAttribute('data-name');
            const projectStatus = card.getAttribute('data-status');

            let showCard = true;

            // Aplicar filtro de pesquisa
            if (currentSearchTerm && !projectName.includes(currentSearchTerm) && !projectStatus.includes(currentSearchTerm)) {
                showCard = false;
            }

            // Aplicar filtro de status
            if (currentStatus && projectStatus !== currentStatus) {
                showCard = false;
            }

            card.style.display = showCard ? 'block' : 'none';
        });

        // Se existir tabela, aplicar filtros também
        if (projectRows.length > 0) {
            projectRows.forEach(function(row) {
                const projectName = row.getAttribute('data-name');
                const projectStatus = row.getAttribute('data-status');

                let showRow = true;

                // Aplicar filtro de pesquisa
                if (currentSearchTerm && !projectName.includes(currentSearchTerm) && !projectStatus.includes(currentSearchTerm)) {
                    showRow = false;
                }

                // Aplicar filtro de status
                if (currentStatus && projectStatus !== currentStatus) {
                    showRow = false;
                }

                row.style.display = showRow ? 'table-row' : 'none';
            });
        }
    }

    // Funcionalidade de pesquisa
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', function() {
        currentSearchTerm = this.value.toLowerCase();
        applyFilters();
    });

    // Filtro de status (se existir)
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentStatus = this.value;
            applyFilters();
        });
    }

    // Inicializar filtros
    applyFilters();
});
</script>
@endpush
