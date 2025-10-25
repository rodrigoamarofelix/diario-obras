<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PagamentoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Pagamento::with(['medicao', 'medicao.contrato', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('medicao_id')) {
            $query->where('medicao_id', $request->medicao_id);
        }

        $perPage = $request->get('per_page', 15);
        $pagamentos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pagamentos->items(),
            'pagination' => [
                'current_page' => $pagamentos->currentPage(),
                'last_page' => $pagamentos->lastPage(),
                'per_page' => $pagamentos->perPage(),
                'total' => $pagamentos->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicao_id' => 'required|exists:medicoes,id',
            'valor' => 'required|numeric|min:0',
            'data_pagamento' => 'required|date',
            'status' => 'required|in:pendente,pago,cancelado',
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

            $pagamento = Pagamento::create([
                'medicao_id' => $request->medicao_id,
                'valor' => $request->valor,
                'data_pagamento' => $request->data_pagamento,
                'status' => $request->status,
                'user_id' => $request->user()->id,
            ]);

            $pagamento->load(['medicao', 'medicao.contrato', 'user']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pagamento criado com sucesso',
                'data' => $pagamento
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Pagamento $pagamento): JsonResponse
    {
        $pagamento->load(['medicao', 'medicao.contrato', 'user']);

        return response()->json([
            'success' => true,
            'data' => $pagamento
        ]);
    }

    public function update(Request $request, Pagamento $pagamento): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'valor' => 'sometimes|required|numeric|min:0',
            'data_pagamento' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pendente,pago,cancelado',
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

            $pagamento->update($request->only(['valor', 'data_pagamento', 'status']));
            $pagamento->load(['medicao', 'medicao.contrato', 'user']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pagamento atualizado com sucesso',
                'data' => $pagamento
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Pagamento $pagamento): JsonResponse
    {
        try {
            $pagamento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pagamento excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Pagamento::count(),
            'por_status' => Pagamento::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get(),
            'valor_total' => Pagamento::sum('valor'),
            'por_mes' => Pagamento::selectRaw('DatabaseHelper::formatDateForMonthGrouping(), count(*) as total, sum(valor) as valor')
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->limit(12)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}