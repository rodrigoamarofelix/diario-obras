@extends('layouts.admin')

@section('title', 'Fotos do Projeto - Diário de Obras')

@push('styles')
<style>
    .project-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .project-title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .project-meta {
        display: flex;
        gap: 30px;
        margin-top: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .gallery-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .photo-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }

    .photo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .photo-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f8f9fa;
    }

    .photo-info {
        padding: 15px;
    }

    .photo-title {
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }

    .photo-meta {
        font-size: 12px;
        color: #666;
        margin-bottom: 10px;
    }

    .photo-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 10px;
    }

    .tag {
        background: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
    }

    .photo-actions {
        display: flex;
        gap: 5px;
        justify-content: flex-end;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .category-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: bold;
        color: white;
    }

    .category-antes { background: #28a745; }
    .category-progresso { background: #007bff; }
    .category-problema { background: #dc3545; }
    .category-solucao { background: #ffc107; color: #333; }
    .category-final { background: #6f42c1; }
    .category-geral { background: #6c757d; }

    .gps-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px;
        border-radius: 50%;
        font-size: 12px;
    }

    .filters {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .view-toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .view-btn {
        padding: 10px 20px;
        border: 2px solid #007bff;
        background: white;
        color: #007bff;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .view-btn.active {
        background: #007bff;
        color: white;
    }

    .view-btn:hover {
        background: #007bff;
        color: white;
    }

    .table-view {
        display: none;
    }

    .table-view.active {
        display: block;
    }

    .gallery-view.active {
        display: grid;
    }

    .gallery-view {
        display: none;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }

    .stat-number {
        font-size: 32px;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
    }

    .breadcrumb-custom {
        background: none;
        padding: 0;
        margin-bottom: 20px;
    }

    .breadcrumb-custom .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
    }

    .breadcrumb-custom .breadcrumb-item.active {
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-images text-primary"></i>
                        Fotos do Projeto
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb breadcrumb-custom float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.projetos.index') }}">Projetos</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.projetos.show', $projeto) }}">{{ $projeto->nome }}</a></li>
                        <li class="breadcrumb-item active">Fotos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Project Header -->
            <div class="project-header">
                <div class="project-title">
                    <i class="fas fa-building"></i>
                    {{ $projeto->nome }}
                </div>
                <p class="mb-0">{{ $projeto->descricao }}</p>

                <div class="project-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>Início: {{ $projeto->data_inicio ? $projeto->data_inicio->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Previsão: {{ $projeto->data_fim ? $projeto->data_fim->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $projeto->endereco }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>{{ ucfirst($projeto->status) }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number">{{ $fotos->total() }}</div>
                    <div class="stat-label">Total de Fotos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $fotos->where('categoria', 'antes')->count() }}</div>
                    <div class="stat-label">Fotos Antes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $fotos->where('categoria', 'progresso')->count() }}</div>
                    <div class="stat-label">Fotos Progresso</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $fotos->where('categoria', 'final')->count() }}</div>
                    <div class="stat-label">Fotos Final</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $fotos->whereNotNull('latitude')->count() }}</div>
                    <div class="stat-label">Com GPS</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('diario-obras.fotos.create', ['projeto_id' => $projeto->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Adicionar Fotos
                    </a>
                    <a href="{{ route('diario-obras.projetos.show', $projeto) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao Projeto
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <input type="text" class="form-control" id="search" placeholder="Título, descrição, tags...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select class="form-control" id="categoria">
                                <option value="">Todas</option>
                                <option value="antes">Antes</option>
                                <option value="progresso">Progresso</option>
                                <option value="problema">Problema</option>
                                <option value="solucao">Solução</option>
                                <option value="final">Final</option>
                                <option value="geral">Geral</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status">
                                <option value="">Todos</option>
                                <option value="aprovada">Aprovadas</option>
                                <option value="pendente">Pendentes</option>
                                <option value="excluida">Excluídas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="view-toggle">
                                <button class="view-btn active" data-view="gallery">
                                    <i class="fas fa-th"></i> Galeria
                                </button>
                                <button class="view-btn" data-view="table">
                                    <i class="fas fa-list"></i> Lista
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery View -->
            <div id="galleryView" class="gallery-view active">
                <div class="gallery-container" id="galleryContainer">
                    @forelse($fotos as $foto)
                    <div class="photo-card"
                         data-name="{{ strtolower($foto->titulo) }}"
                         data-categoria="{{ $foto->categoria }}"
                         data-status="{{ $foto->trashed() ? 'excluida' : ($foto->aprovada ? 'aprovada' : 'pendente') }}"
                         data-tags="{{ strtolower(implode(' ', $foto->tags ?? [])) }}">

                        <div class="position-relative">
                            <img src="{{ $foto->url_arquivo }}"
                                 alt="{{ $foto->titulo }}"
                                 class="photo-image"
                                 onclick="openModal('{{ $foto->id }}')">

                            <div class="category-badge category-{{ $foto->categoria }}">
                                {{ ucfirst($foto->categoria) }}
                            </div>

                            @if($foto->temGeolocalizacao())
                                <div class="gps-indicator" title="Com GPS">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            @endif

                            @if($foto->trashed())
                                <div class="category-badge" style="background: #6c757d; top: 40px;">
                                    Excluída
                                </div>
                            @endif
                        </div>

                        <div class="photo-info">
                            <div class="photo-title">{{ $foto->titulo }}</div>
                            <div class="photo-meta">
                                <i class="fas fa-calendar"></i> {{ $foto->data_captura ? $foto->data_captura->format('d/m/Y H:i') : 'N/A' }}
                                <br>
                                <i class="fas fa-user"></i> {{ $foto->usuario->name }}
                                <br>
                                <i class="fas fa-weight"></i> {{ $foto->tamanho_formatado }}
                            </div>

                            @if($foto->tags)
                                <div class="photo-tags">
                                    @foreach($foto->tags as $tag)
                                        <span class="tag">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="photo-actions">
                                <a href="{{ route('diario-obras.fotos.show', $foto) }}" class="btn btn-sm btn-info" title="Ver detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$foto->trashed())
                                    <a href="{{ route('diario-obras.fotos.edit', $foto) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('diario-obras.fotos.destroy', $foto) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta foto?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('diario-obras.fotos.restore', $foto->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Restaurar" onclick="return confirm('Tem certeza que deseja restaurar esta foto?')">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    @if(auth()->user()->can('manage-users'))
                                        <form action="{{ route('diario-obras.fotos.force-delete', $foto->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir Permanentemente" onclick="return confirm('ATENÇÃO: Esta ação é irreversível! Tem certeza que deseja excluir permanentemente esta foto?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhuma foto encontrada</h4>
                        <p class="text-muted">Comece adicionando fotos a este projeto.</p>
                        <a href="{{ route('diario-obras.fotos.create', ['projeto_id' => $projeto->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Adicionar Primeira Foto
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Table View -->
            <div id="tableView" class="table-view">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Título</th>
                                        <th>Categoria</th>
                                        <th>Data</th>
                                        <th>GPS</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fotos as $foto)
                                    <tr class="{{ $foto->trashed() ? 'table-secondary' : '' }}">
                                        <td>
                                            <img src="{{ $foto->url_arquivo }}"
                                                 alt="{{ $foto->titulo }}"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        </td>
                                        <td>{{ $foto->titulo }}</td>
                                        <td>
                                            <span class="badge badge-{{ $foto->categoria === 'antes' ? 'success' : ($foto->categoria === 'problema' ? 'danger' : 'primary') }}">
                                                {{ ucfirst($foto->categoria) }}
                                            </span>
                                        </td>
                                        <td>{{ $foto->data_captura ? $foto->data_captura->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            @if($foto->temGeolocalizacao())
                                                <i class="fas fa-map-marker-alt text-success"></i>
                                            @else
                                                <i class="fas fa-times text-muted"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if($foto->trashed())
                                                <span class="badge badge-secondary">Excluída</span>
                                            @elseif($foto->aprovada)
                                                <span class="badge badge-success">Aprovada</span>
                                            @else
                                                <span class="badge badge-warning">Pendente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('diario-obras.fotos.show', $foto) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$foto->trashed())
                                                    <a href="{{ route('diario-obras.fotos.edit', $foto) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('diario-obras.fotos.destroy', $foto) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta foto?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('diario-obras.fotos.restore', $foto->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Tem certeza que deseja restaurar esta foto?')">
                                                            <i class="fas fa-undo"></i>
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
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $fotos->links() }}
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// View Toggle
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const view = this.dataset.view;

        // Update buttons
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Update views
        document.getElementById('galleryView').classList.toggle('active', view === 'gallery');
        document.getElementById('tableView').classList.toggle('active', view === 'table');
    });
});

// Filter Functions
function filterPhotos() {
    const search = document.getElementById('search').value.toLowerCase();
    const categoria = document.getElementById('categoria').value;
    const status = document.getElementById('status').value;

    const cards = document.querySelectorAll('.photo-card');
    const rows = document.querySelectorAll('tbody tr');

    cards.forEach(card => {
        const name = card.dataset.name;
        const cardCategoria = card.dataset.categoria;
        const cardStatus = card.dataset.status;
        const tags = card.dataset.tags;

        const matchesSearch = !search || name.includes(search) || tags.includes(search);
        const matchesCategoria = !categoria || cardCategoria === categoria;
        const matchesStatus = !status || cardStatus === status;

        if (matchesSearch && matchesCategoria && matchesStatus) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    rows.forEach(row => {
        // Similar logic for table rows
        row.style.display = 'table-row';
    });
}

// Event Listeners
document.getElementById('search').addEventListener('input', filterPhotos);
document.getElementById('categoria').addEventListener('change', filterPhotos);
document.getElementById('status').addEventListener('change', filterPhotos);
</script>
@endpush
