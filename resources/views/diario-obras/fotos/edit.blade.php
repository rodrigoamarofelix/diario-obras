@extends('layouts.admin')

@section('title', 'Editar Foto - Diário de Obras')

@push('styles')
<style>
    .photo-preview {
        max-width: 300px;
        max-height: 300px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .tag-input {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        min-height: 50px;
    }

    .tag-item {
        background: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .tag-remove {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-weight: bold;
    }

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

    .file-upload-area {
        border: 2px dashed #007bff;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #0056b3;
        background: #e3f2fd;
    }

    .file-upload-area.dragover {
        border-color: #28a745;
        background: #d4edda;
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
                        <i class="fas fa-edit text-warning"></i>
                        Editar Foto
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.fotos.index') }}">Fotos</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.fotos.show', $foto) }}">Detalhes</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('diario-obras.fotos.update', $foto) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Photo Preview -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-image"></i>
                                    Foto Atual
                                </h3>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ $foto->url_arquivo }}"
                                     alt="{{ $foto->titulo }}"
                                     class="photo-preview mb-3">

                                <!-- New Photo Upload -->
                                <div class="file-upload-area" id="fileUploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                    <p>Clique para trocar a foto</p>
                                    <small class="text-muted">JPG, PNG, GIF (máx. 20MB)</small>
                                    <input type="file" id="newPhoto" name="arquivo" accept="image/*" style="display: none;">
                                </div>

                                <div id="newPhotoPreview" style="display: none;">
                                    <img id="previewImage" src="" alt="Preview" class="photo-preview mb-3">
                                    <p class="text-success">
                                        <i class="fas fa-check"></i>
                                        Nova foto selecionada
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- GPS Info -->
                        @if($foto->temGeolocalizacao())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Localização GPS
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="gps-info">
                                    <h6>Coordenadas Atuais</h6>
                                    <p><strong>Latitude:</strong> {{ $foto->latitude }}</p>
                                    <p><strong>Longitude:</strong> {{ $foto->longitude }}</p>
                                    @if($foto->altitude)
                                        <p><strong>Altitude:</strong> {{ $foto->altitude }}m</p>
                                    @endif
                                    @if($foto->precisao)
                                        <p><strong>Precisão:</strong> {{ $foto->precisao }}m</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Form Fields -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-edit"></i>
                                    Informações da Foto
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Título -->
                                <div class="form-group">
                                    <label for="titulo">Título</label>
                                    <input type="text"
                                           class="form-control @error('titulo') is-invalid @enderror"
                                           id="titulo"
                                           name="titulo"
                                           value="{{ old('titulo', $foto->titulo) }}"
                                           placeholder="Digite o título da foto">
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descrição -->
                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                              id="descricao"
                                              name="descricao"
                                              rows="3"
                                              placeholder="Descreva a foto...">{{ old('descricao', $foto->descricao) }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Projeto -->
                                <div class="form-group">
                                    <label for="projeto_id">Projeto *</label>
                                    <select class="form-control @error('projeto_id') is-invalid @enderror"
                                            id="projeto_id"
                                            name="projeto_id"
                                            required>
                                        <option value="">Selecione um projeto</option>
                                        @foreach($projetos as $projeto)
                                            <option value="{{ $projeto->id }}"
                                                    {{ old('projeto_id', $foto->projeto_id) == $projeto->id ? 'selected' : '' }}>
                                                {{ $projeto->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('projeto_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Atividade -->
                                <div class="form-group">
                                    <label for="atividade_id">Atividade</label>
                                    <select class="form-control @error('atividade_id') is-invalid @enderror"
                                            id="atividade_id"
                                            name="atividade_id">
                                        <option value="">Selecione uma atividade</option>
                                        @foreach($atividades as $atividade)
                                            <option value="{{ $atividade->id }}"
                                                    {{ old('atividade_id', $foto->atividade_id) == $atividade->id ? 'selected' : '' }}>
                                                {{ $atividade->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('atividade_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Equipe -->
                                <div class="form-group">
                                    <label for="equipe_id">Equipe</label>
                                    <select class="form-control @error('equipe_id') is-invalid @enderror"
                                            id="equipe_id"
                                            name="equipe_id">
                                        <option value="">Selecione uma equipe</option>
                                        @foreach($equipes as $equipe)
                                            <option value="{{ $equipe->id }}"
                                                    {{ old('equipe_id', $foto->equipe_id) == $equipe->id ? 'selected' : '' }}>
                                                {{ $equipe->pessoa->nome ?? 'N/A' }} - {{ $equipe->projeto->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('equipe_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Categoria -->
                                <div class="form-group">
                                    <label for="categoria">Categoria *</label>
                                    <select class="form-control @error('categoria') is-invalid @enderror"
                                            id="categoria"
                                            name="categoria"
                                            required>
                                        <option value="">Selecione uma categoria</option>
                                        <option value="antes" {{ old('categoria', $foto->categoria) == 'antes' ? 'selected' : '' }}>Antes da Obra</option>
                                        <option value="progresso" {{ old('categoria', $foto->categoria) == 'progresso' ? 'selected' : '' }}>Progresso</option>
                                        <option value="problema" {{ old('categoria', $foto->categoria) == 'problema' ? 'selected' : '' }}>Problema</option>
                                        <option value="solucao" {{ old('categoria', $foto->categoria) == 'solucao' ? 'selected' : '' }}>Solução</option>
                                        <option value="final" {{ old('categoria', $foto->categoria) == 'final' ? 'selected' : '' }}>Resultado Final</option>
                                        <option value="geral" {{ old('categoria', $foto->categoria) == 'geral' ? 'selected' : '' }}>Geral</option>
                                    </select>
                                    @error('categoria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tags -->
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <div class="tag-input" id="tagContainer">
                                        <!-- Tags will be populated here -->
                                    </div>
                                    <input type="text" id="tagInput" placeholder="Digite uma tag e pressione Enter" style="border: none; outline: none; flex: 1;">
                                    <small class="text-muted">Tags automáticas serão adicionadas baseadas na categoria</small>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="aprovada"
                                               name="aprovada"
                                               value="1"
                                               {{ old('aprovada', $foto->aprovada) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="aprovada">
                                            Foto Aprovada
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               id="publica"
                                               name="publica"
                                               value="1"
                                               {{ old('publica', $foto->publica) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="publica">
                                            Foto Pública
                                        </label>
                                    </div>
                                </div>

                                <!-- GPS Coordinates -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="number"
                                                   step="any"
                                                   class="form-control @error('latitude') is-invalid @enderror"
                                                   id="latitude"
                                                   name="latitude"
                                                   value="{{ old('latitude', $foto->latitude) }}"
                                                   placeholder="Ex: -23.550520">
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude">Longitude</label>
                                            <input type="number"
                                                   step="any"
                                                   class="form-control @error('longitude') is-invalid @enderror"
                                                   id="longitude"
                                                   name="longitude"
                                                   value="{{ old('longitude', $foto->longitude) }}"
                                                   placeholder="Ex: -46.633308">
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="altitude">Altitude (metros)</label>
                                            <input type="number"
                                                   step="any"
                                                   class="form-control @error('altitude') is-invalid @enderror"
                                                   id="altitude"
                                                   name="altitude"
                                                   value="{{ old('altitude', $foto->altitude) }}"
                                                   placeholder="Ex: 760">
                                            @error('altitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="precisao">Precisão (metros)</label>
                                            <input type="number"
                                                   step="any"
                                                   class="form-control @error('precisao') is-invalid @enderror"
                                                   id="precisao"
                                                   name="precisao"
                                                   value="{{ old('precisao', $foto->precisao) }}"
                                                   placeholder="Ex: 5">
                                            @error('precisao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Salvar Alterações
                                    </button>
                                    <a href="{{ route('diario-obras.fotos.show', $foto) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
let tags = @json($foto->tags ?? []);

// Initialize tags display
function updateTagDisplay() {
    const container = document.getElementById('tagContainer');
    container.innerHTML = '';

    tags.forEach(tag => {
        const tagElement = document.createElement('span');
        tagElement.className = 'tag-item';
        tagElement.innerHTML = `
            ${tag}
            <button type="button" class="tag-remove" onclick="removeTag('${tag}')">×</button>
        `;
        container.appendChild(tagElement);
    });

    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'tagInput';
    input.placeholder = 'Digite uma tag e pressione Enter';
    input.style.cssText = 'border: none; outline: none; flex: 1;';
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addTag(this.value);
            this.value = '';
        }
    });
    container.appendChild(input);
}

function addTag(tag) {
    if (tag.trim() && !tags.includes(tag.trim())) {
        tags.push(tag.trim());
        updateTagDisplay();
    }
}

function removeTag(tag) {
    tags = tags.filter(t => t !== tag);
    updateTagDisplay();
}

// File upload handling
document.getElementById('fileUploadArea').addEventListener('click', function() {
    document.getElementById('newPhoto').click();
});

document.getElementById('newPhoto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('newPhotoPreview').style.display = 'block';
            document.getElementById('fileUploadArea').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});

// Auto-add tags based on category
document.getElementById('categoria').addEventListener('change', function() {
    const categoria = this.value;
    const autoTags = {
        'antes': ['antes', 'inicio', 'baseline'],
        'progresso': ['progresso', 'andamento', 'desenvolvimento'],
        'problema': ['problema', 'issue', 'defeito'],
        'solucao': ['solucao', 'correcao', 'reparo'],
        'final': ['final', 'concluido', 'resultado'],
        'geral': ['obra', 'construcao']
    };

    if (autoTags[categoria]) {
        autoTags[categoria].forEach(tag => {
            if (!tags.includes(tag)) {
                tags.push(tag);
            }
        });
        updateTagDisplay();
    }
});

// Form submission
document.querySelector('form').addEventListener('submit', function(e) {
    // Add tags as hidden inputs
    tags.forEach((tag, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `tags[${index}]`;
        input.value = tag;
        this.appendChild(input);
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTagDisplay();
});
</script>
@endpush