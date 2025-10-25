@extends('layouts.admin')

@section('title', 'Gerenciar Usuários')
@section('page-title', 'Gerenciar Usuários')

@section('breadcrumb')
<li class="breadcrumb-item active">Usuários</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Usuários</h3>
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

                @if($pendingUsers->count() > 0)
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Usuários Pendentes de Aprovação</h5>
                        <p>Existem <strong>{{ $pendingUsers->count() }}</strong> usuário(s) aguardando aprovação.</p>
                        @if(auth()->user()->isMaster())
                            <a href="{{ route('user-approvals.index') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-check-circle"></i> Gerenciar Aprovações
                            </a>
                        @endif
                    </div>
                @endif

                @if(auth()->user()->canManageUsers())
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Perfil</th>
                                    <th>Status</th>
                                    <th style="width: 100px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="{{ $user->trashed() ? 'table-danger' : '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="badge badge-info ml-2">Você</span>
                                            @endif
                                            @if($user->trashed())
                                                <span class="badge badge-danger ml-2">Excluído</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->trashed())
                                                {{-- Usuário excluído - mostrar apenas o perfil --}}
                                                <span class="badge badge-secondary">
                                                    {{ $user->profile_name }}
                                                </span>
                                            @else
                                                {{-- Usuário ativo - mostrar badge colorido --}}
                                                <span class="badge {{ $user->profile === 'master' ? 'badge-warning' : ($user->profile === 'admin' ? 'badge-primary' : 'badge-success') }}">
                                                    {{ $user->profile_name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->trashed())
                                                <span class="badge badge-danger">Excluído</span>
                                            @elseif($user->isPending())
                                                <span class="badge badge-warning">Pendente</span>
                                            @elseif($user->isRejected())
                                                <span class="badge badge-danger">Rejeitado</span>
                                            @else
                                                <span class="badge badge-success">Aprovado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->trashed())
                                                {{-- Usuário excluído - mostrar opções de restauração/exclusão permanente --}}
                                                <div class="btn-group" role="group">
                                                    @if(auth()->user()->canManageUsers())
                                                        <form method="POST" action="{{ route('users.restore', $user->id) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-success btn-sm" title="Restaurar usuário">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if(auth()->user()->canDeleteUsers() && $user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('users.force-delete', $user->id) }}" class="d-inline"
                                                              onsubmit="return confirm('Tem certeza que deseja excluir PERMANENTEMENTE este usuário? Esta ação não pode ser desfeita!')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir permanentemente">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Usuário ativo - mostrar opções normais --}}
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->canManageUsers())
                                                        <a href="{{ route('users.edit-profile', $user) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->canDeleteUsers() && $user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                                                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Nenhum usuário encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Acesso Negado!</h5>
                            Você não tem permissão para gerenciar usuários.
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
