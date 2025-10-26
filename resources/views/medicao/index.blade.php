@extends('layouts.admin')

@section('title', 'Medições')
@section('page-title', 'Lista de Medições')

@section('breadcrumb')
<li class="breadcrumb-item active">Medições</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if($showDeleted)
                        Medições Excluídas (Histórico)
                    @else
                        Medições Cadastradas
                    @endif
                </h3>
                <div class="card-tools">
                    <div class="btn-group" role="group">
                        @if($showDeleted)
                            <a href="{{ route('medicao.index') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i> <span class="d-none d-sm-inline">Ver Ativas</span>
                            </a>
                        @else
                            <a href="{{ route('medicao.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Nova Medição</span>
                            </a>
                            @if($deletedCount > 0)
                                <a href="{{ route('medicao.index', ['show_deleted' => true]) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-history"></i> <span class="d-none d-sm-inline">Ver Excluídas</span>
                                    <span class="badge badge-light ml-1">{{ $deletedCount }}</span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
                        <i class="icon fas fa-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
                        <i class="icon fas fa-ban"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($medicoes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Número</th>
                                    <th>Data</th>
                                    <th>Catálogo</th>
                                    <th>Contrato</th>
                                    <th>Lotação</th>
                                    <th>Quantidade</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th style="width: 150px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medicoes as $item)
                                    <tr class="{{ $item->trashed() ? 'table-danger' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->numero_medicao }}
                                            @if($item->trashed())
                                                <span class="badge badge-danger ml-2">Excluído</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                Criado: {{ is_object($item->created_at) ? $item->created_at->format('d/m/Y H:i') : ($item->created_at ?? 'N/A') }}
                                            </small>
                                        </td>
                                        <td>{{ is_object($item->data_medicao) ? $item->data_medicao->format('d/m/Y') : ($item->data_medicao ?? 'N/A') }}</td>
                                        <td>{{ $item->catalogo->nome }}</td>
                                        <td>{{ $item->contrato->numero }}</td>
                                        <td>{{ $item->lotacao->nome }}</td>
                                        <td>{{ number_format($item->quantidade, 3, ',', '.') }} {{ $item->catalogo->unidade_medida }}</td>
                                        <td>{{ $item->valor_total_formatado }}</td>
                                        <td>
                                            @if($item->trashed())
                                                <span class="badge badge-secondary">
                                                    {{ $item->status_name }}
                                                </span>
                                            @else
                                                <span class="badge badge-{{ $item->status == 'aprovado' ? 'success' : ($item->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                                    {{ $item->status_name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->trashed())
                                                {{-- Medição excluída - apenas visualizar e restaurar --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('medicao.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('medicao.restore', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Restaurar medição"
                                                                onclick="return confirm('Tem certeza que deseja restaurar esta medição?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Medição ativa - mostrar opções normais --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('medicao.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('medicao.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($item->status !== 'aprovado')
                                                        <button class="btn btn-primary btn-sm"
                                                                onclick="criarWorkflowMedicao({{ $item->id }})"
                                                                title="Solicitar Aprovação">
                                                            <i class="fas fa-tasks"></i>
                                                        </button>
                                                    @endif
                                                    <form method="POST" action="{{ route('medicao.destroy', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir (Soft Delete)"
                                                                onclick="return confirm('Tem certeza que deseja excluir esta medição? Ela será movida para o histórico.')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {!! $medicoes->appends(['search' => Request::get('search')])->render() !!}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i>
                                @if($showDeleted)
                                    Nenhuma medição excluída encontrada!
                                @else
                                    Nenhuma medição encontrada!
                                @endif
                            </h5>
                            @if($showDeleted)
                                Não há medições no histórico de exclusões.
                            @else
                                Não há medições cadastradas no sistema.
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        if (successMessage) {
            setTimeout(function() {
                successMessage.style.transition = 'opacity 0.5s ease-out';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 500);
            }, 5000);
        }

        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.transition = 'opacity 0.5s ease-out';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.remove();
                }, 500);
            }, 5000);
        }
    });

    // Função para criar workflow de medição
    function criarWorkflowMedicao(medicaoId) {
        if (confirm('Deseja solicitar aprovação para esta medição?')) {
            fetch('/workflow/criar-medicao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    medicao_id: medicaoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Workflow criado com sucesso! Aprovação solicitada.');
                    location.reload();
                } else {
                    alert('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao criar workflow. Tente novamente.');
            });
        }
    }
</script>
@endsection
