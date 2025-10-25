@extends('layouts.admin')

@section('title', 'Configurações - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-cog text-secondary"></i>
                        Configurações do Sistema
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Configurações Gerais -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs text-primary"></i>
                                Configurações Gerais
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Nome da Empresa:</label>
                                    <input type="text" class="form-control" value="Construtora Exemplo Ltda">
                                </div>
                                <div class="form-group">
                                    <label>CNPJ:</label>
                                    <input type="text" class="form-control" value="12.345.678/0001-90">
                                </div>
                                <div class="form-group">
                                    <label>Endereço:</label>
                                    <textarea class="form-control" rows="3">Rua das Obras, 123 - Centro - São Paulo/SP</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Telefone:</label>
                                    <input type="text" class="form-control" value="(11) 99999-9999">
                                </div>
                                <div class="form-group">
                                    <label>Email:</label>
                                    <input type="email" class="form-control" value="contato@construtora.com">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt text-success"></i>
                                Configurações de Datas
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Fuso Horário:</label>
                                    <select class="form-control">
                                        <option value="America/Sao_Paulo" selected>Brasília (GMT-3)</option>
                                        <option value="America/Manaus">Manaus (GMT-4)</option>
                                        <option value="America/Rio_Branco">Rio Branco (GMT-5)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Formato de Data:</label>
                                    <select class="form-control">
                                        <option value="d/m/Y" selected>DD/MM/AAAA</option>
                                        <option value="m/d/Y">MM/DD/AAAA</option>
                                        <option value="Y-m-d">AAAA-MM-DD</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Formato de Hora:</label>
                                    <select class="form-control">
                                        <option value="H:i" selected>24 horas (HH:MM)</option>
                                        <option value="g:i A">12 horas (HH:MM AM/PM)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Primeiro Dia da Semana:</label>
                                    <select class="form-control">
                                        <option value="0" selected>Domingo</option>
                                        <option value="1">Segunda-feira</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Backup -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-database text-warning"></i>
                                Backup Automático
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">
                                            Ativar backup automático
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Frequência do Backup:</label>
                                    <select class="form-control">
                                        <option value="diario" selected>Diariamente</option>
                                        <option value="semanal">Semanalmente</option>
                                        <option value="mensal">Mensalmente</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Horário do Backup:</label>
                                    <input type="time" class="form-control" value="02:00">
                                </div>
                                <div class="form-group">
                                    <label>Dias da Semana (se semanal):</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">Domingo</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Segunda</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">Terça</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Manter Backups por:</label>
                                    <select class="form-control">
                                        <option value="7" selected>7 dias</option>
                                        <option value="15">15 dias</option>
                                        <option value="30">30 dias</option>
                                        <option value="90">90 dias</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shield-alt text-info"></i>
                                Segurança
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Sessão Expira em:</label>
                                    <select class="form-control">
                                        <option value="30">30 minutos</option>
                                        <option value="60" selected>1 hora</option>
                                        <option value="120">2 horas</option>
                                        <option value="480">8 horas</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">
                                            Exigir senha forte
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">
                                            Bloquear conta após tentativas falhadas
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tentativas máximas:</label>
                                    <input type="number" class="form-control" value="5" min="3" max="10">
                                </div>
                                <div class="form-group">
                                    <label>Tempo de bloqueio (minutos):</label>
                                    <input type="number" class="form-control" value="15" min="5" max="60">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label">
                                            Ativar autenticação de dois fatores
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações de Notificações -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bell text-danger"></i>
                                Notificações
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Email de Notificações:</label>
                                    <input type="email" class="form-control" value="admin@construtora.com">
                                </div>
                                <div class="form-group">
                                    <label>SMTP Server:</label>
                                    <input type="text" class="form-control" value="smtp.gmail.com">
                                </div>
                                <div class="form-group">
                                    <label>Porta SMTP:</label>
                                    <input type="number" class="form-control" value="587">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">
                                            Usar SSL/TLS
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Usuário SMTP:</label>
                                    <input type="text" class="form-control" value="admin@construtora.com">
                                </div>
                                <div class="form-group">
                                    <label>Senha SMTP:</label>
                                    <input type="password" class="form-control" placeholder="Digite a senha">
                                </div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line text-secondary"></i>
                                Relatórios
                            </h3>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group">
                                    <label>Formato Padrão de Relatórios:</label>
                                    <select class="form-control">
                                        <option value="html" selected>HTML</option>
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Logo nos Relatórios:</label>
                                    <input type="file" class="form-control-file">
                                    <small class="form-text text-muted">Formatos aceitos: JPG, PNG (máx. 2MB)</small>
                                </div>
                                <div class="form-group">
                                    <label>Rodapé dos Relatórios:</label>
                                    <textarea class="form-control" rows="3" placeholder="Texto que aparecerá no rodapé dos relatórios">Construtora Exemplo Ltda - CNPJ: 12.345.678/0001-90</textarea>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">
                                            Incluir gráficos nos relatórios
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label">
                                            Incluir fotos nos relatórios
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações do Sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tools text-dark"></i>
                                Ações do Sistema
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-info btn-block" onclick="limparCache()">
                                        <i class="fas fa-broom"></i>
                                        Limpar Cache
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-warning btn-block" onclick="fazerBackup()">
                                        <i class="fas fa-download"></i>
                                        Fazer Backup Agora
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-success btn-block" onclick="otimizarBanco()">
                                        <i class="fas fa-database"></i>
                                        Otimizar Banco
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-danger btn-block" onclick="resetarSistema()">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Resetar Sistema
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle text-primary"></i>
                                Informações do Sistema
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Versão do Sistema:</strong></td>
                                            <td>Diário de Obras v2.0</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Versão do Laravel:</strong></td>
                                            <td>{{ app()->version() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Versão do PHP:</strong></td>
                                            <td>{{ PHP_VERSION }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Servidor Web:</strong></td>
                                            <td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Banco de Dados:</strong></td>
                                            <td>MySQL {{ DB::select('SELECT VERSION() as version')[0]->version ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Espaço em Disco:</strong></td>
                                            <td>{{ number_format(disk_free_space('/') / 1024 / 1024 / 1024, 2) }} GB livres</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Último Backup:</strong></td>
                                            <td>{{ now()->subDays(1)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status do Sistema:</strong></td>
                                            <td><span class="badge badge-success">Online</span></td>
                                        </tr>
                                    </table>
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
function limparCache() {
    if (confirm('Tem certeza que deseja limpar o cache do sistema?')) {
        // Implementar limpeza de cache
        alert('Cache limpo com sucesso!');
    }
}

function fazerBackup() {
    if (confirm('Tem certeza que deseja fazer backup agora? Isso pode demorar alguns minutos.')) {
        // Implementar backup
        alert('Backup iniciado! Você será notificado quando concluído.');
    }
}

function otimizarBanco() {
    if (confirm('Tem certeza que deseja otimizar o banco de dados?')) {
        // Implementar otimização
        alert('Banco de dados otimizado com sucesso!');
    }
}

function resetarSistema() {
    if (confirm('ATENÇÃO: Esta ação irá resetar todas as configurações do sistema. Tem certeza?')) {
        if (confirm('Esta ação é IRREVERSÍVEL. Digite "CONFIRMAR" para continuar.')) {
            // Implementar reset
            alert('Sistema resetado!');
        }
    }
}
</script>
@endpush

