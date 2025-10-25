<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificacaoController extends Controller
{
    /**
     * Listar notificações do usuário logado
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tipo = $request->get('tipo', 'todas');
        $perPage = $request->get('per_page', 20);

        $query = $user->notificacoes();

        if ($tipo !== 'todas') {
            $query->where('tipo', $tipo);
        }

        $notificacoes = $query->paginate($perPage);

        return response()->json([
            'notificacoes' => $notificacoes->items(),
            'pagination' => [
                'current_page' => $notificacoes->currentPage(),
                'last_page' => $notificacoes->lastPage(),
                'per_page' => $notificacoes->perPage(),
                'total' => $notificacoes->total(),
            ],
            'nao_lidas' => $user->contarNotificacoesNaoLidas(),
        ]);
    }

    /**
     * Obter notificações não lidas
     */
    public function naoLidas(): JsonResponse
    {
        $user = auth()->user();
        $notificacoes = $user->notificacoesNaoLidas()->limit(10)->get();

        return response()->json([
            'notificacoes' => $notificacoes,
            'total_nao_lidas' => $user->contarNotificacoesNaoLidas(),
        ]);
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarComoLida(Notificacao $notificacao): JsonResponse
    {
        // Verificar se a notificação pertence ao usuário logado
        if ($notificacao->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $notificacao->marcarComoLida();

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida',
            'nao_lidas' => auth()->user()->contarNotificacoesNaoLidas(),
        ]);
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function marcarTodasComoLidas(): JsonResponse
    {
        $user = auth()->user();
        $user->marcarTodasNotificacoesComoLidas();

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram marcadas como lidas',
            'nao_lidas' => 0,
        ]);
    }

    /**
     * Excluir notificação
     */
    public function destroy(Notificacao $notificacao): JsonResponse
    {
        // Verificar se a notificação pertence ao usuário logado
        if ($notificacao->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $notificacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificação excluída',
            'nao_lidas' => auth()->user()->contarNotificacoesNaoLidas(),
        ]);
    }

    /**
     * Criar notificação de teste
     */
    public function criarTeste(Request $request): JsonResponse
    {
        $request->validate([
            'tipo' => 'required|in:info,success,warning,error',
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
        ]);

        $notificacao = Notificacao::criar(
            auth()->id(),
            $request->tipo,
            $request->titulo,
            $request->mensagem,
            ['url' => route('dashboard')],
            'teste',
            'notificacao',
            null
        );

        return response()->json([
            'success' => true,
            'message' => 'Notificação de teste criada',
            'notificacao' => $notificacao,
        ]);
    }

    /**
     * Obter estatísticas das notificações
     */
    public function estatisticas(): JsonResponse
    {
        $user = auth()->user();

        $estatisticas = [
            'total' => $user->notificacoes()->count(),
            'nao_lidas' => $user->contarNotificacoesNaoLidas(),
            'por_tipo' => $user->notificacoes()
                ->selectRaw('tipo, count(*) as total')
                ->groupBy('tipo')
                ->pluck('total', 'tipo'),
            'recentes' => $user->notificacoes()
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        return response()->json($estatisticas);
    }
}
