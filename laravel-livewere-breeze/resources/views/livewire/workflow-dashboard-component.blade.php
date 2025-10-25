<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pendentes'] }}</h3>
                    <p>Pendentes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['em_analise'] }}</h3>
                    <p>Em Análise</p>
                </div>
                <div class="icon">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['aprovados_hoje'] }}</h3>
                    <p>Aprovados Hoje</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['urgentes'] }}</h3>
                    <p>Urgentes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $stats['vencidos'] }}</h3>
                    <p>Vencidos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-filter"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-12 mb-3">
                    <label class="form-label">Status:</label>
                    <select class="form-control" wire:model.live="filtroStatus">
                        <option value="">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="em_analise">Em Análise</option>
                        <option value="aprovado">Aprovado</option>
                        <option value="rejeitado">Rejeitado</option>
                    </select>
                </div>
                <div class="col-md-4 col-12 mb-3">
                    <label class="form-label">Tipo:</label>
                    <select class="form-control" wire:model.live="filtroTipo">
                        <option value="">Todos</option>
                        <option value="medicao">Medição</option>
                        <option value="pagamento">Pagamento</option>
                        <option value="contrato">Contrato</option>
                        <option value="usuario">Usuário</option>
                    </select>
                </div>
                <div class="col-md-4 col-12 mb-3">
                    <label class="form-label">Urgente:</label>
                    <select class="form-control" wire:model.live="filtroUrgente">
                        <option value="">Todos</option>
                        <option value="sim">Sim</option>
                        <option value="nao">Não</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Itens para Aprovar -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tasks"></i> Itens para Aprovar
            </h3>
        </div>
        <div class="card-body">
            @if($itensParaAprovar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Item</th>
                                <th>Solicitante</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Prazo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($itensParaAprovar as $item)
                                <tr class="{{ $item->urgente ? 'table-danger' : '' }}">
                                    <td>
                                        <span class="badge badge-{{ $item->tipo === 'medicao' ? 'info' : 'success' }}">
                                            {{ ucfirst($item->tipo) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->tipo === 'medicao')
                                            Medição #{{ $item->model->numero_medicao ?? 'N/A' }}
                                        @elseif($item->tipo === 'pagamento')
                                            Pagamento #{{ $item->model->numero_pagamento ?? 'N/A' }}
                                        @else
                                            {{ $item->tipo }}
                                        @endif
                                    </td>
                                    <td>{{ $item->solicitante->name }}</td>
                                    <td>{{ $item->valor_formatado }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status_cor }}">
                                            {{ $item->status_formatado }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->prazo_aprovacao)
                                            <small class="{{ $item->estaVencido() ? 'text-danger' : 'text-muted' }}">
                                                {{ $item->tempo_restante }}
                                            </small>
                                        @else
                                            <small class="text-muted">Sem prazo</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm d-md-none" role="group">
                                            <button class="btn btn-success mb-1"
                                                    wire:click="aprovar({{ $item->id }})"
                                                    title="Aprovar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-info mb-1"
                                                    wire:click="marcarEmAnalise({{ $item->id }})"
                                                    title="Marcar como Em Análise">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="btn btn-danger"
                                                    onclick="rejeitarItem({{ $item->id }})"
                                                    title="Rejeitar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="btn-group d-none d-md-flex" role="group">
                                            <button class="btn btn-sm btn-success"
                                                    wire:click="aprovar({{ $item->id }})"
                                                    title="Aprovar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info"
                                                    wire:click="marcarEmAnalise({{ $item->id }})"
                                                    title="Marcar como Em Análise">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="rejeitarItem({{ $item->id }})"
                                                    title="Rejeitar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $itensParaAprovar->links() }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h5>Nenhum item pendente</h5>
                    <p>Você não tem itens aguardando aprovação.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Itens Urgentes -->
    @if($itensUrgentes->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-danger">
                <h5 class="card-title text-white">
                    <i class="fas fa-exclamation-triangle"></i> Itens Urgentes
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($itensUrgentes as $item)
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        {{ ucfirst($item->tipo) }} - {{ $item->valor_formatado }}
                                    </h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Solicitado por: {{ $item->solicitante->name }}<br>
                                            Prazo: {{ $item->tempo_restante }}
                                        </small>
                                    </p>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-success"
                                                wire:click="aprovar({{ $item->id }})">
                                            <i class="fas fa-check"></i> Aprovar
                                        </button>
                                        <button class="btn btn-info"
                                                wire:click="marcarEmAnalise({{ $item->id }})">
                                            <i class="fas fa-search"></i> Analisar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Itens Vencidos -->
    @if($itensVencidos->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-secondary">
                <h5 class="card-title text-white">
                    <i class="fas fa-times"></i> Itens Vencidos
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($itensVencidos as $item)
                        <div class="col-md-6 mb-3">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        {{ ucfirst($item->tipo) }} - {{ $item->valor_formatado }}
                                    </h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Solicitado por: {{ $item->solicitante->name }}<br>
                                            Vencido em: {{ $item->prazo_aprovacao->format('d/m/Y H:i') }}
                                        </small>
                                    </p>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-success"
                                                wire:click="aprovar({{ $item->id }})">
                                            <i class="fas fa-check"></i> Aprovar
                                        </button>
                                        <button class="btn btn-info"
                                                wire:click="marcarEmAnalise({{ $item->id }})">
                                            <i class="fas fa-search"></i> Analisar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para Rejeição -->
    <div class="modal fade" id="rejeitarModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rejeitar Item</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Justificativa para rejeição:</label>
                        <textarea class="form-control" id="justificativaRejeicao" rows="4"
                                  placeholder="Digite a justificativa para a rejeição..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarRejeicao">Rejeitar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let itemIdParaRejeitar = null;

    function rejeitarItem(id) {
        itemIdParaRejeitar = id;
        $('#rejeitarModal').modal('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Verificar se o elemento existe antes de adicionar o listener
        const confirmarRejeicao = document.getElementById('confirmarRejeicao');
        if (confirmarRejeicao) {
            confirmarRejeicao.addEventListener('click', function() {
                const justificativa = document.getElementById('justificativaRejeicao').value;

                if (!justificativa.trim()) {
                    alert('Por favor, digite uma justificativa.');
                    return;
                }

                @this.call('rejeitar', itemIdParaRejeitar, justificativa);
                $('#rejeitarModal').modal('hide');
                document.getElementById('justificativaRejeicao').value = '';
            });
        }

        // Debug: Verificar se os botões estão funcionando
        console.log('Workflow Dashboard carregado');
        console.log('Livewire disponível:', typeof @this !== 'undefined');
    });
    </script>
</div>