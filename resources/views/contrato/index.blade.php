@extends('layouts.admin')

@section('title', 'Contratos')
@section('page-title', 'Lista de Contratos')

@section('breadcrumb')
<li class="breadcrumb-item active">Contratos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    @if($showDeleted)
                        Contratos Excluídos (Histórico)
                    @else
                        Contratos Cadastrados
                    @endif
                </h3>
                <div class="card-tools">
                    @if($showDeleted)
                        <a href="{{ route('contrato.index') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye"></i> Ver Ativos
                        </a>
                    @else
                        <a href="{{ route('contrato.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Contrato
                        </a>
                        @if($deletedCount > 0)
                            <a href="{{ route('contrato.index', ['show_deleted' => true]) }}" class="btn btn-warning btn-sm">
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

                @if($contratos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Número</th>
                                    <th>Descrição</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Gestor</th>
                                    <th>Fiscal</th>
                                    <th>Status</th>
                                    <th style="width: 150px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contratos as $item)
                                    <tr class="{{ $item->trashed() ? 'table-danger' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->numero }}
                                            @if($item->trashed())
                                                <span class="badge badge-danger ml-2">Excluído</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($item->descricao, 50) }}</td>
                                        <td>{{ $item->data_inicio->format('d/m/Y') }}</td>
                                        <td>{{ $item->data_fim->format('d/m/Y') }}</td>
                                        <td>
                                            @if($item->gestor_atual)
                                                <span class="badge badge-success">{{ $item->gestor_atual->nome }}</span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->fiscal_atual)
                                                <span class="badge badge-success">{{ $item->fiscal_atual->nome }}</span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->trashed())
                                                <span class="badge badge-secondary">
                                                    {{ $item->status_formatado }}
                                                </span>
                                            @else
                                                @if($item->status === 'ativo')
                                                    <span class="badge badge-success">Ativo</span>
                                                @elseif($item->status === 'inativo')
                                                    <span class="badge badge-secondary">Inativo</span>
                                                @elseif($item->status === 'vencido')
                                                    <span class="badge badge-danger">Vencido</span>
                                                @elseif($item->status === 'suspenso')
                                                    <span class="badge badge-warning">Suspenso</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->trashed())
                                                {{-- Contrato excluído - apenas visualizar e restaurar --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('contrato.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('contrato.restore', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Restaurar contrato"
                                                                onclick="return confirm('Tem certeza que deseja restaurar este contrato?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Contrato ativo - mostrar opções normais --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('contrato.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('contrato.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('contrato.destroy', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir (Soft Delete)"
                                                                onclick="return confirm('Tem certeza que deseja excluir este contrato? Ele será movido para o histórico.')">
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
                        {!! $contratos->appends(['search' => Request::get('search')])->render() !!}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i>
                                @if($showDeleted)
                                    Nenhum contrato excluído encontrado!
                                @else
                                    Nenhum contrato encontrado!
                                @endif
                            </h5>
                            @if($showDeleted)
                                Não há contratos no histórico de exclusões.
                            @else
                                Não há contratos cadastrados no sistema.
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
