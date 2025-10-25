<div>
    <!-- Header do Dashboard -->
    <div class="row mb-4">
        <div class="col-12 col-md-8">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt text-primary"></i>
                <span class="d-none d-md-inline">Dashboard Analytics</span>
                <span class="d-md-none">Dashboard</span>
            </h1>
            <p class="text-muted d-none d-md-block">Visão geral do sistema em tempo real</p>
        </div>
        <div class="col-12 col-md-4">
            <div class="d-flex flex-column flex-md-row gap-2">
                <select wire:model.live="periodo" class="form-select form-select-sm flex-grow-1">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30">Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                    <option value="365">Último ano</option>
                </select>
                <button wire:click="refresh" class="btn btn-outline-primary btn-sm"
                        @if($loading) disabled @endif>
                    <i class="fas fa-sync-alt @if($loading) fa-spin @endif"></i>
                    <span class="d-none d-sm-inline">Atualizar</span>
                </button>
                @if($cacheWorking)
                    <button wire:click="clearCache" class="btn btn-outline-warning btn-sm ml-2">
                        <i class="fas fa-trash"></i>
                        <span class="d-none d-sm-inline">Limpar Cache</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($loading)
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando dados do dashboard...</p>
        </div>
    @else
        <!-- Cards de Métricas Principais -->
        <div class="row mb-4">
            <!-- Contratos -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total de Contratos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalContratos }}</div>
                                <div class="text-xs text-muted">
                                    <span class="text-success">{{ $contratosAtivos }} ativos</span> |
                                    <span class="text-danger">{{ $contratosVencidos }} vencidos</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medições -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Medições
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMedicoes }}</div>
                                <div class="text-xs text-muted">
                                    <span class="text-warning">{{ $medicoesPendentes }} pendentes</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ruler fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagamentos -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pagamentos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPagamentos }}</div>
                                <div class="text-xs text-muted">
                                    <span class="text-warning">{{ $pagamentosPendentes }} pendentes</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuários -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Usuários
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsuarios }}</div>
                                <div class="text-xs text-muted">
                                    <span class="text-warning">{{ $usuariosPendentes }} pendentes</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Valores Financeiros -->
        <div class="row mb-4">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Valor Total das Medições
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    R$ {{ number_format($valorTotalMedicoes, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Valor Total dos Pagamentos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    R$ {{ number_format($valorTotalPagamentos, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <!-- Gráfico de Contratos por Status -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Contratos por Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="contratosStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Medições por Mês -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-success">Medições por Mês</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="medicoesMesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Pagamentos por Mês -->
        <div class="row mb-4">
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-info">Pagamentos por Mês</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="pagamentosMesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Usuários por Mês -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-warning">Usuários por Mês</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="usuariosMesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Cache -->
        @if($cacheWorking && !empty($cacheStats))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-memory"></i> Estatísticas de Performance
                        </h6>
                        <span class="badge badge-success">
                            <i class="fas fa-check"></i> Cache Ativo
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Taxa de Cache Hit
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $cacheStats['hit_rate'] ?? 0 }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Memória Usada
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $cacheStats['used_memory'] ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Clientes Conectados
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $cacheStats['connected_clients'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Comandos Processados
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($cacheStats['total_commands_processed'] ?? 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(isset($cacheStats['cached_at']))
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i>
                                Última atualização: {{ \Carbon\Carbon::parse($cacheStats['cached_at'])->format('d/m/Y H:i:s') }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Atividades Recentes e Top Contratos -->
        <div class="row">
            <!-- Atividades Recentes -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Atividades Recentes</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse($atividadesRecentes as $atividade)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $atividade['cor'] }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $atividade['titulo'] }}</h6>
                                        <p class="timeline-text">{{ $atividade['descricao'] }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $atividade['usuario'] }} |
                                            <i class="fas fa-clock"></i> {{ $atividade['data']->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">Nenhuma atividade recente</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Contratos -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Top Contratos</h6>
                    </div>
                    <div class="card-body">
                        @forelse($topContratos as $contrato)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">{{ $contrato['numero'] }}</h6>
                                    <small class="text-muted">{{ Str::limit($contrato['descricao'], 30) }}</small>
                                    <div class="small text-muted">
                                        <span class="badge bg-{{ $this->getStatusColor($contrato['status']) }}">
                                            {{ $this->getStatusName($contrato['status']) }}
                                        </span>
                                        <br>
                                        <strong>R$ {{ number_format($contrato['valor_total'], 2, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Nenhum contrato encontrado</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Scripts para os gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Armazenar instâncias dos gráficos para poder destruí-los
        let chartInstances = {};
        let isRendering = false; // Flag para prevenir múltiplas execuções

        // Função para renderizar gráficos com proteção contra conflitos
        async function renderizarGraficos() {
            // Prevenir múltiplas execuções simultâneas
            if (isRendering) {
                console.log('⚠️ Renderização já em andamento, ignorando...');
                return;
            }

            isRendering = true;
            console.log('Iniciando renderização dos gráficos...');

            try {
                // Verificar se Chart.js está disponível
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js não está disponível!');
                    return;
                }

            // Destruir gráficos existentes de forma mais robusta
            Object.keys(chartInstances).forEach(key => {
                if (chartInstances[key] && typeof chartInstances[key].destroy === 'function') {
                    try {
                        chartInstances[key].destroy();
                        console.log(`🗑️ Gráfico ${key} destruído`);
                    } catch (error) {
                        console.warn(`⚠️ Erro ao destruir gráfico ${key}:`, error);
                    }
                    chartInstances[key] = null;
                }
            });

            // Limpar completamente o objeto
            chartInstances = {};

            // Aguardar um pouco para garantir que os gráficos foram destruídos
            await new Promise(resolve => setTimeout(resolve, 100));

            // Dados dos gráficos (passados do PHP)
            const contratosData = @json($contratosPorStatus);
            const medicoesData = @json($medicoesPorMes);
            const pagamentosData = @json($pagamentosPorMes);
            const usuariosData = @json($usuariosPorMes);

            console.log('Dados recebidos:', {
                contratos: contratosData,
                medicoes: medicoesData,
                pagamentos: pagamentosData,
                usuarios: usuariosData
            });

            // Gráfico de Contratos por Status
            const contratosCtx = document.getElementById('contratosStatusChart');
            if (contratosCtx) {
                console.log('Renderizando gráfico de contratos por status...');

                // Verificar se já existe um gráfico neste canvas
                const existingChart = Chart.getChart(contratosCtx);
                if (existingChart) {
                    console.log('🗑️ Destruindo gráfico existente no canvas contratosStatusChart');
                    existingChart.destroy();
                }

                chartInstances.contratos = new Chart(contratosCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(contratosData).map(status => {
                            const statusNames = {
                                'ativo': 'Ativo',
                                'inativo': 'Inativo',
                                'vencido': 'Vencido',
                                'suspenso': 'Suspenso'
                            };
                            return statusNames[status] || status;
                        }),
                        datasets: [{
                            data: Object.values(contratosData),
                            backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e', '#858796'],
                            hoverBackgroundColor: ['#17a673', '#e02d1b', '#dda20a', '#6c757d'],
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Gráfico de Medições por Mês
            const medicoesCtx = document.getElementById('medicoesMesChart');
            if (medicoesCtx) {
                console.log('Renderizando gráfico de medições por mês...');

                // Verificar se já existe um gráfico neste canvas
                const existingChart = Chart.getChart(medicoesCtx);
                if (existingChart) {
                    console.log('🗑️ Destruindo gráfico existente no canvas medicoesMesChart');
                    existingChart.destroy();
                }

                chartInstances.medicoes = new Chart(medicoesCtx, {
                    type: 'line',
                    data: {
                        labels: medicoesData.map(item => {
                            const [ano, mes] = item.mes.split('-');
                            return `${mes}/${ano}`;
                        }),
                        datasets: [{
                            label: 'Quantidade',
                            data: medicoesData.map(item => item.total),
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28, 200, 138, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Valor (R$)',
                            data: medicoesData.map(item => item.valor),
                            borderColor: '#36b9cc',
                            backgroundColor: 'rgba(54, 185, 204, 0.1)',
                            tension: 0.4,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Gráfico de Pagamentos por Mês
            const pagamentosCtx = document.getElementById('pagamentosMesChart');
            if (pagamentosCtx) {
                console.log('Renderizando gráfico de pagamentos por mês...');

                // Verificar se já existe um gráfico neste canvas
                const existingChart = Chart.getChart(pagamentosCtx);
                if (existingChart) {
                    console.log('🗑️ Destruindo gráfico existente no canvas pagamentosMesChart');
                    existingChart.destroy();
                }

                chartInstances.pagamentos = new Chart(pagamentosCtx, {
                    type: 'bar',
                    data: {
                        labels: pagamentosData.map(item => {
                            const [ano, mes] = item.mes.split('-');
                            return `${mes}/${ano}`;
                        }),
                        datasets: [{
                            label: 'Quantidade',
                            data: pagamentosData.map(item => item.total),
                            backgroundColor: '#36b9cc',
                            borderColor: '#36b9cc',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Gráfico de Usuários por Mês
            const usuariosCtx = document.getElementById('usuariosMesChart');
            if (usuariosCtx) {
                console.log('Renderizando gráfico de usuários por mês...');

                // Verificar se já existe um gráfico neste canvas
                const existingChart = Chart.getChart(usuariosCtx);
                if (existingChart) {
                    console.log('🗑️ Destruindo gráfico existente no canvas usuariosMesChart');
                    existingChart.destroy();
                }

                chartInstances.usuarios = new Chart(usuariosCtx, {
                    type: 'bar',
                    data: {
                        labels: usuariosData.map(item => {
                            const [ano, mes] = item.mes.split('-');
                            return `${mes}/${ano}`;
                        }),
                        datasets: [{
                            label: 'Novos Usuários',
                            data: usuariosData.map(item => item.total),
                            backgroundColor: '#f6c23e',
                            borderColor: '#f6c23e',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            console.log('Gráficos renderizados com sucesso!');

        } catch (error) {
            console.error('❌ Erro durante renderização dos gráficos:', error);
        } finally {
            // Liberar flag de renderização
            isRendering = false;
        }
        }

        // Executar quando a página carregar
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM carregado, aguardando para renderizar gráficos...');
            setTimeout(function() {
                renderizarGraficos();
            }, 1000);
        });

        // Aguardar o Livewire estar pronto (nova sintaxe)
        document.addEventListener('livewire:init', function () {
            console.log('Livewire inicializado, aguardando para renderizar gráficos...');
            setTimeout(function() {
                renderizarGraficos();
            }, 500);
        });

        // Executar quando o Livewire atualizar (incluindo após clicar em "Atualizar")
        document.addEventListener('livewire:update', function () {
            console.log('Livewire atualizado, renderizando gráficos...');
            setTimeout(function() {
                renderizarGraficos();
            }, 300);
        });

        // Executar quando o Livewire terminar de atualizar
        document.addEventListener('livewire:updated', function () {
            console.log('Livewire atualização concluída, renderizando gráficos...');
            setTimeout(function() {
                renderizarGraficos();
            }, 200);
        });
    </script>

    <!-- CSS para Timeline -->
    @push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .timeline-content {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #e3e6f0;
        }

        .timeline-title {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-text {
            margin-bottom: 10px;
            color: #5a5c69;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .chart-pie {
            height: 300px;
        }

        .chart-area {
            height: 300px;
        }
    </style>
    @endpush
</div>
