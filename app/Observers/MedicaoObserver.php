<?php

namespace App\Observers;

use App\Models\Medicao;
use App\Models\Notificacao;
use App\Models\User;

class MedicaoObserver
{
    /**
     * Handle the Medicao "created" event.
     */
    public function created(Medicao $medicao): void
    {
        // Notificar usuários master e admin sobre nova medição
        $usuariosNotificar = User::whereIn('profile', ['master', 'admin'])->pluck('id')->toArray();

        Notificacao::notificarUsuarios(
            $usuariosNotificar,
            'info',
            'Nova Medição Criada',
            "Uma nova medição foi criada: {$medicao->numero_medicao} - Valor: R$ " . number_format($medicao->valor_total, 2, ',', '.'),
            [
                'url' => route('medicao.show', $medicao->id)
            ],
            'created',
            'Medicao',
            $medicao->id
        );
    }

    /**
     * Handle the Medicao "updated" event.
     */
    public function updated(Medicao $medicao): void
    {
        // Verificar se o status mudou para aprovado
        if ($medicao->wasChanged('status') && $medicao->status === 'aprovado') {
            // Notificar o usuário que criou a medição
            Notificacao::criar(
                $medicao->usuario_id,
                'success',
                'Medição Aprovada',
                "Sua medição {$medicao->numero_medicao} foi aprovada!",
                [
                    'url' => route('medicao.show', $medicao->id)
                ],
                'approved',
                'Medicao',
                $medicao->id
            );

            // Notificar usuários master e admin
            $usuariosNotificar = User::whereIn('profile', ['master', 'admin'])->pluck('id')->toArray();

            Notificacao::notificarUsuarios(
                $usuariosNotificar,
                'success',
                'Medição Aprovada',
                "A medição {$medicao->numero_medicao} foi aprovada.",
                [
                    'url' => route('medicao.show', $medicao->id)
                ],
                'approved',
                'Medicao',
                $medicao->id
            );
        }

        // Verificar se o status mudou para rejeitado
        if ($medicao->wasChanged('status') && $medicao->status === 'rejeitado') {
            // Notificar o usuário que criou a medição
            Notificacao::criar(
                $medicao->usuario_id,
                'error',
                'Medição Rejeitada',
                "Sua medição {$medicao->numero_medicao} foi rejeitada.",
                [
                    'url' => route('medicao.show', $medicao->id)
                ],
                'rejected',
                'Medicao',
                $medicao->id
            );
        }
    }

    /**
     * Handle the Medicao "deleted" event.
     */
    public function deleted(Medicao $medicao): void
    {
        // Notificar usuários master e admin sobre exclusão
        $usuariosNotificar = User::whereIn('profile', ['master', 'admin'])->pluck('id')->toArray();

        Notificacao::notificarUsuarios(
            $usuariosNotificar,
            'warning',
            'Medição Excluída',
            "A medição {$medicao->numero_medicao} foi excluída.",
            [],
            'deleted',
            'Medicao',
            $medicao->id
        );
    }

    /**
     * Handle the Medicao "restored" event.
     */
    public function restored(Medicao $medicao): void
    {
        // Notificar usuários master e admin sobre restauração
        $usuariosNotificar = User::whereIn('profile', ['master', 'admin'])->pluck('id')->toArray();

        Notificacao::notificarUsuarios(
            $usuariosNotificar,
            'info',
            'Medição Restaurada',
            "A medição {$medicao->numero_medicao} foi restaurada.",
            [
                'url' => route('medicao.show', $medicao->id)
            ],
            'restored',
            'Medicao',
            $medicao->id
        );
    }
}
