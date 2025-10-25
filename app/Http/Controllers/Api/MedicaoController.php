<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicao;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MedicaoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Medicao::with(['contrato', 'catalogo', 'lotacao', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('contrato_id')) {
            $query->where('contrato_id', $request->contrato_id);
        }

        $perPage = $request->get('per_page', 15);
        $medicoes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $medicoes->items(),
            'pagination' => [
                'current_page' => $medicoes->currentPage(),
                'last_page' => $medicoes->lastPage(),
                'per_page' => $medicoes->perPage(),
                'total' => $medicoes->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contrato_id' => 'required|exists:contratos,id',
            'catalogo_id' => 'required|exists:catalogos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor_unitario' => 'required|numeric|min:0',
            'status' => 'required|in:pendente,aprovado,rejeitado',
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

            $medicao = Medicao::create([
                'contrato_id' => $request->contrato_id,
                'catalogo_id' => $request->catalogo_id,
                'quantidade' => $request->quantidade,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => $request->quantidade * $request->valor_unitario,
                'status' => $request->status,
                'user_id' => $request->user()->id,
            ]);

            $medicao->load(['contrato', 'catalogo', 'lotacao', 'user']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medição criada com sucesso',
                'data' => $medicao
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar medição',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Medicao $medicao): JsonResponse
    {
        $medicao->load(['contrato', 'catalogo', 'lotacao', 'user']);

        return response()->json([
            'success' => true,
            'data' => $medicao
        ]);
    }

    public function update(Request $request, Medicao $medicao): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantidade' => 'sometimes|required|numeric|min:0',
            'valor_unitario' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:pendente,aprovado,rejeitado',
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

            $data = $request->only(['quantidade', 'valor_unitario', 'status']);

            if (isset($data['quantidade']) && isset($data['valor_unitario'])) {
                $data['valor_total'] = $data['quantidade'] * $data['valor_unitario'];
            }

            $medicao->update($data);
            $medicao->load(['contrato', 'catalogo', 'lotacao', 'user']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medição atualizada com sucesso',
                'data' => $medicao
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar medição',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Medicao $medicao): JsonResponse
    {
        try {
            $medicao->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medição excluída com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir medição',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Medicao::count(),
            'por_status' => Medicao::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get(),
            'valor_total' => Medicao::sum('valor_total'),
            'por_mes' => Medicao::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as mes, count(*) as total, sum(valor_total) as valor')
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