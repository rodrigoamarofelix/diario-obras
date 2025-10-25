@extends('layouts.admin')

@section('title', 'Empresas - Sistema')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-building text-primary"></i>
                        Empresas
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Empresas</li>
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
                        <i class="fas fa-building text-primary"></i>
                        Empresas ({{ $empresas->total() }})
                    </h2>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <!-- Filtro de Status -->
                        <div class="col-md-4">
                            <select class="form-control form-control-sm" id="statusFilter">
                                <option value="">Todas as status</option>
                                <option value="ativo">Ativas</option>
                                <option value="inativo">Inativas</option>
                                <option value="excluida">Excluídas</option>
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
                                    <a class="dropdown-item" href="{{ route('empresas.create') }}">
                                        <i class="fas fa-building"></i> Nova Empresa
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por nome ou CNPJ...">
                    </div>
                </div>
            </div>

            <!-- Visualização em Cards -->
            <div id="cardView" class="row" style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important; gap: 20px !important;">
                @if($empresas->count() > 0)
                    @foreach($empresas as $empresa)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 empresa-card" style="width: auto !important; margin: 0 !important; padding: 0 !important; max-width: 350px !important;"
                         data-name="{{ strtolower($empresa->nome) }}"
                         data-cnpj="{{ strtolower($empresa->cnpj) }}"
                         data-status="{{ $empresa->trashed() ? 'excluida' : ($empresa->ativo ? 'ativo' : 'inativo') }}">
                        <div class="card h-100">
                            <!-- Imagem de capa -->
                            <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                    <i class="fas fa-building fa-3x"></i>
                                </div>
                                <!-- Status badge -->
                                <div class="position-absolute" style="top: 10px; left: 10px;">
                                    @if($empresa->trashed())
                                        <span class="badge badge-danger badge-lg">Excluída</span>
                                    @else
                                        <span class="badge badge-{{ $empresa->ativo ? 'success' : 'secondary' }} badge-lg">
                                            {{ $empresa->ativo ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- Métricas -->
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file-alt text-primary mr-2"></i>
                                            <span class="font-weight-bold">{{ $empresa->projetos->count() }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-users text-info mr-2"></i>
                                            <span class="font-weight-bold">0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Título da empresa -->
                                <h5 class="card-title text-center">{{ $empresa->nome }}</h5>

                                <!-- Informações adicionais -->
                                <div class="text-center text-muted small">
                                    <div class="mb-1">
                                        <i class="fas fa-id-card"></i>
                                        {{ $empresa->cnpj_formatado }}
                                    </div>
                                    @if($empresa->email)
                                    <div class="mb-1">
                                        <i class="fas fa-envelope"></i>
                                        {{ Str::limit($empresa->email, 20) }}
                                    </div>
                                    @endif
                                    <div class="mb-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $empresa->cidade }}/{{ $empresa->estado }}
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100">
                                    @if($empresa->trashed())
                                        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-info btn-sm" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('empresas.restore', $empresa->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" title="Restaurar">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('empresas.force-delete', $empresa->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir permanentemente">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-primary btn-sm" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('empresas.toggle-status', $empresa) }}" method="GET" class="d-inline">
                                            <button type="submit" class="btn btn-{{ $empresa->ativo ? 'secondary' : 'success' }} btn-sm" title="{{ $empresa->ativo ? 'Inativar' : 'Ativar' }}">
                                                <i class="fas fa-{{ $empresa->ativo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="d-inline">
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
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhuma empresa cadastrada</h4>
                            <p class="text-muted">Comece cadastrando sua primeira empresa.</p>
                            <a href="{{ route('empresas.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Cadastrar Primeira Empresa
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Visualização em Tabela -->
            <div id="tableView" class="row" style="display: none;">
                <div class="col-12">
                    @if($empresas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>CNPJ</th>
                                        <th>Email</th>
                                        <th>Cidade/Estado</th>
                                        <th>Status</th>
                                        <th>Projetos</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($empresas as $empresa)
                                    <tr class="empresa-row"
                                        data-name="{{ strtolower($empresa->nome) }}"
                                        data-cnpj="{{ strtolower($empresa->cnpj) }}"
                                        data-status="{{ $empresa->trashed() ? 'excluida' : ($empresa->ativo ? 'ativo' : 'inativo') }}">
                                        <td>
                                            <strong>{{ $empresa->nome }}</strong>
                                            @if($empresa->razao_social)
                                                <br><small class="text-muted">{{ $empresa->razao_social }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $empresa->cnpj_formatado }}</td>
                                        <td>{{ $empresa->email ?? '-' }}</td>
                                        <td>{{ $empresa->cidade }}/{{ $empresa->estado }}</td>
                                        <td>
                                            @if($empresa->trashed())
                                                <span class="badge badge-danger">Excluída</span>
                                            @else
                                                <span class="badge badge-{{ $empresa->ativo ? 'success' : 'secondary' }}">
                                                    {{ $empresa->ativo ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $empresa->projetos->count() }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if($empresa->trashed())
                                                    <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-sm btn-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('empresas.restore', $empresa->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Restaurar">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-sm btn-primary" title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('empresas.toggle-status', $empresa) }}" method="GET" class="d-inline">
                                                        <button type="submit" class="btn btn-sm btn-{{ $empresa->ativo ? 'secondary' : 'success' }}" title="{{ $empresa->ativo ? 'Inativar' : 'Ativar' }}">
                                                            <i class="fas fa-{{ $empresa->ativo ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="d-inline">
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
            @if($empresas->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $empresas->links() }}
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
    .empresa-card {
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

    #cardView .empresa-card {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        flex: none !important;
        float: none !important;
    }

    /* CSS elegante para cards */
    .empresa-card {
        transition: all 0.3s ease-in-out;
    }

    .empresa-card:hover {
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
    let empresaCards = document.querySelectorAll('.empresa-card');
    let empresaRows = document.querySelectorAll('.empresa-row');
    let currentSearchTerm = '';
    let currentStatus = '';

    // Função para atualizar elementos após mudança de visualização
    function updateElements() {
        empresaCards = document.querySelectorAll('.empresa-card');
        empresaRows = document.querySelectorAll('.empresa-row');
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
                const cards = cardView.querySelectorAll('.empresa-card');
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
        empresaCards.forEach(function(card) {
            const empresaName = card.getAttribute('data-name');
            const empresaCnpj = card.getAttribute('data-cnpj');
            const cardStatus = card.getAttribute('data-status');

            let showCard = true;

            // Aplicar filtro de pesquisa
            if (currentSearchTerm && !empresaName.includes(currentSearchTerm) && !empresaCnpj.includes(currentSearchTerm)) {
                showCard = false;
            }

            // Aplicar filtro de status
            if (currentStatus && cardStatus !== currentStatus) {
                showCard = false;
            }

            card.style.display = showCard ? 'block' : 'none';
        });

        empresaRows.forEach(function(row) {
            const empresaName = row.getAttribute('data-name');
            const empresaCnpj = row.getAttribute('data-cnpj');
            const rowStatus = row.getAttribute('data-status');

            let showRow = true;

            // Aplicar filtro de pesquisa
            if (currentSearchTerm && !empresaName.includes(currentSearchTerm) && !empresaCnpj.includes(currentSearchTerm)) {
                showRow = false;
            }

            // Aplicar filtro de status
            if (currentStatus && rowStatus !== currentStatus) {
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

    // Filtro de status
    const statusFilter = document.getElementById('statusFilter');

    statusFilter.addEventListener('change', function() {
        currentStatus = this.value;
        applyFilters();
    });

    // Inicializar filtros
    applyFilters();
});
</script>
@endpush