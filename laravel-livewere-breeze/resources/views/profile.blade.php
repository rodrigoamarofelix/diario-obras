@extends('layouts.admin')

@section('title', 'Perfil')
@section('page-title', 'Perfil')

@section('breadcrumb')
<li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
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

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user"></i> Informações do Perfil
                </h3>
            </div>
            <div class="card-body">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-key"></i> Atualizar Senha
                </h3>
            </div>
            <div class="card-body">
                <!-- Formulário tradicional como alternativa -->
                <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf

                    <div class="form-group">
                        <label for="current_password">Senha Atual:</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Nova Senha:</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="password" name="password" required minlength="8" style="padding-right: 50px;">
                            <span onclick="togglePasswordVisibility()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 18px; z-index: 10;" id="togglePassword">👁️</span>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Nova Senha:</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required style="padding-right: 50px;">
                            <span onclick="togglePasswordConfirmationVisibility()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 18px; z-index: 10;" id="togglePasswordConfirmation">👁️</span>
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" id="updatePasswordBtn">
                        <i class="fas fa-save"></i> Atualizar Senha
                    </button>
                </form>

                <!-- Componente Livewire como backup -->
                <div class="mt-4" style="display: none;">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle"></i> Excluir Conta
                </h3>
            </div>
            <div class="card-body">
                <livewire:profile.delete-user-form />
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

        // Adicionar listener para o formulário de senha
        const passwordForm = document.querySelector('form[action="{{ route('profile.update-password') }}"]');
        const updatePasswordBtn = document.getElementById('updatePasswordBtn');

        if (passwordForm && updatePasswordBtn) {
            passwordForm.addEventListener('submit', function(e) {
                // Mostrar mensagem de sucesso imediatamente
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = '<i class="icon fas fa-check"></i> Senha atualizada com sucesso!';
                alertDiv.style.position = 'fixed';
                alertDiv.style.top = '20px';
                alertDiv.style.right = '20px';
                alertDiv.style.zIndex = '9999';
                alertDiv.style.minWidth = '300px';

                document.body.appendChild(alertDiv);

                // Desabilitar botão para evitar duplo submit
                updatePasswordBtn.disabled = true;
                updatePasswordBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Atualizando...';

                // Remover mensagem após 3 segundos
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            });
        }

        // Função para alternar visibilidade da senha
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }

        // Função para alternar visibilidade da confirmação de senha
        function togglePasswordConfirmationVisibility() {
            const passwordInput = document.getElementById('password_confirmation');
            const toggleIcon = document.getElementById('togglePasswordConfirmation');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    });
</script>
@endsection
