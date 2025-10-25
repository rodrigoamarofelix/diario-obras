<div>
    <!-- Barra de Busca Principal -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-search"></i> 🔍 Busca Avançada
            </h3>
            <div class="card-tools">
                <button class="btn btn-tool" wire:click="toggleFilters">
                    <i class="fas fa-filter"></i> Filtros
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Campo de Busca -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            placeholder="Digite o termo de busca..."
                            wire:model.live="searchTerm"
                        >
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-control form-control-lg" wire:model.live="searchType">
                        <option value="all">🔍 Buscar em Tudo</option>
                        <option value="contratos">📄 Contratos</option>
                        <option value="medicoes">📊 Medições</option>
                        <option value="pagamentos">💰 Pagamentos</option>
                        <option value="pessoas">👥 Pessoas</option>
                        <option value="usuarios">👤 Usuários</option>
                    </select>
                </div>
            </div>

            <!-- Filtros Avançados -->
            @if($showFilters)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-sliders-h"></i> Filtros Avançados
                        </h5>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">
                                <i class="fas fa-times"></i> Limpar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Status -->
                            <div class="col-md-3 mb-3">
                                <label>Status:</label>
                                <select class="form-control" wire:model.live="filters.status">
                                    <option value="">Todos</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="aprovado">Aprovado</option>
                                    <option value="rejeitado">Rejeitado</option>
                                </select>
                            </div>

                            <!-- Data Início -->
                            <div class="col-md-3 mb-3">
                                <label>Data Início:</label>
                                <input type="date" class="form-control" wire:model.live="filters.date_from">
                            </div>

                            <!-- Data Fim -->
                            <div class="col-md-3 mb-3">
                                <label>Data Fim:</label>
                                <input type="date" class="form-control" wire:model.live="filters.date_to">
                            </div>

                            <!-- Valor Mínimo -->
                            <div class="col-md-3 mb-3">
                                <label>Valor Mínimo:</label>
                                <input type="number" step="0.01" class="form-control" wire:model.live="filters.valor_min" placeholder="0.00">
                            </div>

                            <!-- Valor Máximo -->
                            <div class="col-md-3 mb-3">
                                <label>Valor Máximo:</label>
                                <input type="number" step="0.01" class="form-control" wire:model.live="filters.valor_max" placeholder="999999.99">
                            </div>

                            <!-- Usuário -->
                            <div class="col-md-3 mb-3">
                                <label>Usuário:</label>
                                <select class="form-control" wire:model.live="filters.user_id">
                                    <option value="">Todos</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Lotação -->
                            <div class="col-md-3 mb-3">
                                <label>Lotação:</label>
                                <select class="form-control" wire:model.live="filters.lotacao_id">
                                    <option value="">Todas</option>
                                    @foreach(\App\Models\Lotacao::all() as $lotacao)
                                        <option value="{{ $lotacao->id }}">{{ $lotacao->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filtros Salvos -->
                        @if(count($savedFilters) > 0)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <label>Filtros Salvos:</label>
                                    <div class="btn-group" role="group">
                                        @foreach($savedFilters as $name => $filter)
                                            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="loadFilter('{{ $name }}')">
                                                {{ $name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Resultados -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Resultados da Busca
                @if($totalResults > 0)
                    <span class="badge badge-primary ml-2">{{ $totalResults }} encontrado(s)</span>
                @endif
            </h3>
        </div>
        <div class="card-body">
            @if($totalResults > 0)
                @if($searchType === 'all')
                    <!-- Resultados Mistos -->
                    <div class="row">
                        @foreach($results as $result)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="card-title">
                                                    @switch($result['type'])
                                                        @case('contrato')
                                                            <i class="fas fa-file-contract text-primary"></i>
                                                            @break
                                                        @case('medicao')
                                                            <i class="fas fa-chart-line text-success"></i>
                                                            @break
                                                        @case('pagamento')
                                                            <i class="fas fa-money-bill-wave text-warning"></i>
                                                            @break
                                                        @case('pessoa')
                                                            <i class="fas fa-user text-info"></i>
                                                            @break
                                                        @case('usuario')
                                                            <i class="fas fa-user-circle text-secondary"></i>
                                                            @break
                                                    @endswitch
                                                    {{ $result['title'] }}
                                                </h6>
                                                <p class="card-text text-muted">{{ $result['description'] }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($result['date'])->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            <div>
                                                <span class="badge badge-{{ $result['status'] === 'ativo' ? 'success' : ($result['status'] === 'pendente' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($result['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ $result['url'] }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Ver Detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Resultados Específicos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nome/Número</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $item)
                                    <tr>
                                        <td>
                                            @switch($searchType)
                                                @case('contratos')
                                                    {{ $item->numero_contrato }}
                                                    @break
                                                @case('medicoes')
                                                    {{ $item->numero_medicao }}
                                                    @break
                                                @case('pagamentos')
                                                    {{ $item->numero_pagamento }}
                                                    @break
                                                @case('pessoas')
                                                    {{ $item->nome }}
                                                    @break
                                                @case('usuarios')
                                                    {{ $item->name }}
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $item->status === 'ativo' ? 'success' : ($item->status === 'pendente' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($item->status ?? $item->approval_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($searchType)
                                                @case('contratos')
                                                    {{ $item->data_inicio ? \Carbon\Carbon::parse($item->data_inicio)->format('d/m/Y') : '-' }}
                                                    @break
                                                @case('medicoes')
                                                    {{ $item->data_medicao ? \Carbon\Carbon::parse($item->data_medicao)->format('d/m/Y') : '-' }}
                                                    @break
                                                @case('pagamentos')
                                                    {{ $item->data_pagamento ? \Carbon\Carbon::parse($item->data_pagamento)->format('d/m/Y') : '-' }}
                                                    @break
                                                @case('pessoas')
                                                    {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '-' }}
                                                    @break
                                                @case('usuarios')
                                                    {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '-' }}
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($searchType)
                                                @case('medicoes')
                                                    R$ {{ number_format($item->valor_total, 2, ',', '.') }}
                                                    @break
                                                @case('pagamentos')
                                                    R$ {{ number_format($item->valor_pago, 2, ',', '.') }}
                                                    @break
                                                @default
                                                    -
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($searchType)
                                                @case('contratos')
                                                    <a href="{{ route('contrato.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @break
                                                @case('medicoes')
                                                    <a href="{{ route('medicao.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @break
                                                @case('pagamentos')
                                                    <a href="{{ route('pagamento.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @break
                                                @case('pessoas')
                                                    <a href="{{ route('pessoa.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @break
                                                @case('usuarios')
                                                    <a href="{{ route('users.show', $item->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Paginação -->
                @if($totalResults > $perPage && $searchType !== 'all')
                    <div class="d-flex justify-content-center mt-3">
                        {{ $results->links() }}
                    </div>
                @endif
            @elseif(!empty($searchTerm) || !empty(array_filter($filters)))
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>Nenhum resultado encontrado</h5>
                    <p>Tente ajustar os termos de busca ou filtros.</p>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h5>Digite um termo de busca</h5>
                    <p>Use a barra de busca acima para encontrar contratos, medições, pagamentos e mais.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Histórico de Buscas -->
    @if(count($searchHistory) > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-history"></i> Histórico de Buscas
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach(array_slice($searchHistory, 0, 5) as $history)
                        <div class="col-md-6 mb-2">
                            <div class="card card-outline">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">{{ $history['term'] }}</small>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($history['type']) }}</small>
                                        </div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($history['timestamp'])->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
