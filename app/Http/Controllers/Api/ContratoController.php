<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ContratoController extends Controller
{
    /**
     * Listar contratos com filtros
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contrato::with(['gestor', 'fiscal']);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('data_inicio')) {
            $query->where('data_inicio', '>=', $request->data_inicio);
        }

        if ($request->has('data_fim')) {
            $query->where('data_fim', '<=', $request->data_fim);
        }

        if ($request->has('gestor_id')) {
            $query->where('gestor_id', $request->gestor_id);
        }

        if ($request->has('fiscal_id')) {
            $query->where('fiscal_id', $request->fiscal_id);
        }

        // Busca por texto
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        // Paginação
        $perPage = $request->get('per_page', 15);
        $contratos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $contratos->items(),
            'pagination' => [
                'current_page' => $contratos->currentPage(),
                'last_page' => $contratos->lastPage(),
                'per_page' => $contratos->perPage(),
                'total' => $contratos->total(),
                'from' => $contratos->firstItem(),
                'to' => $contratos->lastItem(),
            ]
        ]);
    }

    /**
     * Criar novo contrato
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'required|string|max:255|unique:contratos,numero',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'status' => 'required|in:ativo,inativo,vencido,suspenso',
            'gestor_id' => 'required|exists:pessoas,id',
            'fiscal_id' => 'required|exists:pessoas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contrato = Contrato::create([
                'numero' => $request->numero,
                'descricao' => $request->descricao,
                'data_inicio' => $request->data_inicio,
                'data_fim' => $request->data_fim,
                'status' => $request->status,
                'gestor_id' => $request->gestor_id,
                'fiscal_id' => $request->fiscal_id,
            ]);

            $contrato->load(['gestor', 'fiscal']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato criado com sucesso',
                'data' => $contrato
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar contrato',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibir contrato específico
     */
    public function show(Contrato $contrato): JsonResponse
    {
        $contrato->load(['gestor', 'fiscal', 'medicoes', 'medicoes.catalogo', 'medicoes.lotacao']);

        return response()->json([
            'success' => true,
            'data' => $contrato
        ]);
    }

    /**
     * Atualizar contrato
     */
    public function update(Request $request, Contrato $contrato): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero' => 'sometimes|required|string|max:255|unique:contratos,numero,' . $contrato->id,
            'descricao' => 'sometimes|required|string',
            'data_inicio' => 'sometimes|required|date',
            'data_fim' => 'sometimes|required|date|after:data_inicio',
            'status' => 'sometimes|required|in:ativo,inativo,vencido,suspenso',
            'gestor_id' => 'sometimes|required|exists:pessoas,id',
            'fiscal_id' => 'sometimes|required|exists:pessoas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contrato->update($request->only([
                'numero', 'descricao', 'data_inicio', 'data_fim',
                'status', 'gestor_id', 'fiscal_id'
            ]));

            $contrato->load(['gestor', 'fiscal']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrato atualizado com sucesso',
                'data' => $contrato
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar contrato',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir contrato
     */
    public function destroy(Contrato $contrato): JsonResponse
    {
        try {
            // Verificar se há medições associadas
            if ($contrato->medicoes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir contrato com medições associadas'
                ], 422);
            }

            $contrato->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contrato excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir contrato',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estatísticas dos contratos
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Contrato::count(),
            'por_status' => Contrato::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get(),
            'por_mes' => Contrato::selectRaw('DatabaseHelper::formatDateForMonthGrouping(), count(*) as total')
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->limit(12)
                ->get(),
            'vencendo_em_30_dias' => Contrato::where('data_fim', '<=', now()->addDays(30))
                ->where('data_fim', '>=', now())
                ->where('status', 'ativo')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}