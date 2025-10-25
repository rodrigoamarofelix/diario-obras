@extends('layouts.admin')

@section('title', 'Gerenciar Lotações')
@section('page-title', 'Gerenciar Lotações')

@section('breadcrumb')
<li class="breadcrumb-item active">Lotações</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Lotações</h3>
                <div class="card-tools">
                    <a href="{{ route('lotacao.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nova Lotação
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
                        <i class="icon fas fa-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th style="width: 100px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lotacoes as $lotacao)
                                <tr class="{{ $lotacao->trashed() ? 'table-danger' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $lotacao->nome }}
                                        @if($lotacao->trashed())
                                            <span class="badge badge-danger ml-2">Excluído</span>
                                        @endif
                                    </td>
                                    <td>{{ $lotacao->descricao ?? '-' }}</td>
                                    <td>
                                        @if($lotacao->trashed())
                                            <span class="badge badge-secondary">
                                                {{ $lotacao->status_name }}
                                            </span>
                                        @else
                                            <span class="badge {{ $lotacao->status === 'ativo' ? 'badge-success' : 'badge-danger' }}">
                                                {{ $lotacao->status_name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lotacao->trashed())
                                            {{-- Lotação excluída - mostrar opções de restauração/exclusão permanente --}}
                                            <div class="btn-group" role="group">
                                                <form method="POST" action="{{ route('lotacao.restore', $lotacao->id) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm" title="Restaurar lotação">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('lotacao.force-delete', $lotacao->id) }}" class="d-inline"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir PERMANENTEMENTE esta lotação? Esta ação não pode ser desfeita!')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir permanentemente">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            {{-- Lotação ativa - mostrar opções normais --}}
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('lotacao.show', $lotacao) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('lotacao.edit', $lotacao) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('lotacao.destroy', $lotacao) }}" class="d-inline"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir esta lotação?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Nenhuma lotação encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide success message after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.transition = 'opacity 0.5s ease-out';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.remove();
                }, 500);
            }, 5000); // 5 seconds
        }
    });
</script>
@endsection
