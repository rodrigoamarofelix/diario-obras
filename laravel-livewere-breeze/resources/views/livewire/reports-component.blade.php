@extends('layouts.admin')

@section('title', 'Relatórios')
@section('page-title', 'Sistema de Relatórios')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Relatórios</li>
@endsection

@section('content')
<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icon fas fa-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon fas fa-ban"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulário de Filtros -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Filtros do Relatório
            </h3>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="generateReport">
                <div class="row">
                    <!-- Tipo de Relatório -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="reportType">Tipo de Relatório:</label>
                            <select wire:model="reportType" class="form-control" id="reportType">
                                <option value="contratos">Contratos</option>
                                <option value="medicoes">Medições</option>
                                <option value="pagamentos">Pagamentos</option>
                                <option value="usuarios">Usuários</option>
                                <option value="auditoria">Auditoria</option>
                                <option value="financeiro">Financeiro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Data Inicial -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dateFrom">Data Inicial:</label>
                            <input type="date" wire:model="dateFrom" class="form-control" id="dateFrom">
                        </div>
                    </div>

                    <!-- Data Final -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dateTo">Data Final:</label>
                            <input type="date" wire:model="dateTo" class="form-control" id="dateTo">
                        </div>
                    </div>

                    <!-- Status (quando aplicável) -->
                    @if(in_array($reportType, ['contratos', 'pagamentos', 'usuarios']))
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select wire:model="status" class="form-control" id="status">
                                <option value="">Todos</option>
                                @if($reportType == 'contratos')
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="vencido">Vencido</option>
                                    <option value="suspenso">Suspenso</option>
                                @elseif($reportType == 'pagamentos')
                                    <option value="pendente">Pendente</option>
                                    <option value="pago">Pago</option>
                                    <option value="cancelado">Cancelado</option>
                                @elseif($reportType == 'usuarios')
                                    <option value="pending">Pendente</option>
                                    <option value="approved">Aprovado</option>
                                    <option value="rejected">Rejeitado</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- Filtros específicos -->
                    @if($reportType == 'medicoes')
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="contractId">Contrato:</label>
                            <select wire:model="contractId" class="form-control" id="contractId">
                                <option value="">Todos</option>
                                @foreach($this->contracts as $contract)
                                    <option value="{{ $contract->id }}">{{ $contract->numero }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    @if($reportType == 'auditoria')
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="userId">Usuário:</label>
                            <select wire:model="userId" class="form-control" id="userId">
                                <option value="">Todos</option>
                                @foreach($this->users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-search"></i> Gerar Relatório
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin"></i> Gerando...
                            </span>
                        </button>

                        <!-- Teste simples -->
                        <button type="button" wire:click="testClick" class="btn btn-success ml-2">
                            <i class="fas fa-test"></i> Teste Simples
                        </button>

                        <!-- Teste ainda mais simples -->
                        <button type="button" onclick="alert('JavaScript funciona!')" class="btn btn-warning ml-2">
                            <i class="fas fa-exclamation"></i> Teste JS
                        </button>

                        <!-- Teste Livewire direto -->
                        <button type="button" onclick="console.log('Livewire:', window.Livewire); alert('Livewire: ' + (window.Livewire ? 'Carregado' : 'Não carregado'))" class="btn btn-info ml-2">
                            <i class="fas fa-info"></i> Teste Livewire
                        </button>

                        <!-- Teste fora do formulário -->
                        <button type="button" wire:click="testClick" class="btn btn-secondary ml-2" style="margin-top: 10px;">
                            <i class="fas fa-external-link-alt"></i> Teste Fora Form
                        </button>

                        <!-- Teste com console.log -->
                        <button type="button" onclick="console.log('Testando...'); window.Livewire.find('{{ $this->getId() }}').call('testClick')" class="btn btn-dark ml-2" style="margin-top: 10px;">
                            <i class="fas fa-bug"></i> Teste Console
                        </button>

                        <!-- Debug Info -->
                        <div class="mt-2">
                            <small class="text-muted">
                                Debug: Loading={{ $loading ? 'true' : 'false' }},
                                ShowResults={{ $showResults ? 'true' : 'false' }},
                                Results={{ !empty($results) ? 'has_data' : 'empty' }}
                            </small>
                        </div>

                        <!-- CSRF Token Debug -->
                        <div class="mt-1">
                            <small class="text-muted">
                                CSRF: {{ csrf_token() }}
                            </small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados do Relatório -->
    @if($showResults && !empty($results))
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Resultado do Teste
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> Livewire Funcionando!</h5>
                <p><strong>Mensagem:</strong> {{ $results['test'] }}</p>
                <p><strong>Tipo de Relatório:</strong> {{ $results['reportType'] }}</p>
                <p><strong>Data Inicial:</strong> {{ $results['dateFrom'] }}</p>
                <p><strong>Data Final:</strong> {{ $results['dateTo'] }}</p>
                <p><strong>Timestamp:</strong> {{ $results['timestamp'] }}</p>
            </div>

            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Debug Info</h5>
                <pre>{{ json_encode($results, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection