<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TwoFactorController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Mostra a página de configuração do 2FA
     */
    public function index()
    {
        $user = Auth::user();

        return view('two-factor.index', compact('user'));
    }

    /**
     * Gera uma nova chave secreta para configuração
     */
    public function generateSecret()
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => '2FA já está ativado para este usuário.'
            ]);
        }

        $secret = $this->twoFactorService->generateSecret();
        $backupCodes = $this->twoFactorService->generateBackupCodes();
        $qrCodeUrl = $this->twoFactorService->generateQRCodeUrl(
            $user->email,
            $secret,
            'SGC - Gestão de Contratos'
        );

        // Armazenar temporariamente na sessão
        session([
            'two_factor_setup' => [
                'secret' => $secret,
                'backup_codes' => $backupCodes,
                'qr_code_url' => $qrCodeUrl,
            ]
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'secret' => $secret,
                'backup_codes' => $backupCodes,
                'qr_code_url' => $qrCodeUrl,
            ]
        ]);
    }

    /**
     * Ativa o 2FA após verificação do código
     */
    public function enable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido.',
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();
        $setup = session('two_factor_setup');

        if (!$setup) {
            return response()->json([
                'success' => false,
                'message' => 'Sessão expirada. Tente novamente.'
            ]);
        }

        // Verificar o código
        if (!$this->twoFactorService->verifyCode($setup['secret'], $request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido. Verifique seu aplicativo autenticador.'
            ]);
        }

        // Ativar 2FA
        $user->enableTwoFactor($setup['secret'], $setup['backup_codes']);

        // Limpar sessão
        session()->forget('two_factor_setup');

        return response()->json([
            'success' => true,
            'message' => '2FA ativado com sucesso!',
            'backup_codes' => $setup['backup_codes']
        ]);
    }

    /**
     * Desativa o 2FA
     */
    public function disable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Senha é obrigatória.',
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();

        // Verificar senha
        if (!password_verify($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.'
            ]);
        }

        // Desativar 2FA
        $user->disableTwoFactor();

        return response()->json([
            'success' => true,
            'message' => '2FA desativado com sucesso!'
        ]);
    }

    /**
     * Verifica código durante o login
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido.',
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json([
                'success' => false,
                'message' => '2FA não está ativado para este usuário.'
            ]);
        }

        // Verificar código TOTP
        if ($this->twoFactorService->verifyCode($user->two_factor_secret, $request->code)) {
            // Marcar como verificado na sessão
            session(['two_factor_verified' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Código verificado com sucesso!'
            ]);
        }

        // Verificar código de backup
        if ($user->verifyBackupCode($request->code)) {
            session(['two_factor_verified' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Código de backup verificado com sucesso!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Código inválido.'
        ]);
    }

    /**
     * Regenera códigos de backup
     */
    public function regenerateBackupCodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Senha é obrigatória.',
                'errors' => $validator->errors()
            ]);
        }

        $user = Auth::user();

        // Verificar senha
        if (!password_verify($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.'
            ]);
        }

        // Gerar novos códigos
        $newBackupCodes = $this->twoFactorService->generateBackupCodes();
        $user->update(['two_factor_backup_codes' => $newBackupCodes]);

        return response()->json([
            'success' => true,
            'message' => 'Códigos de backup regenerados com sucesso!',
            'backup_codes' => $newBackupCodes
        ]);
    }
}


