@extends('layouts.admin')

@section('title', 'Funções - Sistema')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-briefcase text-warning"></i>
                        Funções
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Funções</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Header com controles -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="fas fa-briefcase text-warning"></i>
                        Funções ({{ $funcoes->total() }})
                    </h2>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <!-- Filtro de Categoria -->
                        <div class="col-md-4">
                            <select class="form-control form-control-sm" id="categoriaFilter">
                                <option value="">Todas as categorias</option>
                                <option value="construcao">Construção</option>
                                <option value="tecnica">Técnica</option>
                                <option value="supervisao">Supervisão</option>
                                <option value="administrativa">Administrativa</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>

                        <!-- Botões de Visualização -->
                        <div class="col-md-4">
                            <div class="btn-group" role="group">
                                <button type="button" id="cardViewBtn" class="btn btn-sm btn-secondary active">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button type="button" id="tableViewBtn" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Botão Adicionar -->
                        <div class="col-md-4">
                            <div class="dropdown">
                                <button class="btn btn-warning btn-sm dropdown-toggle" type="button" id="addDropdown" data-toggle="dropdown">
                                    <i class="fas fa-plus"></i> ADICIONAR
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('funcoes.create') }}">
                                        <i class="fas fa-briefcase"></i> Nova Função
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Pesquisa -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por nome da função...">
                    </div>
                </div>
            </div>

            <!-- Visualização em Cards -->
            <div id="cardView" class="row" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important; gap: 20px !important;">
                @if($funcoes->count() > 0)
                    @foreach($funcoes as $funcao)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 funcao-card" style="width: auto !important; margin: 0 !important; padding: 0 !important; max-width: 350px !important;"
                         data-name="{{ strtolower($funcao->nome) }}"
                         data-categoria="{{ strtolower($funcao->categoria) }}"
                         data-status="{{ $funcao->trashed() ? 'excluida' : ($funcao->ativo ? 'ativo' : 'inativo') }}">
                        <div class="card h-100 funcao-card-item">
                            <!-- Imagem de capa -->
                            <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                    <i class="fas fa-briefcase fa-3x"></i>
                                </div>
                                <!-- Status badge -->
                                <div class="position-absolute" style="top: 10px; left: 10px;">
                                    @if($funcao->trashed())
                                        <span class="badge badge-danger badge-lg">Excluída</span>
                                    @else
                                        <span class="badge badge-{{ $funcao->ativo ? 'success' : 'secondary' }} badge-lg">
                                            {{ $funcao->ativo ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Métricas -->
                                <div class="metrics-container">
                                    <div class="metric-item">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-users text-primary mr-2"></i>
                                            <span class="font-weight-bold">{{ $funcao->pessoas->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="metric-item">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-tag text-info mr-2"></i>
                                            <span class="font-weight-bold">{{ ucfirst($funcao->categoria) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Título da função -->
                                <h5 class="card-title text-center">{{ $funcao->nome }}</h5>

                                <!-- Informações adicionais -->
                                <div class="text-center text-muted small">
                                    @if($funcao->descricao)
                                    <div class="mb-1">
                                        <i class="fas fa-info-circle"></i>
                                        {{ Str::limit($funcao->descricao, 50) }}
                                    </div>
                                    @endif
                                    <div class="mb-1">
                                        <i class="fas fa-calendar"></i>
                                        Criada em {{ $funcao->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100">
                                    @if($funcao->trashed())
                                        <a href="{{ route('funcoes.show', $funcao) }}" class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('funcoes.restore', $funcao->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" title="Restaurar">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('funcoes.force-delete', $funcao->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir permanentemente">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('funcoes.show', $funcao) }}" class="btn btn-primary btn-sm" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('funcoes.edit', $funcao) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('funcoes.toggle-status', $funcao) }}" method="GET" class="d-inline">
                                            <button type="submit" class="btn btn-{{ $funcao->ativo ? 'secondary' : 'success' }} btn-sm" title="{{ $funcao->ativo ? 'Inativar' : 'Ativar' }}">
                                                <i class="fas fa-{{ $funcao->ativo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('funcoes.destroy', $funcao) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="no-cards">
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhuma função cadastrada</h4>
                            <p class="text-muted">Comece criando sua primeira função.</p>
                            <a href="{{ route('funcoes.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Criar Primeira Função
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Visualização em Tabela -->
            <div id="tableView" class="row" style="display: none;">
                <div class="col-12">
                    @if($funcoes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Descrição</th>
                                        <th>Status</th>
                                        <th>Pessoas</th>
                                        <th>Criada em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($funcoes as $funcao)
                                    <tr class="funcao-row"
                                        data-name="{{ strtolower($funcao->nome) }}"
                                        data-categoria="{{ strtolower($funcao->categoria) }}"
                                        data-status="{{ $funcao->trashed() ? 'excluida' : ($funcao->ativo ? 'ativo' : 'inativo') }}">
                                        <td>
                                            <strong>{{ $funcao->nome }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($funcao->categoria) }}</span>
                                        </td>
                                        <td>{{ $funcao->descricao ? Str::limit($funcao->descricao, 50) : '-' }}</td>
                                        <td>
                                            @if($funcao->trashed())
                                                <span class="badge badge-danger">Excluída</span>
                                            @else
                                                <span class="badge badge-{{ $funcao->ativo ? 'success' : 'secondary' }}">
                                                    {{ $funcao->ativo ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $funcao->pessoas->count() }}</td>
                                        <td>{{ $funcao->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if($funcao->trashed())
                                                    <a href="{{ route('funcoes.show', $funcao) }}" class="btn btn-sm btn-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('funcoes.restore', $funcao->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Restaurar">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('funcoes.show', $funcao) }}" class="btn btn-sm btn-primary" title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('funcoes.edit', $funcao) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('funcoes.toggle-status', $funcao) }}" method="GET" class="d-inline">
                                                        <button type="submit" class="btn btn-sm btn-{{ $funcao->ativo ? 'secondary' : 'success' }}" title="{{ $funcao->ativo ? 'Inativar' : 'Ativar' }}">
                                                            <i class="fas fa-{{ $funcao->ativo ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('funcoes.destroy', $funcao) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
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

            <!-- Paginação -->
            @if($funcoes->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $funcoes->links() }}
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
    .funcao-card {
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

    #cardView .funcao-card {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: none !important;
        float: none !important;
    }

    /* CSS elegante para cards */
    .funcao-card {
        transition: all 0.3s ease-in-out;
    }

    .funcao-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
    let funcaoCards = document.querySelectorAll('.funcao-card');
    let funcaoRows = document.querySelectorAll('.funcao-row');
    let currentSearchTerm = '';
    let currentCategoria = '';

    // Função para atualizar elementos após mudança de visualização
    function updateElements() {
        funcaoCards = document.querySelectorAll('.funcao-card');
        funcaoRows = document.querySelectorAll('.funcao-row');
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
        funcaoCards.forEach(function(card) {
            const funcaoName = card.getAttribute('data-name');
            const cardCategoria = card.getAttribute('data-categoria');

            let showCard = true;

            // Aplicar filtro de pesquisa
            if (currentSearchTerm && !funcaoName.includes(currentSearchTerm)) {
                showCard = false;
            }

            // Aplicar filtro de categoria
            if (currentCategoria && cardCategoria !== currentCategoria) {
                showCard = false;
            }

            card.style.display = showCard ? 'block' : 'none';
        });

        funcaoRows.forEach(function(row) {
            const funcaoName = row.getAttribute('data-name');
            const rowCategoria = row.getAttribute('data-categoria');

            let showRow = true;

            // Aplicar filtro de pesquisa
            if (currentSearchTerm && !funcaoName.includes(currentSearchTerm)) {
                showRow = false;
            }

            // Aplicar filtro de categoria
            if (currentCategoria && rowCategoria !== currentCategoria) {
                showRow = false;
            }

            row.style.display = showRow ? 'table-row' : 'none';
        });
    }

    // Funcionalidade de pesquisa
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', function() {
        currentSearchTerm = this.value.toLowerCase();
        applyFilters();
    });

    // Filtro de categoria
    const categoriaFilter = document.getElementById('categoriaFilter');

    categoriaFilter.addEventListener('change', function() {
        currentCategoria = this.value;
        applyFilters();
    });

    // Inicializar filtros
    applyFilters();
});
</script>
@endpush