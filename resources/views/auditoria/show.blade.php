@extends('layouts.admin')

@section('page-title', 'Detalhes da Auditoria')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auditoria.index') }}">Auditoria</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i>
                        Detalhes da Auditoria
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('auditoria.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Data/Hora:</strong></label>
                                <p class="form-control-plaintext">{{ $auditoria->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Modelo:</strong></label>
                                <p class="form-control-plaintext">
                                    <span class="badge badge-info">{{ $auditoria->modelo_formatado }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Ação:</strong></label>
                                <p class="form-control-plaintext">
                                    @switch($auditoria->acao)
                                        @case('created')
                                            <span class="badge badge-success">Criado</span>
                                            @break
                                        @case('updated')
                                            <span class="badge badge-warning">Atualizado</span>
                                            @break
                                        @case('deleted')
                                            <span class="badge badge-danger">Excluído</span>
                                            @break
                                        @case('restored')
                                            <span class="badge badge-primary">Restaurado</span>
                                            @break
                                        @case('manager_changed')
                                            <span class="badge badge-secondary">Responsável Alterado</span>
                                            @break
                                        @default
                                            <span class="badge badge-light">{{ ucfirst($auditoria->acao) }}</span>
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Usuário:</strong></label>
                                <p class="form-control-plaintext">
                                    @if($auditoria->usuario)
                                        {{ $auditoria->usuario->name }} ({{ $auditoria->usuario->email }})
                                    @else
                                        <span class="text-muted">Sistema</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>IP Address:</strong></label>
                                <p class="form-control-plaintext">{{ $auditoria->ip_address ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>User Agent:</strong></label>
                                <p class="form-control-plaintext">{{ $auditoria->user_agent ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label><strong>Observações:</strong></label>
                                @if($auditoria->observacoes)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        {{ $auditoria->observacoes }}
                                    </div>
                                @else
                                    <p class="form-control-plaintext text-muted">
                                        <i class="fas fa-minus"></i> Nenhuma observação registrada
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dados Anteriores -->
                    @if($auditoria->dados_anteriores)
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-history"></i>
                                            Dados Anteriores
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($auditoria->dados_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Dados Novos -->
                    @if($auditoria->dados_novos)
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-plus"></i>
                                            Dados Novos
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($auditoria->dados_novos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



