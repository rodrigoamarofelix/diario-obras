@extends('layouts.admin')

@section('title', 'Editar Perfil do Usuário')
@section('page-title', 'Editar Perfil do Usuário')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
<li class="breadcrumb-item active">Editar Perfil</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit"></i> Editar Perfil: {{ $user->name }}
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
                        <i class="icon fas fa-ban"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.update-profile', $user) }}">
                    @method('PUT')
                    @csrf

                    <div class="form-group">
                        <label for="name">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="profile">Perfil <span class="text-danger">*</span></label>
                        <select class="form-control" name="profile" id="profile" required>
                            <option value="">Selecione o perfil</option>
                            <option value="user" {{ old('profile', $user->profile) == 'user' ? 'selected' : ''}}>Usuário</option>
                            <option value="visualizador" {{ old('profile', $user->profile) == 'visualizador' ? 'selected' : ''}}>Visualizador/Consultor</option>
                            <option value="construtor" {{ old('profile', $user->profile) == 'construtor' ? 'selected' : ''}}>Construtor/Fornecedor</option>
                            <option value="fiscal" {{ old('profile', $user->profile) == 'fiscal' ? 'selected' : ''}}>Fiscal de Obra</option>
                            <option value="gestor" {{ old('profile', $user->profile) == 'gestor' ? 'selected' : ''}}>Gestor de Contratos</option>
                            <option value="admin" {{ old('profile', $user->profile) == 'admin' ? 'selected' : ''}}>Administrador</option>
                            @if(auth()->user()->isMaster())
                                <option value="master" {{ old('profile', $user->profile) == 'master' ? 'selected' : ''}}>Master</option>
                            @endif
                        </select>
                        @error('profile')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Perfil
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide error message after 8 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.transition = 'opacity 0.5s ease-out';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.remove();
                }, 500);
            }, 8000); // 8 seconds
        }
    });
</script>
@endsection



