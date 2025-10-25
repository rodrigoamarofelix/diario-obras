@extends('layouts.admin')

@section('title', 'Upload de Fotos - Diário de Obras')

@push('styles')
<!-- Mapa isolado em iframe -->

<style>
    .upload-area {
        border: 3px dashed #007bff;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-area:hover {
        border-color: #0056b3;
        background: #e3f2fd;
    }

    .upload-area.dragover {
        border-color: #28a745;
        background: #d4edda;
    }

    .preview-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .preview-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 10px;
        font-size: 12px;
    }

    .remove-photo {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-size: 14px;
    }

    .gps-status {
        padding: 10px;
        border-radius: 5px;
        margin: 10px 0;
        font-weight: bold;
    }

    .gps-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .gps-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .gps-loading {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
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

    .progress-bar {
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #007bff, #28a745);
        transition: width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    /* Estilo do mapa isolado */
    #mapContainer {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Responsividade do mapa */
    @media (max-width: 768px) {
        #mapContainer {
            height: 200px !important;
        }
    }

    @media (max-width: 480px) {
        #mapContainer {
            height: 180px !important;
        }
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
                        <i class="fas fa-camera text-primary"></i>
                        Upload de Fotos
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.fotos.index') }}">Fotos</a></li>
                        <li class="breadcrumb-item active">Upload</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form id="uploadForm" action="{{ route('diario-obras.fotos.upload-multiple') }}" method="POST" enctype="multipart/form-data">
                @csrf

            <div class="row">
                    <!-- Upload Area -->
                    <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    Selecionar Fotos
                            </h3>
                        </div>
                        <div class="card-body">
                                <!-- GPS Status -->
                                <div id="gpsStatus" class="gps-loading">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Obtendo localização GPS...
                                </div>

                                <!-- Camera Status -->
                                <div id="cameraStatus" class="gps-loading" style="display:none;">
                                    <i class="fas fa-camera"></i>
                                    Aguardando acesso à câmera...
                                </div>

                                <!-- Upload Area -->
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                    <h4>Arraste e solte suas fotos aqui</h4>
                                    <p class="text-muted">ou clique para selecionar arquivos</p>
                                    <p class="text-muted">
                                        <small>Formatos aceitos: JPG, PNG, GIF (máx. 20MB cada)</small>
                                    </p>
                                    <input type="file" id="fileInput" name="fotos[]" multiple accept="image/*" style="display: none;">
                                </div>

                                <!-- Camera Button -->
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-success btn-lg" id="cameraBtn">
                                        <i class="fas fa-camera"></i>
                                        Tirar Foto com Câmera
                                    </button>
                                </div>

                                <!-- Camera Modal -->
                                <div id="cameraModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999;">
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; max-width: 90%; max-height: 90%;">
                                        <div style="text-align: center;">
                                            <h4>Tirar Foto</h4>
                                            <video id="cameraVideo" autoplay muted playsinline style="width: 100%; max-width: 400px; border-radius: 5px;"></video>
                                            <div style="margin: 15px 0;">
                                                <button type="button" class="btn btn-primary" id="captureBtn">
                                                    <i class="fas fa-camera"></i> Capturar
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="closeCameraBtn">
                                                    <i class="fas fa-times"></i> Fechar
                                                </button>
                                            </div>
                                            <canvas id="cameraCanvas" style="display: none;"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div id="progressContainer" style="display: none;">
                                    <div class="progress-bar">
                                        <div class="progress-fill" id="progressFill" style="width: 0%">0%</div>
                                    </div>
                                </div>

                                <!-- Preview Container -->
                                <div id="previewContainer" class="preview-container"></div>
                            </div>
                                        </div>
                                    </div>

                    <!-- Form Details -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i>
                                    Informações da Foto
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Projeto -->
                                        <div class="form-group">
                                    <label for="projeto_id">Projeto *</label>
                                    <select class="form-control" id="projeto_id" name="projeto_id" required>
                                        <option value="">Selecione um projeto</option>
                                        @foreach($projetos as $projeto)
                                            <option value="{{ $projeto->id }}" {{ (isset($projetoId) && $projetoId == $projeto->id) ? 'selected' : '' }}>
                                                {{ $projeto->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Atividade -->
                                <div class="form-group">
                                    <label for="atividade_id">Atividade</label>
                                    <select class="form-control" id="atividade_id" name="atividade_id">
                                        <option value="">Selecione uma atividade</option>
                                        @foreach($atividades as $atividade)
                                            <option value="{{ $atividade->id }}">{{ $atividade->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Equipe -->
                                <div class="form-group">
                                    <label for="equipe_id">Equipe</label>
                                    <select class="form-control" id="equipe_id" name="equipe_id">
                                        <option value="">Selecione uma equipe</option>
                                        @foreach($equipes as $equipe)
                                            <option value="{{ $equipe->id }}">
                                                {{ $equipe->pessoa->nome ?? 'N/A' }} - {{ $equipe->projeto->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Categoria -->
                                <div class="form-group">
                                    <label for="categoria">Categoria *</label>
                                    <select class="form-control" id="categoria" name="categoria" required>
                                        <option value="">Selecione uma categoria</option>
                                        <option value="antes">Antes da Obra</option>
                                        <option value="progresso">Progresso</option>
                                        <option value="problema">Problema</option>
                                        <option value="solucao">Solução</option>
                                        <option value="final">Resultado Final</option>
                                        <option value="geral">Geral</option>
                                    </select>
                                </div>

                                <!-- Tags -->
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <div class="tag-input" id="tagContainer">
                                        <input type="text" id="tagInput" placeholder="Digite uma tag e pressione Enter" style="border: none; outline: none; flex: 1;">
                                    </div>
                                    <small class="text-muted">Tags automáticas serão adicionadas baseadas na categoria</small>
                                </div>

                                <!-- Coordenadas (hidden) -->
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                                <input type="hidden" id="altitude" name="altitude">
                                <input type="hidden" id="precisao" name="precisao">

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block" id="submitBtn" disabled>
                                        <i class="fas fa-upload"></i>
                                        Upload das Fotos
                                    </button>
                                </div>

                                <!-- Test Button -->
                                <div class="form-group">
                                    <button type="button" class="btn btn-warning btn-block" id="testBtn">
                                        <i class="fas fa-test-tube"></i>
                                        Teste de Conexão
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- GPS Info -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Localização GPS
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="gpsInfo">
                                    <p class="text-muted">Aguardando localização...</p>
                                </div>

                                <!-- Mapa Isolado em iframe -->
                                <div id="mapContainer" style="height: 250px; width: 100%; border-radius: 8px; margin-top: 15px; display: none; border: 2px solid #dee2e6; position: relative;">
                                    <iframe id="mapFrame" src="about:blank" style="width: 100%; height: 100%; border: none; border-radius: 6px;" frameborder="0"></iframe>
                                </div>

                                <!-- Botão para atualizar localização -->
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="getCurrentLocation()" style="display: none;" id="refreshLocationBtn">
                                        <i class="fas fa-sync-alt"></i> Atualizar Localização
                                    </button>
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
<!-- Mapa isolado em iframe -->

<script>
let selectedFiles = [];
let currentLocation = null;
let tags = [];
let cameraStream = null;

// Camera Functions
function openCamera() {
    console.log('Solicitando permissão da câmera...');
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');

    modal.style.display = 'block';

    // Show permission request message
    const statusDiv = document.getElementById('cameraStatus');
    if (statusDiv) {
        statusDiv.style.display = 'block';
        statusDiv.className = 'status info';
        statusDiv.textContent = '📷 Solicitando permissão da câmera...';
    }

    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: 'environment',
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }
    })
    .then(function(stream) {
        console.log('Câmera acessada com sucesso!');
        cameraStream = stream;
        video.srcObject = stream;
        video.play();

        if (statusDiv) {
            statusDiv.className = 'status success';
            statusDiv.textContent = '✅ Câmera aberta!';
        }
    })
    .catch(function(error) {
        console.error('Erro ao acessar câmera:', error);
        let errorMessage = 'Erro ao acessar câmera: ';

        if (error.name === 'NotAllowedError') {
            errorMessage += '❌ Permissão negada - permita acesso à câmera';
        } else if (error.name === 'NotFoundError') {
            errorMessage += '❌ Câmera não encontrada';
        } else if (error.name === 'NotSupportedError') {
            errorMessage += '❌ Câmera não suportada';
        } else if (error.name === 'NotReadableError') {
            errorMessage += '❌ Câmera já em uso';
        } else {
            errorMessage += error.message;
        }

        alert(errorMessage);
        closeCamera();
    });
}

function closeCamera() {
    const modal = document.getElementById('cameraModal');
    modal.style.display = 'none';

    if (cameraStream) {
        cameraStream.getTracks().forEach(function(track) {
            track.stop();
        });
        cameraStream = null;
    }
}

function capturePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const ctx = canvas.getContext('2d');

    // Set canvas size to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Draw video frame to canvas
    ctx.drawImage(video, 0, 0);

    // Convert canvas to blob
    canvas.toBlob(function(blob) {
        // Create a File object from blob
        const file = new File([blob], 'camera-photo-' + Date.now() + '.jpg', {
            type: 'image/jpeg'
        });

        // Add to selected files
        selectedFiles.push(file);
        createPreview(file);
        updateSubmitButton();

        // Close camera
        closeCamera();

        // Show success message
        alert('Foto capturada com sucesso!');
    }, 'image/jpeg', 0.8);
}

// GPS Functions
function getCurrentLocation() {
    if (!navigator.geolocation) {
        updateGpsStatus('error', 'Geolocalização não suportada pelo navegador');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            console.log('GPS obtido com sucesso!');
            currentLocation = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                altitude: position.coords.altitude,
                accuracy: position.coords.accuracy
            };

            // Update hidden inputs
            document.getElementById('latitude').value = currentLocation.latitude;
            document.getElementById('longitude').value = currentLocation.longitude;
            document.getElementById('altitude').value = currentLocation.altitude || '';
            document.getElementById('precisao').value = currentLocation.accuracy;

            updateGpsStatus('success', `✅ Localização obtida! Precisão: ${Math.round(currentLocation.accuracy)}m`);
            updateGpsInfo();
        },
        function(error) {
            console.log('Erro GPS:', error);
            let message = 'Erro ao obter localização: ';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message += '❌ Permissão negada - permita acesso à localização';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message += '❌ Posição indisponível';
                    break;
                case error.TIMEOUT:
                    message += '⏰ Timeout - tente novamente';
                    break;
                default:
                    message += '❌ Erro desconhecido';
                    break;
            }
            updateGpsStatus('error', message);
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0  // Force fresh location
        }
    );
}

function updateGpsStatus(type, message) {
    const statusDiv = document.getElementById('gpsStatus');
    statusDiv.className = `gps-status gps-${type}`;
    statusDiv.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${message}`;
}

function updateGpsInfo() {
    if (!currentLocation) return;

    const infoDiv = document.getElementById('gpsInfo');
    infoDiv.innerHTML = `
        <p><strong>Latitude:</strong> ${currentLocation.latitude.toFixed(6)}</p>
        <p><strong>Longitude:</strong> ${currentLocation.longitude.toFixed(6)}</p>
        <p><strong>Altitude:</strong> ${currentLocation.altitude ? currentLocation.altitude.toFixed(2) + 'm' : 'N/A'}</p>
        <p><strong>Precisão:</strong> ${Math.round(currentLocation.accuracy)}m</p>
    `;

    // Mostrar mapa e botão de atualizar
    document.getElementById('mapContainer').style.display = 'block';
    document.getElementById('refreshLocationBtn').style.display = 'inline-block';

    // Aguardar um pouco para garantir que o elemento esteja visível
    setTimeout(() => {
        initMap();
    }, 500);
}

function initMap() {
    if (!currentLocation) {
        console.log('❌ Sem localização para criar mapa');
        return;
    }

    console.log('🗺️ Inicializando mapa isolado...', currentLocation);

    const mapContainer = document.getElementById('mapContainer');
    const mapFrame = document.getElementById('mapFrame');

    if (!mapContainer || !mapFrame) {
        console.log('❌ Elementos do mapa não encontrados');
        return;
    }

    try {
        // Carregar o mapa isolado no iframe
        mapFrame.src = '/mapa_isolado.html';

        // Aguardar o iframe carregar e enviar dados
        mapFrame.onload = function() {
            setTimeout(() => {
                mapFrame.contentWindow.postMessage({
                    type: 'initMap',
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    accuracy: currentLocation.accuracy
                }, '*');

                console.log('✅ Dados enviados para mapa isolado');
            }, 500);
        };

    } catch (error) {
        console.error('❌ Erro ao criar mapa isolado:', error);
    }
}

// File Upload Functions
function handleFileSelect(files) {
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            selectedFiles.push(file);
            createPreview(file);
        }
    });
    updateSubmitButton();
}

function createPreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const previewContainer = document.getElementById('previewContainer');
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.innerHTML = `
            <img src="${e.target.result}" alt="${file.name}">
            <div class="preview-info">
                <div>${file.name}</div>
                <div>${(file.size / 1024 / 1024).toFixed(2)} MB</div>
            </div>
            <button type="button" class="remove-photo" onclick="removeFile('${file.name}')">
                <i class="fas fa-times"></i>
            </button>
        `;
        previewContainer.appendChild(previewItem);
    };
    reader.readAsDataURL(file);
}

function removeFile(fileName) {
    selectedFiles = selectedFiles.filter(file => file.name !== fileName);
    updatePreview();
    updateSubmitButton();
}

function updatePreview() {
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';
    selectedFiles.forEach(file => createPreview(file));
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = selectedFiles.length === 0;
    submitBtn.innerHTML = `
        <i class="fas fa-upload"></i>
        Upload ${selectedFiles.length} Foto${selectedFiles.length !== 1 ? 's' : ''}
    `;
}

// Tag Functions
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

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Get GPS location with explicit permission request
    setTimeout(() => {
        console.log('Solicitando permissão de GPS...');
        getCurrentLocation();
    }, 1000);

    // Upload area events
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');

    uploadArea.addEventListener('click', () => fileInput.click());

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        handleFileSelect(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', function(e) {
        handleFileSelect(e.target.files);
    });

    // Camera button events
    document.getElementById('cameraBtn').addEventListener('click', openCamera);
    document.getElementById('captureBtn').addEventListener('click', capturePhoto);
    document.getElementById('closeCameraBtn').addEventListener('click', closeCamera);

    // Test connection button
    document.getElementById('testBtn').addEventListener('click', function() {
        console.log('Testing connection...');

        const xhr = new XMLHttpRequest();
        xhr.timeout = 10000; // 10 seconds

        xhr.addEventListener('load', function() {
            console.log('Test completed, status:', xhr.status);
            if (xhr.status === 200) {
                alert('✅ Conexão OK! Servidor respondendo normalmente.');
            } else {
                alert('⚠️ Servidor respondeu com status: ' + xhr.status);
            }
        });

        xhr.addEventListener('error', function() {
            console.log('Test error');
            alert('❌ Erro de conexão! Verifique sua internet.');
        });

        xhr.addEventListener('timeout', function() {
            console.log('Test timeout');
            alert('⏰ Timeout! Conexão muito lenta.');
        });

        xhr.open('GET', '{{ route("diario-obras.dashboard") }}');
        xhr.send();
    });

    // Form submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        console.log('Form submitted');
        console.log('Selected files:', selectedFiles.length);

        if (selectedFiles.length === 0) {
            alert('Selecione pelo menos uma foto');
            return;
        }

        // Validate required fields
        const projetoId = document.getElementById('projeto_id').value;
        const categoria = document.getElementById('categoria').value;

        if (!projetoId) {
            alert('Selecione um projeto');
            return;
        }

        if (!categoria) {
            alert('Selecione uma categoria');
            return;
        }

        console.log('Creating FormData...');

        // Create FormData
        const formData = new FormData();

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        }

        formData.append('projeto_id', projetoId);
        formData.append('atividade_id', document.getElementById('atividade_id').value);
        formData.append('equipe_id', document.getElementById('equipe_id').value);
        formData.append('categoria', categoria);
        formData.append('latitude', document.getElementById('latitude').value);
        formData.append('longitude', document.getElementById('longitude').value);
        formData.append('altitude', document.getElementById('altitude').value);
        formData.append('precisao', document.getElementById('precisao').value);

        // Add tags
        tags.forEach((tag, index) => {
            formData.append(`tags[${index}]`, tag);
        });

        // Add files
        selectedFiles.forEach((file, index) => {
            formData.append(`fotos[${index}]`, file);
        });

        console.log('FormData created, files:', selectedFiles.length);

        // Show progress
        document.getElementById('progressContainer').style.display = 'block';

        // Submit with progress
        const xhr = new XMLHttpRequest();

        // Increase timeout for mobile connections
        xhr.timeout = 120000; // 2 minutes

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                document.getElementById('progressFill').style.width = percentComplete + '%';
                document.getElementById('progressFill').textContent = Math.round(percentComplete) + '%';
            }
        });

        xhr.addEventListener('load', function() {
            console.log('Upload completed, status:', xhr.status);
            console.log('Response:', xhr.responseText);

            if (xhr.status === 200) {
                alert('Upload realizado com sucesso!');
                window.location.href = '{{ route("diario-obras.fotos.index") }}';
            } else {
                alert('Erro no upload: ' + xhr.responseText);
            }
        });

        xhr.addEventListener('error', function() {
            console.log('Upload error');
            alert('Erro de conexão durante o upload. Verifique sua internet e tente novamente.');
        });

        xhr.addEventListener('timeout', function() {
            console.log('Upload timeout');
            alert('Upload demorou muito. Verifique sua conexão e tente novamente.');
        });

        console.log('Sending request to:', this.action);
        xhr.open('POST', this.action);

        // Add headers for better compatibility
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.send(formData);
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
});
</script>
@endpush