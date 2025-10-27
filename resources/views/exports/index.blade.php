@extends('layouts.admin')

@section('title', config('app.name') . ' - Exportação de Relatórios')
@section('page-title', config('app.name') . ' - Exportação de Relatórios')

@section('breadcrumb')
<li class="breadcrumb-item active">Exportação</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-download"></i> {{ config('app.name') }} - Sistema de Exportação
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">PDF e Excel</span>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-filter"></i> Filtros de Exportação
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="exportForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_inicio">Data Início</label>
                                                <input type="date" class="form-control" id="data_inicio" name="data_inicio">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="data_fim">Data Fim</label>
                                                <input type="date" class="form-control" id="data_fim" name="data_fim">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="">Todos</option>
                                                    <option value="ativo">Ativo</option>
                                                    <option value="inativo">Inativo</option>
                                                    <option value="vencido">Vencido</option>
                                                    <option value="suspenso">Suspenso</option>
                                                    <option value="pendente">Pendente</option>
                                                    <option value="aprovado">Aprovado</option>
                                                    <option value="rejeitado">Rejeitado</option>
                                                    <option value="pago">Pago</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="lotacao_id">Lotação</label>
                                                <select class="form-control" id="lotacao_id" name="lotacao_id">
                                                    <option value="">Todas</option>
                                                    @foreach(\App\Models\Lotacao::all() as $lotacao)
                                                        <option value="{{ $lotacao->id }}">{{ $lotacao->nome }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatórios Disponíveis -->
                <div class="row">
                    <!-- Contratos -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-file-contract"></i> Contratos
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Exportar lista de contratos com filtros personalizados.</p>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger" onclick="exportar('contratos', 'pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="exportar('contratos', 'excel')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medições -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-ruler"></i> Medições
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Exportar medições com valores e status detalhados.</p>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger" onclick="exportar('medicoes', 'pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="exportar('medicoes', 'excel')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagamentos -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-warning text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-money-bill"></i> Pagamentos
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Exportar pagamentos realizados e pendentes.</p>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger" onclick="exportar('pagamentos', 'pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="exportar('pagamentos', 'excel')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Relatório Financeiro -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-chart-line"></i> Relatório Financeiro
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Relatório completo com resumo financeiro e estatísticas.</p>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger" onclick="exportar('relatorio-financeiro', 'pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-secondary" disabled>
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Relatório de Usuários -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-users"></i> Usuários
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Lista de usuários cadastrados no sistema.</p>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger" onclick="exportar('usuarios', 'pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="exportar('usuarios', 'excel')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auditoria -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-dark text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history"></i> Auditoria
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Log de atividades e alterações no sistema.</p>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-danger w-100" onclick="exportar('auditoria', 'pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informações sobre Exportação</h5>
                            <ul class="mb-0">
                                <li><strong>PDF:</strong> Relatórios formatados para impressão - abrem em nova aba do navegador</li>
                                <li><strong>Excel:</strong> Planilhas editáveis com dados estruturados - download automático</li>
                                <li><strong>Filtros:</strong> Aplicados automaticamente em todas as exportações</li>
                                <li><strong>Formato:</strong> Arquivos nomeados com data e hora da exportação</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportar(tipo, formato) {
    // Coletar filtros do formulário
    const formData = new FormData(document.getElementById('exportForm'));
    const params = new URLSearchParams();

    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }

    // Construir URL
    const url = `/exports/${tipo}/${formato}?${params.toString()}`;

    // Mostrar loading
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
    button.disabled = true;

    if (formato === 'pdf') {
        // Para PDF: abrir em nova aba
        window.open(url, '_blank');
    } else {
        // Para Excel: fazer download
        const link = document.createElement('a');
        link.href = url;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Restaurar botão após 2 segundos
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

// Definir data padrão (últimos 30 dias)
document.addEventListener('DOMContentLoaded', function() {
    const hoje = new Date();
    const trintaDiasAtras = new Date(hoje.getTime() - (30 * 24 * 60 * 60 * 1000));

    document.getElementById('data_fim').value = hoje.toISOString().split('T')[0];
    document.getElementById('data_inicio').value = trintaDiasAtras.toISOString().split('T')[0];
});
</script>
@endsection
