@extends('layouts.admin')

@section('title', 'Relatórios - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-chart-bar text-primary"></i>
                        Relatórios Avançados
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Relatórios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Cards de Relatórios -->
            <div class="row">
                <!-- Relatório de Projeto -->
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building text-primary"></i>
                                Relatório de Projeto
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Relatório completo de um projeto específico com atividades, equipe, materiais e fotos.</p>
                            <form action="{{ route('diario-obras.relatorios.projeto', 1) }}" method="GET">
                                <div class="form-group">
                                    <label>Selecionar Projeto:</label>
                                    <select class="form-control" name="projeto_id">
                                        <option value="">Selecione um projeto</option>
                                        @foreach(\App\Models\Projeto::all() as $projeto)
                                            <option value="{{ $projeto->id }}">{{ $projeto->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Período:</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="data_inicio" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="data_fim" value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Formato:</label>
                                    <select class="form-control" name="formato">
                                        <option value="html">Visualizar</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-chart-bar"></i>
                                    Gerar Relatório
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Produtividade -->
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users text-success"></i>
                                Relatório de Produtividade
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Análise de produtividade da equipe com horas trabalhadas e atividades realizadas.</p>
                            <form action="{{ route('diario-obras.relatorios.produtividade') }}" method="GET">
                                <div class="form-group">
                                    <label>Projeto (Opcional):</label>
                                    <select class="form-control" name="projeto_id">
                                        <option value="">Todos os projetos</option>
                                        @foreach(\App\Models\Projeto::all() as $projeto)
                                            <option value="{{ $projeto->id }}">{{ $projeto->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Período:</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="data_inicio" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="data_fim" value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-chart-line"></i>
                                    Gerar Relatório
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Relatório de Custos -->
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-dollar-sign text-warning"></i>
                                Relatório de Custos
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Análise de custos com materiais, fornecedores e gastos por projeto.</p>
                            <form action="{{ route('diario-obras.relatorios.custos') }}" method="GET">
                                <div class="form-group">
                                    <label>Projeto (Opcional):</label>
                                    <select class="form-control" name="projeto_id">
                                        <option value="">Todos os projetos</option>
                                        @foreach(\App\Models\Projeto::all() as $projeto)
                                            <option value="{{ $projeto->id }}">{{ $projeto->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Período:</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="data_inicio" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="date" class="form-control" name="data_fim" value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-chart-pie"></i>
                                    Gerar Relatório
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exportação de Dados -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-download text-info"></i>
                                Exportação de Dados
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tipo de Dados:</label>
                                        <select class="form-control" id="tipoExportacao">
                                            <option value="projetos">Projetos</option>
                                            <option value="atividades">Atividades</option>
                                            <option value="equipe">Equipe</option>
                                            <option value="materiais">Materiais</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data Início:</label>
                                        <input type="date" class="form-control" id="dataInicioExport" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Data Fim:</label>
                                        <input type="date" class="form-control" id="dataFimExport" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-info btn-block" onclick="exportarDados()">
                                            <i class="fas fa-file-excel"></i>
                                            Exportar Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-area text-secondary"></i>
                                Estatísticas Rápidas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-building"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total de Projetos</span>
                                            <span class="info-box-number">{{ \App\Models\Projeto::count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success">
                                            <i class="fas fa-tasks"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total de Atividades</span>
                                            <span class="info-box-number">{{ \App\Models\AtividadeObra::count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-users"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Registros de Equipe</span>
                                            <span class="info-box-number">{{ \App\Models\EquipeObra::count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Movimentações de Materiais</span>
                                            <span class="info-box-number">{{ \App\Models\MaterialObra::count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function exportarDados() {
    const tipo = document.getElementById('tipoExportacao').value;
    const dataInicio = document.getElementById('dataInicioExport').value;
    const dataFim = document.getElementById('dataFimExport').value;

    if (!dataInicio || !dataFim) {
        alert('Por favor, selecione as datas de início e fim.');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("diario-obras.exportar.excel") }}';

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';

    const tipoInput = document.createElement('input');
    tipoInput.type = 'hidden';
    tipoInput.name = 'tipo';
    tipoInput.value = tipo;

    const dataInicioInput = document.createElement('input');
    dataInicioInput.type = 'hidden';
    dataInicioInput.name = 'data_inicio';
    dataInicioInput.value = dataInicio;

    const dataFimInput = document.createElement('input');
    dataFimInput.type = 'hidden';
    dataFimInput.name = 'data_fim';
    dataFimInput.value = dataFim;

    form.appendChild(csrfToken);
    form.appendChild(tipoInput);
    form.appendChild(dataInicioInput);
    form.appendChild(dataFimInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush

