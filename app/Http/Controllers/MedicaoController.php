<?php

namespace App\Http\Controllers;

use App\Models\Medicao;
use App\Models\Catalogo;
use App\Models\Contrato;
use App\Models\Lotacao;
use Illuminate\Http\Request;

class MedicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $showDeleted = $request->get('show_deleted', false);
        $perPage = 25;

        $query = Medicao::with(['catalogo', 'contrato', 'lotacao', 'usuario']);

        // Se não estiver mostrando excluídos, filtrar apenas ativos
        if (!$showDeleted) {
            $query = $query->whereNull('deleted_at');
        } else {
            $query = $query->withTrashed();
        }

        if (!empty($keyword)) {
            $query = $query->where('numero_medicao', 'LIKE', "%$keyword%")
                ->orWhereHas('catalogo', function($q) use ($keyword) {
                    $q->where('nome', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('contrato', function($q) use ($keyword) {
                    $q->where('numero', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('lotacao', function($q) use ($keyword) {
                    $q->where('nome', 'LIKE', "%$keyword%");
                })
                ->orWhere('status', 'LIKE', "%$keyword%");
        }

        $medicoes = $query->orderBy('data_medicao', 'desc')->paginate($perPage);

        // Contar excluídos para mostrar no cabeçalho
        $deletedCount = Medicao::onlyTrashed()->count();

        return view('medicao.index', compact('medicoes', 'showDeleted', 'deletedCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $catalogos = Catalogo::ativo()->orderBy('nome')->get();
        $contratos = Contrato::where('status', 'ativo')->orderBy('numero')->get();
        $lotacoes = Lotacao::ativo()->orderBy('nome')->get();
        $proximoNumero = Medicao::gerarProximoNumero();

        return view('medicao.create', compact('catalogos', 'contratos', 'lotacoes', 'proximoNumero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Corrigir sequência do PostgreSQL
        try {
            $maxId = \DB::selectOne("SELECT MAX(id) as max_id FROM medicoes");
            if ($maxId && $maxId->max_id) {
                \DB::select("SELECT setval('medicoes_id_seq', {$maxId->max_id})");
            }
        } catch (\Exception $e) {
            // Ignorar erro de sequência se não existir
        }

        $request->validate([
            'catalogo_id' => 'required|exists:catalogos,id',
            'contrato_id' => 'required|exists:contratos,id',
            'lotacao_id' => 'required|exists:lotacoes,id',
            'data_medicao' => 'required|date',
            'quantidade' => 'required|numeric|min:0.001',
            'valor_unitario' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,aprovado,rejeitado',
        ], [
            'catalogo_id.required' => 'O catálogo é obrigatório.',
            'catalogo_id.exists' => 'O catálogo selecionado não existe.',
            'contrato_id.required' => 'O contrato é obrigatório.',
            'contrato_id.exists' => 'O contrato selecionado não existe.',
            'lotacao_id.required' => 'A lotação é obrigatória.',
            'lotacao_id.exists' => 'A lotação selecionada não existe.',
            'data_medicao.required' => 'A data da medição é obrigatória.',
            'data_medicao.date' => 'A data deve ser válida.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.numeric' => 'A quantidade deve ser um número.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
            'valor_unitario.required' => 'O valor unitário é obrigatório.',
            'valor_unitario.numeric' => 'O valor unitário deve ser um número.',
            'valor_unitario.min' => 'O valor unitário deve ser maior que zero.',
            'status.required' => 'O status é obrigatório.',
        ]);

        $medicao = new Medicao($request->all());
        $medicao->usuario_id = auth()->id();
        $medicao->numero_medicao = Medicao::gerarProximoNumero();
        $medicao->calcularValorTotal();
        $medicao->save();

        return redirect()->route('medicao.index')
            ->with('success', 'Medição criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicao $medicao)
    {
        $medicao->load(['catalogo', 'contrato', 'lotacao', 'usuario', 'pagamentos']);

        return view('medicao.show', compact('medicao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicao $medicao)
    {
        // Verificar se a medição pode ser editada (apenas se estiver pendente)
        if ($medicao->status !== 'pendente') {
            return redirect()->route('medicao.show', $medicao)
                ->with('error', 'Esta medição não pode ser editada pois não está mais pendente. Status atual: ' . $medicao->status_name);
        }

        $catalogos = Catalogo::ativo()->orderBy('nome')->get();
        $contratos = Contrato::where('status', 'ativo')->orderBy('numero')->get();
        $lotacoes = Lotacao::ativo()->orderBy('nome')->get();

        return view('medicao.edit', compact('medicao', 'catalogos', 'contratos', 'lotacoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicao $medicao)
    {
        // Verificar se a medição pode ser editada (apenas se estiver pendente)
        if ($medicao->status !== 'pendente') {
            return redirect()->route('medicao.show', $medicao)
                ->with('error', 'Esta medição não pode ser editada pois não está mais pendente. Status atual: ' . $medicao->status_name);
        }

        $request->validate([
            'catalogo_id' => 'required|exists:catalogos,id',
            'contrato_id' => 'required|exists:contratos,id',
            'lotacao_id' => 'required|exists:lotacoes,id',
            'numero_medicao' => 'required|string|max:50',
            'data_medicao' => 'required|date',
            'quantidade' => 'required|numeric|min:0.001',
            'valor_unitario' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:pendente,aprovado,rejeitado',
        ], [
            'catalogo_id.required' => 'O catálogo é obrigatório.',
            'catalogo_id.exists' => 'O catálogo selecionado não existe.',
            'contrato_id.required' => 'O contrato é obrigatório.',
            'contrato_id.exists' => 'O contrato selecionado não existe.',
            'lotacao_id.required' => 'A lotação é obrigatória.',
            'lotacao_id.exists' => 'A lotação selecionada não existe.',
            'numero_medicao.required' => 'O número da medição é obrigatório.',
            'data_medicao.required' => 'A data da medição é obrigatória.',
            'data_medicao.date' => 'A data deve ser válida.',
            'quantidade.required' => 'A quantidade é obrigatória.',
            'quantidade.numeric' => 'A quantidade deve ser um número.',
            'quantidade.min' => 'A quantidade deve ser maior que zero.',
            'valor_unitario.required' => 'O valor unitário é obrigatório.',
            'valor_unitario.numeric' => 'O valor unitário deve ser um número.',
            'valor_unitario.min' => 'O valor unitário deve ser maior que zero.',
            'status.required' => 'O status é obrigatório.',
        ]);

        $medicao->fill($request->all());
        $medicao->calcularValorTotal();
        $medicao->save();

        return redirect()->route('medicao.index')
            ->with('success', 'Medição atualizada com sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicao $medicao)
    {
        $medicao->delete(); // Soft delete

        return redirect()->route('medicao.index')
            ->with('success', 'Medição excluída com sucesso!');
    }

    /**
     * Restore a soft deleted medicao.
     */
    public function restore($id)
    {
        $medicao = Medicao::withTrashed()->findOrFail($id);
        $medicao->restore();

        return redirect()->route('medicao.index')
            ->with('success', 'Medição restaurada com sucesso!');
    }

    /**
     * Get catalog value for AJAX request
     */
    public function getCatalogoValor(Request $request)
    {
        $catalogo = Catalogo::find($request->catalogo_id);

        if (!$catalogo) {
            return response()->json(['error' => 'Catálogo não encontrado'], 404);
        }

        return response()->json([
            'valor_unitario' => $catalogo->valor_unitario,
            'unidade_medida' => $catalogo->unidade_medida
        ]);
    }
}
