@extends('layouts.admin')

@section('page-title', 'Auditoria - Contratos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('auditoria.index') }}">Auditoria</a></li>
    <li class="breadcrumb-item active">Contratos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-contract"></i>
                        Auditoria de Contratos
                    </h3>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('auditoria.contratos') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ação:</label>
                                    <select name="acao" class="form-control">
                                        <option value="">Todas as Ações</option>
                                        @foreach($acoes as $acao)
                                            <option value="{{ $acao }}" {{ request('acao') == $acao ? 'selected' : '' }}>
                                                {{ ucfirst($acao) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Usuário:</label>
                                    <select name="usuario_id" class="form-control">
                                        <option value="">Todos os Usuários</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}" {{ request('usuario_id') == $usuario->id ? 'selected' : '' }}>
                                                {{ $usuario->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Data Início:</label>
                                    <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Data Fim:</label>
                                    <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filtrar
                                        </button>
                                        <a href="{{ route('auditoria.contratos') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Limpar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabela de Auditorias -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Data/Hora</th>
                                    <th>Ação</th>
                                    <th>Usuário</th>
                                    <th>Observações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditorias as $auditoria)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $auditoria->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
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
                                                @default
                                                    <span class="badge badge-light">{{ ucfirst($auditoria->acao) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($auditoria->usuario)
                                                {{ $auditoria->usuario->name }}
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($auditoria->observacoes)
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $auditoria->observacoes }}">
                                                    {{ $auditoria->observacoes }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('auditoria.show', $auditoria->id) }}" class="btn btn-sm btn-info" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Nenhuma auditoria de contratos encontrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    @if($auditorias->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $auditorias->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 500);
    });
}, 3000);
</script>
@endsection



