@extends('layouts.admin')

@section('title', 'Detalhes da Função - Sistema')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-briefcase text-warning"></i>
                        Detalhes da Função
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('funcoes.index') }}">Funções</a></li>
                        <li class="breadcrumb-item active">{{ $funcao->nome }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <!-- Informações principais -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">
                                        <i class="fas fa-briefcase text-warning"></i>
                                        {{ $funcao->nome }}
                                    </h3>
                                </div>
                                <div class="col-md-6 text-right">
                                    @if($funcao->trashed())
                                        <span class="badge badge-danger badge-lg">Função Excluída</span>
                                    @else
                                        <span class="badge badge-{{ $funcao->ativo ? 'success' : 'secondary' }} badge-lg">
                                            {{ $funcao->ativo ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Informações básicas -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-tag text-primary"></i> Categoria</h5>
                                    <p class="text-muted">
                                        <span class="badge badge-info badge-lg">{{ ucfirst($funcao->categoria) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-users text-success"></i> Pessoas Associadas</h5>
                                    <p class="text-muted">
                                        <span class="badge badge-primary badge-lg">{{ $funcao->pessoas->count() }} pessoa(s)</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Descrição -->
                            @if($funcao->descricao)
                            <div class="mb-4">
                                <h5><i class="fas fa-info-circle text-info"></i> Descrição</h5>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0">{{ $funcao->descricao }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Datas -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-calendar-plus text-success"></i> Data de Criação</h5>
                                    <p class="text-muted">{{ $funcao->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                @if($funcao->updated_at != $funcao->created_at)
                                <div class="col-md-6">
                                    <h5><i class="fas fa-calendar-edit text-warning"></i> Última Atualização</h5>
                                    <p class="text-muted">{{ $funcao->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($funcao->trashed())
                                        <!-- Função excluída -->
                                        <form method="POST" action="{{ route('funcoes.restore', $funcao->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success"
                                                    onclick="return confirm('Tem certeza que deseja restaurar esta função?')">
                                                <i class="fas fa-undo"></i>
                                                Restaurar Função
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('funcoes.force-delete', $funcao->id) }}" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir PERMANENTEMENTE esta função? Esta ação não pode ser desfeita!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                                Excluir Permanentemente
                                            </button>
                                        </form>
                                    @else
                                        <!-- Função ativa -->
                                        <a href="{{ route('funcoes.edit', $funcao) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                            Editar Função
                                        </a>
                                        <form action="{{ route('funcoes.toggle-status', $funcao) }}" method="GET" class="d-inline">
                                            <button type="submit" class="btn btn-{{ $funcao->ativo ? 'secondary' : 'success' }}"
                                                    onclick="return confirm('Tem certeza que deseja {{ $funcao->ativo ? 'inativar' : 'ativar' }} esta função?')">
                                                <i class="fas fa-{{ $funcao->ativo ? 'pause' : 'play' }}"></i>
                                                {{ $funcao->ativo ? 'Inativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('funcoes.destroy', $funcao) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta função? Ela poderá ser restaurada posteriormente.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                                Excluir Função
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('funcoes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Voltar para Lista
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar com estatísticas -->
                <div class="col-md-4">
                    <!-- Estatísticas -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar text-primary"></i>
                                Estatísticas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <div class="bg-primary text-white p-3 rounded">
                                        <h3 class="mb-0">{{ $funcao->pessoas->count() }}</h3>
                                        <small>Pessoas com esta função</small>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="bg-success text-white p-2 rounded">
                                        <h5 class="mb-0">{{ $funcao->pessoas->where('ativo', true)->count() }}</h5>
                                        <small>Ativas</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-secondary text-white p-2 rounded">
                                        <h5 class="mb-0">{{ $funcao->pessoas->where('ativo', false)->count() }}</h5>
                                        <small>Inativas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pessoas com esta função -->
                    @if($funcao->pessoas->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users text-success"></i>
                                Pessoas com esta Função
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($funcao->pessoas->take(5) as $pessoa)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $pessoa->nome }}</h6>
                                            <small class="text-muted">{{ $pessoa->cpf }}</small>
                                        </div>
                                        <span class="badge badge-{{ $pessoa->ativo ? 'success' : 'secondary' }}">
                                            {{ $pessoa->ativo ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach

                                @if($funcao->pessoas->count() > 5)
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        E mais {{ $funcao->pessoas->count() - 5 }} pessoa(s)...
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Informações técnicas -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog text-info"></i>
                                Informações Técnicas
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $funcao->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Slug:</strong></td>
                                    <td>{{ Str::slug($funcao->nome) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Criado por:</strong></td>
                                    <td>Sistema</td>
                                </tr>
                                @if($funcao->trashed())
                                <tr>
                                    <td><strong>Excluída em:</strong></td>
                                    <td>{{ $funcao->deleted_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmação para ações destrutivas
    const deleteButtons = document.querySelectorAll('form[action*="destroy"], form[action*="force-delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('submit', function(e) {
            const action = this.action.includes('force-delete') ? 'excluir permanentemente' : 'excluir';
            if (!confirm(`Tem certeza que deseja ${action} esta função?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush



