<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkflowAprovacao;
use App\Models\User;
use App\Models\Medicao;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorkflowController extends Controller
{
    /**
     * Dashboard principal do workflow
     */
    public function index()
    {
        $user = Auth::user();

        // Estatísticas gerais
        $stats = [
            'pendentes' => WorkflowAprovacao::pendentes()->count(),
            'em_analise' => WorkflowAprovacao::emAnalise()->count(),
            'aprovados_hoje' => WorkflowAprovacao::aprovados()
                ->whereDate('aprovado_em', today())->count(),
            'urgentes' => WorkflowAprovacao::urgentes()
                ->whereIn('status', ['pendente', 'em_analise'])->count(),
            'vencidos' => WorkflowAprovacao::vencidos()->count(),
        ];

        // Itens para o usuário aprovar
        $itensParaAprovar = WorkflowAprovacao::where('aprovador_id', $user->id)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->with(['model', 'solicitante'])
            ->orderBy('urgente', 'desc')
            ->orderBy('prazo_aprovacao', 'asc')
            ->paginate(10);

        // Itens solicitados pelo usuário
        $itensSolicitados = WorkflowAprovacao::where('solicitante_id', $user->id)
            ->with(['model', 'aprovador', 'aprovadoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Itens urgentes e vencidos
        $itensUrgentes = WorkflowAprovacao::urgentes()
            ->whereIn('status', ['pendente', 'em_analise'])
            ->with(['model', 'solicitante', 'aprovador'])
            ->orderBy('prazo_aprovacao', 'asc')
            ->limit(5)
            ->get();

        $itensVencidos = WorkflowAprovacao::vencidos()
            ->with(['model', 'solicitante', 'aprovador'])
            ->orderBy('prazo_aprovacao', 'asc')
            ->limit(5)
            ->get();

        return view('workflow.index', compact(
            'stats',
            'itensParaAprovar',
            'itensSolicitados',
            'itensUrgentes',
            'itensVencidos'
        ));
    }

    /**
     * Listar todos os itens de workflow
     */
    public function listar(Request $request)
    {
        $query = WorkflowAprovacao::with(['model', 'solicitante', 'aprovador', 'aprovadoPor']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('aprovador_id')) {
            $query->where('aprovador_id', $request->aprovador_id);
        }

        if ($request->filled('urgente')) {
            $query->where('urgente', $request->urgente);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $itens = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('workflow.listar', compact('itens'));
    }

    /**
     * Mostrar detalhes de um item
     */
    public function show($id)
    {
        $item = WorkflowAprovacao::with(['model', 'solicitante', 'aprovador', 'aprovadoPor'])
            ->findOrFail($id);

        // Verificar se o usuário pode visualizar
        if (!$item->podeSerVisualizadoPor(Auth::id())) {
            abort(403, 'Você não tem permissão para visualizar este item.');
        }

        return view('workflow.show', compact('item'));
    }

    /**
     * Aprovar um item
     */
    public function aprovar(Request $request, $id)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        // Verificar se pode aprovar
        if (!$item->podeSerAprovadoPor(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para aprovar este item.'
            ], 403);
        }

        $request->validate([
            'comentarios' => 'nullable|string|max:1000',
        ]);

        $item->aprovar(Auth::id(), $request->comentarios);

        return response()->json([
            'success' => true,
            'message' => 'Item aprovado com sucesso!'
        ]);
    }

    /**
     * Rejeitar um item
     */
    public function rejeitar(Request $request, $id)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        // Verificar se pode rejeitar
        if (!$item->podeSerRejeitadoPor(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para rejeitar este item.'
            ], 403);
        }

        $request->validate([
            'justificativa' => 'required|string|max:1000',
        ]);

        $item->rejeitar(Auth::id(), $request->justificativa);

        return response()->json([
            'success' => true,
            'message' => 'Item rejeitado com sucesso!'
        ]);
    }

    /**
     * Suspender um item
     */
    public function suspender(Request $request, $id)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        // Verificar se pode suspender
        if (!$item->podeSerRejeitadoPor(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para suspender este item.'
            ], 403);
        }

        $request->validate([
            'comentarios' => 'required|string|max:1000',
        ]);

        $item->suspender(Auth::id(), $request->comentarios);

        return response()->json([
            'success' => true,
            'message' => 'Item suspenso com sucesso!'
        ]);
    }

    /**
     * Marcar como em análise
     */
    public function marcarEmAnalise($id)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        // Verificar se pode marcar como em análise
        if (!$item->podeSerAprovadoPor(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para marcar este item como em análise.'
            ], 403);
        }

        $item->marcarComoEmAnalise();

        return response()->json([
            'success' => true,
            'message' => 'Item marcado como em análise!'
        ]);
    }

    /**
     * Criar workflow para medição
     */
    public function criarParaMedicao(Request $request)
    {
        $request->validate([
            'medicao_id' => 'required|exists:medicoes,id',
            'comentarios' => 'nullable|string|max:1000',
        ]);

        $medicao = Medicao::findOrFail($request->medicao_id);

        // Verificar se já existe workflow para esta medição
        $workflowExistente = WorkflowAprovacao::where('model_type', Medicao::class)
            ->where('model_id', $medicao->id)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->first();

        if ($workflowExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe um workflow pendente para esta medição.'
            ], 400);
        }

        $workflow = WorkflowAprovacao::criarParaMedicao(
            $medicao->id,
            Auth::id(),
            $medicao->valor_total
        );

        return response()->json([
            'success' => true,
            'message' => 'Workflow criado com sucesso!',
            'workflow_id' => $workflow->id
        ]);
    }

    /**
     * Criar workflow para pagamento
     */
    public function criarParaPagamento(Request $request)
    {
        $request->validate([
            'pagamento_id' => 'required|exists:pagamentos,id',
            'comentarios' => 'nullable|string|max:1000',
        ]);

        $pagamento = Pagamento::findOrFail($request->pagamento_id);

        // Verificar se já existe workflow para este pagamento
        $workflowExistente = WorkflowAprovacao::where('model_type', Pagamento::class)
            ->where('model_id', $pagamento->id)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->first();

        if ($workflowExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe um workflow pendente para este pagamento.'
            ], 400);
        }

        $workflow = WorkflowAprovacao::criarParaPagamento(
            $pagamento->id,
            Auth::id(),
            $pagamento->valor_pagamento
        );

        return response()->json([
            'success' => true,
            'message' => 'Workflow criado com sucesso!',
            'workflow_id' => $workflow->id
        ]);
    }

    /**
     * Estatísticas para dashboard
     */
    public function stats()
    {
        $stats = [
            'pendentes' => WorkflowAprovacao::pendentes()->count(),
            'em_analise' => WorkflowAprovacao::emAnalise()->count(),
            'aprovados_hoje' => WorkflowAprovacao::aprovados()
                ->whereDate('aprovado_em', today())->count(),
            'urgentes' => WorkflowAprovacao::urgentes()
                ->whereIn('status', ['pendente', 'em_analise'])->count(),
            'vencidos' => WorkflowAprovacao::vencidos()->count(),
            'total_mes' => WorkflowAprovacao::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}