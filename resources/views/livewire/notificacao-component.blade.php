<div>
    <div class="dropdown">
        <!-- Botão do sino de notificações -->
        <button class="btn btn-link text-white" type="button" id="notificacaoDropdown"
                wire:click="toggleDropdown">
            <i class="fas fa-bell"></i>
            @if($totalNaoLidas > 0)
                <span class="badge badge-danger badge-sm">{{ $totalNaoLidas }}</span>
            @endif
        </button>

        <!-- Dropdown de notificações -->
        <div class="dropdown-menu dropdown-menu-right {{ $mostrarDropdown ? 'show' : '' }}"
             style="width: 350px; max-height: 400px; overflow-y: auto; {{ $mostrarDropdown ? 'display: block;' : 'display: none;' }}">
            <div class="dropdown-header d-flex justify-content-between align-items-center">
                <span>Notificações</span>
                <div>
                    @if($totalNaoLidas > 0)
                        <button class="btn btn-sm btn-outline-primary" wire:click="marcarTodasComoLidas">
                            <i class="fas fa-check-double"></i> Marcar todas
                        </button>
                    @endif
                    <button class="btn btn-sm btn-outline-info ml-1" wire:click="criarNotificacaoTeste">
                        <i class="fas fa-plus"></i> Teste
                    </button>
                </div>
            </div>

            <div class="dropdown-divider"></div>

            @if($carregando)
                <div class="text-center p-3">
                    <i class="fas fa-spinner fa-spin"></i> Carregando...
                </div>
            @elseif(count($notificacoes) > 0)
                @foreach($notificacoes as $notificacao)
                    <div class="dropdown-item d-flex align-items-start p-3 {{ !$notificacao['lida'] ? 'bg-light' : '' }}"
                         style="border-left: 4px solid {{ $this->getCorNotificacao($notificacao['tipo']) }};">
                        <div class="flex-shrink-0 mr-3">
                            <i class="{{ $notificacao['icone'] }} text-{{ $notificacao['cor'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-1 text-truncate" style="max-width: 200px;">
                                    {{ $notificacao['titulo'] }}
                                </h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if(!$notificacao['lida'])
                                            <a class="dropdown-item" href="#"
                                               wire:click="marcarComoLida({{ $notificacao['id'] }})">
                                                <i class="fas fa-check text-success"></i> Marcar como lida
                                            </a>
                                        @endif
                                        <a class="dropdown-item text-danger" href="#"
                                           wire:click="excluirNotificacao({{ $notificacao['id'] }})"
                                           onclick="return confirm('Deseja excluir esta notificação?')">
                                            <i class="fas fa-trash"></i> Excluir
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-1 text-muted small">{{ Str::limit($notificacao['mensagem'], 100) }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($notificacao['created_at'])->diffForHumans() }}
                            </small>
                            @if(isset($notificacao['dados']['url']) && $notificacao['dados']['url'])
                                <div class="mt-2">
                                    <a href="{{ $notificacao['dados']['url'] }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> Ver detalhes
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="dropdown-divider"></div>
                    @endif
                @endforeach

                <div class="dropdown-divider"></div>
                <div class="text-center p-2">
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list"></i> Ver todas as notificações
                    </a>
                </div>
            @else
                <div class="text-center p-4 text-muted">
                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                    <p class="mb-0">Nenhuma notificação</p>
                    <small>Todas as suas notificações estão em dia!</small>
                </div>
            @endif
        </div>
    </div>

    <style>
    .dropdown-menu {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.15);
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item:focus {
        background-color: #e9ecef;
    }

    .badge-sm {
        font-size: 0.7em;
        padding: 0.25em 0.4em;
    }
    </style>

    <script>
    document.addEventListener('livewire:init', () => {
        // Auto-refresh das notificações a cada 30 segundos
        setInterval(() => {
            if (@this.totalNaoLidas > 0) {
                @this.carregarNotificacoes();
            }
        }, 30000);

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificacaoDropdown');
            const dropdownMenu = dropdown.nextElementSibling;

            // Se clicou fora do dropdown, fechar
            if (!dropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
                @this.mostrarDropdown = false;
            }
        });
    });
    </script>
</div>
