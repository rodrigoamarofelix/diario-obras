<div class="container-fluid">
    <!-- Status do 2FA -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <h4><i class="icon fas fa-info"></i> Status da Autenticação 2FA</h4>
                @if($user->hasTwoFactorEnabled())
                    <div class="row">
                        <div class="col-md-6">
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-check"></i> 2FA Ativado
                            </span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                Ativado em: {{ $user->two_factor_enabled_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                @else
                    <span class="badge badge-warning badge-lg">
                        <i class="fas fa-exclamation-triangle"></i> 2FA Desativado
                    </span>
                    <p class="mt-2 mb-0">Proteja sua conta ativando a autenticação de dois fatores.</p>
                @endif
            </div>
        </div>
    </div>
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$user->hasTwoFactorEnabled())
                <!-- Configuração Inicial -->
                <div class="space-y-6">
                    <!-- Informações sobre 2FA -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">📱 O que é Autenticação de Dois Fatores?</h3>
                        <p class="text-blue-800 text-sm">
                            A autenticação de dois fatores adiciona uma camada extra de segurança à sua conta.
                            Além da sua senha, você precisará de um código gerado pelo seu aplicativo autenticador.
                        </p>
                    </div>

                    <!-- Apps Recomendados -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">📲 Aplicativos Recomendados</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="bg-white rounded-lg p-3 shadow border-2 border-green-200">
                                    <div class="text-2xl mb-2">🔐</div>
                                    <h4 class="font-semibold text-green-800">Authy</h4>
                                    <p class="text-sm text-green-600 font-semibold">GRATUITO</p>
                                    <p class="text-xs text-gray-600">Backup na nuvem</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="bg-white rounded-lg p-3 shadow border-2 border-purple-200">
                                    <div class="text-2xl mb-2">🛡️</div>
                                    <h4 class="font-semibold text-purple-800">Microsoft Authenticator</h4>
                                    <p class="text-sm text-purple-600 font-semibold">GRATUITO</p>
                                    <p class="text-xs text-gray-600">Integração Microsoft</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="bg-white rounded-lg p-3 shadow border-2 border-blue-200">
                                    <div class="text-2xl mb-2">🔓</div>
                                    <h4 class="font-semibold text-blue-800">FreeOTP</h4>
                                    <p class="text-sm text-blue-600 font-semibold">GRATUITO</p>
                                    <p class="text-xs text-gray-600">Open Source</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Recomendação:</strong> Use Authy ou Microsoft Authenticator para melhor experiência.
                            </p>
                        </div>
                    </div>

                    @if(!$showQrCode)
                        <!-- Botão para iniciar configuração -->
                        <div class="text-center">
                            <button
                                wire:click="generateSecret"
                                wire:loading.attr="disabled"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="generateSecret">
                                    🚀 Ativar 2FA
                                </span>
                                <span wire:loading wire:target="generateSecret">
                                    ⏳ Gerando chave...
                                </span>
                            </button>
                        </div>
                    @else
                        <!-- QR Code e Configuração -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📱 Configure seu Aplicativo</h3>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- QR Code -->
                                <div class="text-center">
                                    <div class="bg-white border-2 border-gray-200 rounded-lg p-4 inline-block">
                                        @if($qrCodeUrl)
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
                                                 alt="QR Code"
                                                 class="w-48 h-48 mx-auto"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div style="display:none;" class="w-48 h-48 mx-auto flex items-center justify-center text-gray-400">
                                                <div>
                                                    <i class="fas fa-qrcode text-4xl"></i>
                                                    <p class="text-sm mt-2">QR Code indisponível</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-48 h-48 mx-auto flex items-center justify-center text-gray-400">
                                                <div>
                                                    <i class="fas fa-qrcode text-4xl"></i>
                                                    <p class="text-sm mt-2">QR Code será gerado aqui</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Escaneie este QR Code com seu aplicativo autenticador
                                    </p>
                                </div>

                                <!-- Instruções -->
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">📋 Passos:</h4>
                                        <ol class="text-sm text-gray-700 space-y-1">
                                            <li>1. Abra seu aplicativo autenticador</li>
                                            <li>2. Escaneie o QR Code ao lado</li>
                                            <li>3. Digite o código de 6 dígitos abaixo</li>
                                            <li>4. Clique em "Ativar 2FA"</li>
                                        </ol>
                                    </div>

                                    <!-- Código Manual -->
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">🔑 Chave Manual:</h4>
                                        <div class="bg-white border border-gray-200 rounded p-2 font-mono text-sm break-all">
                                            {{ $secret }}
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Use esta chave se não conseguir escanear o QR Code
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Verificação -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Código de Verificação (6 dígitos)
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        wire:model="verificationCode"
                                        maxlength="6"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-center text-lg font-mono tracking-widest"
                                        placeholder="000000"
                                    >
                                    <button
                                        wire:click="enableTwoFactor"
                                        wire:loading.attr="disabled"
                                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="enableTwoFactor">
                                            ✅ Ativar
                                        </span>
                                        <span wire:loading wire:target="enableTwoFactor">
                                            ⏳ Verificando...
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Cancelar -->
                            <div class="mt-4 text-center">
                                <button
                                    wire:click="cancelSetup"
                                    class="text-gray-500 hover:text-gray-700 text-sm underline"
                                >
                                    Cancelar configuração
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- 2FA Ativado -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="text-green-600 text-2xl mr-3">✅</div>
                            <div>
                                <h3 class="text-lg font-semibold text-green-900">2FA Ativado com Sucesso!</h3>
                                <p class="text-green-800 text-sm">
                                    Sua conta está protegida com autenticação de dois fatores.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Códigos de Backup -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">🔑 Códigos de Backup</h3>
                        <div class="flex gap-2">
                            <button
                                wire:click="testLivewire"
                                class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-3 rounded transition duration-200"
                            >
                                🧪 Teste Livewire
                            </button>
                            <button
                                wire:click="showBackupCodes"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-3 rounded transition duration-200"
                            >
                                Ver Códigos
                            </button>
                            <button
                                wire:click="regenerateBackupCodes"
                                class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold py-2 px-3 rounded transition duration-200"
                            >
                                Regenerar
                            </button>
                        </div>
                        </div>

                        <p class="text-gray-600 text-sm mb-4">
                            Use estes códigos para acessar sua conta caso perca seu dispositivo.
                            Cada código só pode ser usado uma vez.
                        </p>

                        @if($showBackupCodes)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($backupCodes as $code)
                                        <div class="bg-white border border-gray-200 rounded p-2 text-center font-mono text-sm">
                                            {{ $code }}
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    ⚠️ Guarde estes códigos em local seguro!
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Desativar 2FA -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-red-900 mb-2">⚠️ Desativar 2FA</h3>
                        <p class="text-red-800 text-sm mb-4">
                            Desativar o 2FA reduzirá a segurança da sua conta.
                            Certifique-se de que realmente deseja fazer isso.
                        </p>

                        <div class="flex gap-2">
                            <input
                                type="password"
                                wire:model="password"
                                placeholder="Digite sua senha"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2"
                            >
                            <button
                                wire:click="disableTwoFactor"
                                wire:loading.attr="disabled"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="disableTwoFactor">
                                    🗑️ Desativar
                                </span>
                                <span wire:loading wire:target="disableTwoFactor">
                                    ⏳ Desativando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- QR Code Script Simplificado -->
<script>
    // Função simples para debug
    document.addEventListener('livewire:init', () => {
        Livewire.on('secret-generated', () => {
            console.log('Secret gerado, QR Code deve aparecer automaticamente');
        });
    });
</script>
