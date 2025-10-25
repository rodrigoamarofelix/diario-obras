@extends('layouts.admin')

@section('title', 'Aprovação de Usuários')
@section('page-title', 'Aprovação de Usuários')

@section('breadcrumb')
<li class="breadcrumb-item active">Aprovações</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Usuários Pendentes de Aprovação</h3>
                <div class="card-tools">
                    <span class="badge badge-warning">{{ $pendingUsers->count() }} pendente(s)</span>
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

                @if($pendingUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Data de Cadastro</th>
                                    @if(auth()->user()->isMaster())
                                        <th style="width: 150px">Ações</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        @if(auth()->user()->isMaster())
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <form method="POST" action="{{ route('user-approvals.approve', $user) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm" title="Aprovar usuário"
                                                                onclick="return confirm('Tem certeza que deseja aprovar este usuário?')">
                                                            <i class="fas fa-check"></i> Aprovar
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('user-approvals.reject', $user) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Rejeitar usuário"
                                                                onclick="return confirm('Tem certeza que deseja rejeitar este usuário?')">
                                                            <i class="fas fa-times"></i> Rejeitar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(!auth()->user()->isMaster())
                        <div class="alert alert-warning mt-3">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Acesso Limitado!</h5>
                            Você pode visualizar os usuários pendentes, mas apenas usuários Master podem aprovar ou rejeitar contas.
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info-circle"></i> Nenhum usuário pendente!</h5>
                            Não há usuários aguardando aprovação no momento.
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
