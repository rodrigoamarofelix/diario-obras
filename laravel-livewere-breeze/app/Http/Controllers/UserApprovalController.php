<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserApprovalController extends Controller
{
    /**
     * Lista usuários pendentes de aprovação
     */
    public function index()
    {
        // Verificar se o usuário atual está logado
        if (!Auth::check()) {
            abort(403, 'Você precisa estar logado para acessar esta página.');
        }

        $pendingUsers = User::pending()->orderBy('created_at', 'desc')->get();

        return view('user-approvals.index', compact('pendingUsers'));
    }

    /**
     * Aprova um usuário
     */
    public function approve(User $user)
    {
        // Verificar se o usuário atual pode gerenciar aprovações
        if (!Auth::user()->isMaster()) {
            abort(403, 'Você não tem permissão para gerenciar aprovações.');
        }

        // Verificar se o usuário está pendente
        if (!$user->isPending()) {
            return back()->with('error', 'Este usuário não está pendente de aprovação.');
        }

        // Aprovar o usuário
        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Enviar email de aprovação
        $this->sendApprovalEmail($user);

        return back()->with('success', 'Usuário aprovado com sucesso! Email de confirmação enviado.');
    }

    /**
     * Rejeita um usuário
     */
    public function reject(User $user)
    {
        // Verificar se o usuário atual pode gerenciar aprovações
        if (!Auth::user()->isMaster()) {
            abort(403, 'Você não tem permissão para gerenciar aprovações.');
        }

        // Verificar se o usuário está pendente
        if (!$user->isPending()) {
            return back()->with('error', 'Este usuário não está pendente de aprovação.');
        }

        // Rejeitar o usuário
        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Enviar email de rejeição
        $this->sendRejectionEmail($user);

        return back()->with('success', 'Usuário rejeitado. Email de notificação enviado.');
    }

    /**
     * Envia email de aprovação
     */
    private function sendApprovalEmail(User $user)
    {
        try {
            Mail::send('emails.user-approved', [
                'user' => $user,
                'loginUrl' => route('login'),
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Conta Aprovada - SGL Sistema de Gestão');
            });
        } catch (\Exception $e) {
            // Log do erro, mas não falha a aprovação
            \Log::error('Erro ao enviar email de aprovação: ' . $e->getMessage());
        }
    }

    /**
     * Envia email de rejeição
     */
    private function sendRejectionEmail(User $user)
    {
        try {
            Mail::send('emails.user-rejected', [
                'user' => $user,
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Conta Rejeitada - SGL Sistema de Gestão');
            });
        } catch (\Exception $e) {
            // Log do erro, mas não falha a rejeição
            \Log::error('Erro ao enviar email de rejeição: ' . $e->getMessage());
        }
    }
}