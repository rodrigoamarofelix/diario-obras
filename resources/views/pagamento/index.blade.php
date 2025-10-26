@extends('layouts.admin')

@section('title', 'Pagamentos')
@section('page-title', 'Lista de Pagamentos')

@section('breadcrumb')
<li class="breadcrumb-item active">Pagamentos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if($showDeleted)
                        Pagamentos Excluídos (Histórico)
                    @else
                        Pagamentos Cadastrados
                    @endif
                </h3>
                <div class="card-tools">
                    @if($showDeleted)
                        <a href="{{ route('pagamento.index') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye"></i> Ver Ativos
                        </a>
                    @else
                        <a href="{{ route('pagamento.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Pagamento
                        </a>
                        @if($deletedCount > 0)
                            <a href="{{ route('pagamento.index', ['show_deleted' => true]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-history"></i> Ver Excluídos ({{ $deletedCount }})
                            </a>
                        @endif
                    @endif
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

                @if($pagamentos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Número</th>
                                    <th>Data</th>
                                    <th>Medição</th>
                                    <th>Catálogo</th>
                                    <th>Contrato</th>
                                    <th>Lotação</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Redmine</th>
                                    <th style="width: 150px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagamentos as $item)
                                    <tr class="{{ $item->trashed() ? 'table-danger' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->numero_pagamento }}
                                            @if($item->trashed())
                                                <span class="badge badge-danger ml-2">Excluído</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                Criado: {{ is_object($item->created_at) ? $item->created_at->format('d/m/Y H:i') : ($item->created_at ?? 'N/A') }}
                                            </small>
                                        </td>
                                        <td>{{ is_object($item->data_pagamento) ? $item->data_pagamento->format('d/m/Y') : ($item->data_pagamento ?? 'N/A') }}</td>
                                        <td>{{ $item->medicao->numero_medicao }}</td>
                                        <td>{{ $item->medicao->catalogo->nome }}</td>
                                        <td>{{ $item->medicao->contrato->numero }}</td>
                                        <td>{{ $item->medicao->lotacao->nome }}</td>
                                        <td>{{ $item->valor_pagamento_formatado }}</td>
                                        <td>
                                            @if($item->trashed())
                                                <span class="badge badge-secondary">
                                                    {{ $item->status_name }}
                                                </span>
                                            @else
                                                <span class="badge badge-{{ $item->status == 'pago' ? 'success' : ($item->status == 'rejeitado' ? 'danger' : 'warning') }}">
                                                    {{ $item->status_name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->tem_documento_redmine)
                                                <a href="{{ $item->link_documento_redmine }}" target="_blank" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->trashed())
                                                {{-- Pagamento excluído - apenas visualizar e restaurar --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pagamento.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('pagamento.restore', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Restaurar pagamento"
                                                                onclick="return confirm('Tem certeza que deseja restaurar este pagamento?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Pagamento ativo - mostrar opções normais --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pagamento.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pagamento.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('pagamento.destroy', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir (Soft Delete)"
                                                                onclick="return confirm('Tem certeza que deseja excluir este pagamento? Ele será movido para o histórico.')">
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
                        {!! $pagamentos->appends(['search' => Request::get('search')])->render() !!}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i>
                                @if($showDeleted)
                                    Nenhum pagamento excluído encontrado!
                                @else
                                    Nenhum pagamento encontrado!
                                @endif
                            </h5>
                            @if($showDeleted)
                                Não há pagamentos no histórico de exclusões.
                            @else
                                Não há pagamentos cadastrados no sistema.
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
</script>
@endsection
