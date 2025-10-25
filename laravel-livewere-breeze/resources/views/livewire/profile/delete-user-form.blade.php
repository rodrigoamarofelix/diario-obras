<?php

use Livewire\Volt\Component;

new class extends Component
{
    // Componente apenas informativo - exclusão desabilitada para usuários comuns
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Excluir Conta') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Para excluir sua conta, entre em contato com um administrador do sistema.') }}
        </p>
    </header>

    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    {{ __('Acesso Restrito') }}
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>{{ __('Apenas administradores podem excluir contas de usuários.') }}</p>
                    <p class="mt-1">{{ __('Se você precisa excluir sua conta, entre em contato com um administrador.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
