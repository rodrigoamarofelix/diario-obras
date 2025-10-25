@extends('layouts.admin')

@section('title', 'Detalhes da Foto - Diário de Obras')

@push('styles')
<style>
    .photo-detail-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }

    .photo-main {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .photo-image {
        width: 100%;
        max-height: 600px;
        object-fit: contain;
        background: #f8f9fa;
    }

    .photo-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .info-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: bold;
        color: #333;
    }

    .info-value {
        color: #666;
        text-align: right;
    }

    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }

    .tag {
        background: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }

    .category-badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: bold;
        color: white;
        display: inline-block;
    }

    .category-antes { background: #28a745; }
    .category-progresso { background: #007bff; }
    .category-problema { background: #dc3545; }
    .category-solucao { background: #ffc107; color: #333; }
    .category-final { background: #6f42c1; }
    .category-geral { background: #6c757d; }

    .gps-info {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .gps-info h6 {
        color: #1976d2;
        margin-bottom: 10px;
    }

    .map-container {
        height: 200px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        margin-top: 10px;
    }

    .exif-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .exif-item {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .exif-value {
        font-size: 18px;
        font-weight: bold;
        color: #007bff;
    }

    .exif-label {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-action {
        flex: 1;
        padding: 12px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-aprovada {
        background: #d4edda;
        color: #155724;
    }

    .status-pendente {
        background: #fff3cd;
        color: #856404;
    }

    .status-excluida {
        background: #f8d7da;
        color: #721c24;
    }

    .photo-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .nav-btn {
        padding: 10px 20px;
        border: 2px solid #007bff;
        background: white;
        color: #007bff;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .nav-btn:hover {
        background: #007bff;
        color: white;
    }

    .nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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
                        <i class="fas fa-image text-primary"></i>
                        Detalhes da Foto
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.fotos.index') }}">Fotos</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Navigation -->
            <div class="photo-navigation">
                <a href="{{ route('diario-obras.fotos.index') }}" class="nav-btn">
                    <i class="fas fa-arrow-left"></i>
                    Voltar para Galeria
                </a>

                <div class="action-buttons">
                    @if(!$foto->trashed())
                        <a href="{{ route('diario-obras.fotos.edit', $foto) }}" class="btn-action btn btn-warning">
                            <i class="fas fa-edit"></i>
                            Editar
                        </a>
                        <form action="{{ route('diario-obras.fotos.destroy', $foto) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta foto?')">
                                <i class="fas fa-trash"></i>
                                Excluir
                            </button>
                        </form>
                    @else
                        <form action="{{ route('diario-obras.fotos.restore', $foto->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-action btn btn-success" onclick="return confirm('Tem certeza que deseja restaurar esta foto?')">
                                <i class="fas fa-undo"></i>
                                Restaurar
                            </button>
                        </form>
                        @if(auth()->user()->can('manage-users'))
                            <form action="{{ route('diario-obras.fotos.force-delete', $foto->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn btn-danger" onclick="return confirm('ATENÇÃO: Esta ação é irreversível! Tem certeza que deseja excluir permanentemente esta foto?')">
                                    <i class="fas fa-trash-alt"></i>
                                    Excluir Permanentemente
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Photo Details -->
            <div class="photo-detail-container">
                <!-- Main Photo -->
                <div class="photo-main">
                    <img src="{{ $foto->url_arquivo }}"
                         alt="{{ $foto->titulo }}"
                         class="photo-image">
                </div>

                <!-- Sidebar -->
                <div class="photo-sidebar">
                    <!-- Basic Info -->
                    <div class="info-card">
                        <h5><i class="fas fa-info-circle"></i> Informações Básicas</h5>

                        <div class="info-item">
                            <span class="info-label">Título:</span>
                            <span class="info-value">{{ $foto->titulo }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Categoria:</span>
                            <span class="info-value">
                                <span class="category-badge category-{{ $foto->categoria }}">
                                    {{ ucfirst($foto->categoria) }}
                                </span>
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                @if($foto->trashed())
                                    <span class="status-indicator status-excluida">
                                        <i class="fas fa-trash"></i>
                                        Excluída
                                    </span>
                                @elseif($foto->aprovada)
                                    <span class="status-indicator status-aprovada">
                                        <i class="fas fa-check"></i>
                                        Aprovada
                                    </span>
                                @else
                                    <span class="status-indicator status-pendente">
                                        <i class="fas fa-clock"></i>
                                        Pendente
                                    </span>
                                @endif
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Projeto:</span>
                            <span class="info-value">{{ $foto->projeto->nome }}</span>
                        </div>

                        @if($foto->atividade)
                        <div class="info-item">
                            <span class="info-label">Atividade:</span>
                            <span class="info-value">{{ $foto->atividade->nome }}</span>
                        </div>
                        @endif

                        @if($foto->equipe)
                        <div class="info-item">
                            <span class="info-label">Equipe:</span>
                            <span class="info-value">{{ $foto->equipe->pessoa->nome ?? 'N/A' }}</span>
                        </div>
                        @endif

                        <div class="info-item">
                            <span class="info-label">Fotógrafo:</span>
                            <span class="info-value">{{ $foto->usuario->name }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Data de Captura:</span>
                            <span class="info-value">{{ $foto->data_captura ? $foto->data_captura->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Data de Upload:</span>
                            <span class="info-value">{{ $foto->data_upload ? $foto->data_upload->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Tamanho:</span>
                            <span class="info-value">{{ $foto->tamanho_formatado }}</span>
                        </div>

                        @if($foto->tags)
                        <div class="info-item">
                            <span class="info-label">Tags:</span>
                            <div class="tag-list">
                                @foreach($foto->tags as $tag)
                                    <span class="tag">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($foto->descricao)
                        <div class="info-item">
                            <span class="info-label">Descrição:</span>
                            <span class="info-value">{{ $foto->descricao }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- GPS Info -->
                    @if($foto->temGeolocalizacao())
                    <div class="info-card">
                        <h5><i class="fas fa-map-marker-alt"></i> Localização GPS</h5>

                        <div class="gps-info">
                            <div class="info-item">
                                <span class="info-label">Latitude:</span>
                                <span class="info-value">{{ $foto->latitude }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Longitude:</span>
                                <span class="info-value">{{ $foto->longitude }}</span>
                            </div>

                            @if($foto->altitude)
                            <div class="info-item">
                                <span class="info-label">Altitude:</span>
                                <span class="info-value">{{ $foto->altitude }}m</span>
                            </div>
                            @endif

                            @if($foto->precisao)
                            <div class="info-item">
                                <span class="info-label">Precisão:</span>
                                <span class="info-value">{{ $foto->precisao }}m</span>
                            </div>
                            @endif

                            <div class="map-container">
                                <i class="fas fa-map fa-2x"></i>
                                <div style="margin-left: 10px;">
                                    <div>Mapa interativo</div>
                                    <small>Clique para abrir no Google Maps</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- EXIF Data -->
                    @if($foto->camera_marca || $foto->camera_modelo || $foto->aperture || $foto->shutter_speed || $foto->iso || $foto->focal_length)
                    <div class="info-card">
                        <h5><i class="fas fa-camera"></i> Dados EXIF</h5>

                        <div class="exif-grid">
                            @if($foto->camera_marca)
                            <div class="exif-item">
                                <div class="exif-value">{{ $foto->camera_marca }}</div>
                                <div class="exif-label">Marca</div>
                            </div>
                            @endif

                            @if($foto->camera_modelo)
                            <div class="exif-item">
                                <div class="exif-value">{{ $foto->camera_modelo }}</div>
                                <div class="exif-label">Modelo</div>
                            </div>
                            @endif

                            @if($foto->aperture)
                            <div class="exif-item">
                                <div class="exif-value">f/{{ $foto->aperture }}</div>
                                <div class="exif-label">Abertura</div>
                            </div>
                            @endif

                            @if($foto->shutter_speed)
                            <div class="exif-item">
                                <div class="exif-value">{{ $foto->shutter_speed }}s</div>
                                <div class="exif-label">Velocidade</div>
                            </div>
                            @endif

                            @if($foto->iso)
                            <div class="exif-item">
                                <div class="exif-value">ISO {{ $foto->iso }}</div>
                                <div class="exif-label">ISO</div>
                            </div>
                            @endif

                            @if($foto->focal_length)
                            <div class="exif-item">
                                <div class="exif-value">{{ $foto->focal_length }}mm</div>
                                <div class="exif-label">Distância Focal</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Map functionality
document.querySelector('.map-container').addEventListener('click', function() {
    if ({{ $foto->temGeolocalizacao() ? 'true' : 'false' }}) {
        const lat = {{ $foto->latitude ?? 'null' }};
        const lng = {{ $foto->longitude ?? 'null' }};
        const url = `https://www.google.com/maps?q=${lat},${lng}`;
        window.open(url, '_blank');
    }
});
</script>
@endpush