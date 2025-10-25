@extends('layouts.admin')

@section('title', 'Pessoas')
@section('page-title', 'Lista de Pessoas')

@section('breadcrumb')
<li class="breadcrumb-item active">Pessoas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if($showDeleted)
                                Pessoas Excluídas (Histórico)
                            @else
                                Pessoas Cadastradas
                            @endif
                        </h3>
                        <div class="card-tools">
                            @if($showDeleted)
                                <a href="{{ route('pessoa.index') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-eye"></i> Ver Ativas
                                </a>
                            @else
                                <a href="{{ route('pessoa.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Nova Pessoa
                                </a>
                                @if($deletedCount > 0)
                                    <a href="{{ route('pessoa.index', ['show_deleted' => true]) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-history"></i> Ver Excluídas ({{ $deletedCount }})
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

                        @if($pessoas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Lotação</th>
                                    <th>Status</th>
                                    <th>Validação</th>
                                    <th style="width: 200px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pessoas as $item)
                                    <tr class="{{ $item->trashed() ? 'table-danger' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->nome }}
                                            @if($item->trashed())
                                                <span class="badge badge-danger ml-2">Excluído</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                Criado: {{ $item->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>{{ $item->cpf_formatado }}</td>
                                        <td>
                                            @if($item->lotacao)
                                                <span class="badge badge-info">{{ $item->lotacao->nome }}</span>
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->trashed())
                                                <span class="badge badge-secondary">
                                                    {{ $item->status_formatado }}
                                                </span>
                                            @elseif($item->status === 'ativo')
                                                <span class="badge badge-success">Ativo</span>
                                            @elseif($item->status === 'pendente')
                                                <span class="badge badge-warning">Pendente</span>
                                            @else
                                                <span class="badge badge-danger">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status_validacao === 'validado')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Validado
                                                </span>
                                                @if($item->data_validacao)
                                                    <br><small class="text-muted">{{ $item->data_validacao ? $item->data_validacao->format('d/m/Y') : '' }}</small>
                                                @endif
                                            @elseif($item->status_validacao === 'pendente')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> Pendente
                                                </span>
                                                @if($item->tentativas_validacao > 0)
                                                    <br><small class="text-muted">{{ $item->tentativas_validacao }} tentativa(s)</small>
                                                @endif
                                            @elseif($item->status_validacao === 'rejeitado')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times"></i> Rejeitado
                                                </span>
                                                @if($item->data_validacao)
                                                    <br><small class="text-muted">{{ $item->data_validacao ? $item->data_validacao->format('d/m/Y') : '' }}</small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->trashed())
                                                {{-- Pessoa excluída - apenas visualizar e restaurar --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pessoa.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('pessoa.restore', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Restaurar pessoa"
                                                                onclick="return confirm('Tem certeza que deseja restaurar esta pessoa?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- Pessoa ativa - mostrar opções normais --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pessoa.show', $item->id) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pessoa.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($item->status_validacao === 'pendente')
                                                        <form method="POST" action="{{ route('pessoa.revalidar', $item->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary btn-sm" title="Revalidar CPF"
                                                                    onclick="return confirm('Deseja tentar revalidar este CPF na Receita Federal?')">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form method="POST" action="{{ route('pessoa.destroy', $item->id) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir (Soft Delete)"
                                                                onclick="return confirm('Tem certeza que deseja excluir esta pessoa? Ela será movida para o histórico.')">
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
                        {!! $pessoas->appends(['search' => Request::get('search')])->render() !!}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i>
                                @if($showDeleted)
                                    Nenhuma pessoa excluída encontrada!
                                @else
                                    Nenhuma pessoa encontrada!
                                @endif
                            </h5>
                            @if($showDeleted)
                                Não há pessoas no histórico de exclusões.
                            @else
                                Não há pessoas cadastradas no sistema.
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
