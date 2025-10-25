<?php

namespace App\Livewire;

use App\Services\TwoFactorService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorComponent extends Component
{
    public $user;
    public $secret = '';
    public $backupCodes = [];
    public $qrCodeUrl = '';
    public $verificationCode = '';
    public $password = '';
    public $showBackupCodes = false;
    public $showQrCode = false;
    public $loading = false;

    protected $twoFactorService;

    public function boot(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    /**
     * Gera uma nova chave secreta
     */
    public function generateSecret()
    {
        $this->loading = true;

        try {
            $this->secret = $this->twoFactorService->generateSecret();
            $this->backupCodes = $this->twoFactorService->generateBackupCodes();
            $this->qrCodeUrl = $this->twoFactorService->generateQRCodeUrl(
                $this->user->email,
                $this->secret,
                'SGC - Gestão de Contratos'
            );

            $this->showQrCode = true;
            $this->showBackupCodes = false;

            session([
                'two_factor_setup' => [
                    'secret' => $this->secret,
                    'backup_codes' => $this->backupCodes,
                    'qr_code_url' => $this->qrCodeUrl,
                ]
            ]);

            $this->dispatch('secret-generated');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao gerar chave secreta: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Ativa o 2FA
     */
    public function enableTwoFactor()
    {
        $this->validate([
            'verificationCode' => 'required|string|size:6',
        ], [
            'verificationCode.required' => 'Código de verificação é obrigatório.',
            'verificationCode.size' => 'Código deve ter 6 dígitos.',
        ]);

        $this->loading = true;

        try {
            $setup = session('two_factor_setup');

            if (!$setup) {
                session()->flash('error', 'Sessão expirada. Tente novamente.');
                return;
            }

            // Debug: Log dos valores
            $testData = $this->twoFactorService->testTOTP($setup['secret']);
            \Log::info('Verificando código 2FA', [
                'secret' => $setup['secret'],
                'code' => $this->verificationCode,
                'current_time' => time(),
                'test_data' => $testData,
                'verification_result' => $this->twoFactorService->verifyCode($setup['secret'], $this->verificationCode)
            ]);

            // Verificar o código
            if (!$this->twoFactorService->verifyCode($setup['secret'], $this->verificationCode)) {
                // Mostrar códigos válidos para debug
                $validCodes = [
                    'current' => $testData['current_code'],
                    'previous' => $testData['previous_code'],
                    'next' => $testData['next_code']
                ];

                session()->flash('error', 'Código inválido. Códigos válidos: ' . implode(', ', $validCodes));
                \Log::warning('Código 2FA inválido', [
                    'secret' => $setup['secret'],
                    'code' => $this->verificationCode,
                    'valid_codes' => $validCodes
                ]);
                return;
            }

            // Ativar 2FA
            $this->user->enableTwoFactor($setup['secret'], $setup['backup_codes']);

            // Limpar sessão
            session()->forget('two_factor_setup');

            $this->showBackupCodes = true;
            $this->showQrCode = false;
            $this->verificationCode = '';

            session()->flash('success', '2FA ativado com sucesso!');
            $this->dispatch('two-factor-enabled');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao ativar 2FA: ' . $e->getMessage());
            \Log::error('Erro ao ativar 2FA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Desativa o 2FA
     */
    public function disableTwoFactor()
    {
        $this->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Senha é obrigatória para desativar o 2FA.',
            'password.current_password' => 'Senha incorreta.',
        ]);

        $this->loading = true;

        try {
            // Desativar 2FA
            $this->user->disableTwoFactor();

            $this->password = '';
            $this->showQrCode = false;
            $this->showBackupCodes = false;

            session()->flash('success', '2FA desativado com sucesso!');
            $this->dispatch('two-factor-disabled');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao desativar 2FA: ' . $e->getMessage());
            \Log::error('Erro ao desativar 2FA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Regenera códigos de backup
     */
    public function regenerateBackupCodes()
    {
        $this->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Senha é obrigatória para regenerar códigos.',
        ]);

        $this->loading = true;

        try {
            // Verificar senha
            if (!Hash::check($this->password, $this->user->password)) {
                session()->flash('error', 'Senha incorreta.');
                return;
            }

            // Gerar novos códigos
            $newBackupCodes = $this->twoFactorService->generateBackupCodes();
            $this->user->update(['two_factor_backup_codes' => $newBackupCodes]);
            $this->backupCodes = $newBackupCodes;

            $this->password = '';
            $this->showBackupCodes = true;

            session()->flash('success', 'Códigos de backup regenerados com sucesso!');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao regenerar códigos: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Mostra os códigos de backup
     */
    public function showBackupCodes()
    {
        \Log::info('=== MÉTODO showBackupCodes CHAMADO ===');

        try {
            // Recarregar o usuário do banco para garantir dados atualizados
            $this->user->refresh();

            \Log::info('Debug showBackupCodes - Antes', [
                'user_id' => $this->user->id,
                'two_factor_enabled' => $this->user->two_factor_enabled,
                'two_factor_backup_codes_raw' => $this->user->two_factor_backup_codes,
                'two_factor_backup_codes_type' => gettype($this->user->two_factor_backup_codes),
                'showBackupCodes_before' => $this->showBackupCodes
            ]);

            $this->backupCodes = $this->user->getBackupCodes();
            $this->showBackupCodes = true;

            \Log::info('Debug showBackupCodes - Depois', [
                'user_id' => $this->user->id,
                'backup_codes_count' => count($this->backupCodes),
                'backup_codes' => $this->backupCodes,
                'showBackupCodes_after' => $this->showBackupCodes
            ]);

            session()->flash('success', 'Códigos carregados com sucesso!');

        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao carregar códigos de backup: ' . $e->getMessage());
            \Log::error('Erro ao mostrar códigos de backup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Esconde os códigos de backup
     */
    public function hideBackupCodes()
    {
        $this->showBackupCodes = false;
    }

    /**
     * Cancela a configuração
     */
    public function cancelSetup()
    {
        session()->forget('two_factor_setup');
        $this->showQrCode = false;
        $this->showBackupCodes = false;
        $this->verificationCode = '';
        $this->secret = '';
        $this->backupCodes = [];
        $this->qrCodeUrl = '';
    }

    /**
     * Método de teste para verificar se Livewire está funcionando
     */
    public function testLivewire()
    {
        \Log::info('=== TESTE LIVEWIRE FUNCIONANDO ===');
        session()->flash('success', 'Livewire está funcionando! Teste realizado com sucesso.');
    }

    public function render()
    {
        return view('livewire.two-factor-component');
    }
}
