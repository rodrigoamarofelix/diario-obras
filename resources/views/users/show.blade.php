@extends('layouts.admin')

@section('title', 'Visualizar Usuário')
@section('page-title', 'Visualizar Usuário')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
<li class="breadcrumb-item active">Visualizar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i> Visualizar Usuário: {{ $user->name }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" class="form-control bg-light" id="name" name="name"
                                   value="{{ $user->name }}"
                                   readonly disabled>
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control bg-light" id="email" name="email"
                                   value="{{ $user->email }}"
                                   readonly disabled>
                        </div>

                        <div class="form-group">
                            <label for="profile">Perfil</label>
                            <select class="form-control bg-light" name="profile" id="profile" disabled>
                                <option value="user" {{ $user->profile == 'user' ? 'selected' : ''}}>Usuário</option>
                                <option value="admin" {{ $user->profile == 'admin' ? 'selected' : ''}}>Administrador</option>
                                <option value="master" {{ $user->profile == 'master' ? 'selected' : ''}}>Master</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="created_at">Data de Cadastro</label>
                            <input type="text" class="form-control bg-light" id="created_at" name="created_at"
                                   value="{{ $user->created_at->format('d/m/Y H:i') }}"
                                   readonly disabled>
                        </div>

                        @if($user->trashed())
                        <div class="form-group">
                            <label for="deleted_at">Data de Exclusão</label>
                            <input type="text" class="form-control bg-light" id="deleted_at" name="deleted_at"
                                   value="{{ $user->deleted_at->format('d/m/Y H:i') }}"
                                   readonly disabled>
                        </div>
                        @endif

                        <div class="form-group">
                            @if(auth()->user()->canManageUsers())
                                <a href="{{ route('users.edit-profile', $user) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endif
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



