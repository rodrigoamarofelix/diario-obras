<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Medicao;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $showDeleted = $request->get('show_deleted', false);
        $perPage = 25;

        $query = Pagamento::with(['medicao.catalogo', 'medicao.contrato', 'medicao.lotacao', 'usuario']);

        // Se não estiver mostrando excluídos, filtrar apenas ativos
        if (!$showDeleted) {
            $query = $query->whereNull('deleted_at');
        } else {
            $query = $query->withTrashed();
        }

        if (!empty($keyword)) {
            $query = $query->where('numero_pagamento', 'LIKE', "%$keyword%")
                ->orWhereHas('medicao', function($q) use ($keyword) {
                    $q->where('numero_medicao', 'LIKE', "%$keyword%")
                      ->orWhereHas('catalogo', function($q2) use ($keyword) {
                          $q2->where('nome', 'LIKE', "%$keyword%");
                      })
                      ->orWhereHas('contrato', function($q2) use ($keyword) {
                          $q2->where('numero', 'LIKE', "%$keyword%");
                      })
                      ->orWhereHas('lotacao', function($q2) use ($keyword) {
                          $q2->where('nome', 'LIKE', "%$keyword%");
                      });
                })
                ->orWhere('status', 'LIKE', "%$keyword%");
        }

        $pagamentos = $query->orderBy('data_pagamento', 'desc')->paginate($perPage);

        // Contar excluídos para mostrar no cabeçalho
        $deletedCount = Pagamento::onlyTrashed()->count();

        return view('pagamento.index', compact('pagamentos', 'showDeleted', 'deletedCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicoes = Medicao::where('status', 'aprovado')
            ->naoPagas() // Filtra apenas medições que não foram pagas
            ->with(['catalogo', 'contrato', 'lotacao'])
            ->orderBy('data_medicao', 'desc')
            ->get();
        $proximoNumero = Pagamento::gerarProximoNumero();

        return view('pagamento.create', compact('medicoes', 'proximoNumero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'medicao_id' => 'required|exists:medicoes,id',
            'data_pagamento' => 'required|date',
            'valor_pagamento' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
            'documento_redmine' => 'nullable|string|max:255',
            'status' => 'required|in:pendente,aprovado,rejeitado,pago',
        ], [
            'medicao_id.required' => 'A medição é obrigatória.',
            'medicao_id.exists' => 'A medição selecionada não existe.',
            'data_pagamento.required' => 'A data do pagamento é obrigatória.',
            'data_pagamento.date' => 'A data deve ser válida.',
            'valor_pagamento.required' => 'O valor do pagamento é obrigatório.',
            'valor_pagamento.numeric' => 'O valor deve ser um número.',
            'valor_pagamento.min' => 'O valor deve ser maior que zero.',
            'status.required' => 'O status é obrigatório.',
        ]);

        // Verificar se a medição está aprovada
        $medicao = Medicao::findOrFail($request->medicao_id);
        if ($medicao->status !== 'aprovado') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['medicao_id' => 'Só é possível criar pagamentos para medições aprovadas.']);
        }

        // Verificar se a medição já foi paga
        if ($medicao->foiPaga()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['medicao_id' => 'Esta medição já foi paga e não pode receber novos pagamentos.']);
        }

        $pagamento = new Pagamento($request->all());
        $pagamento->usuario_id = auth()->id();
        $pagamento->numero_pagamento = Pagamento::gerarProximoNumero();
        $pagamento->save();

        return redirect()->route('pagamento.index')
            ->with('success', 'Pagamento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pagamento $pagamento)
    {
        $pagamento->load(['medicao.catalogo', 'medicao.contrato', 'medicao.lotacao', 'usuario']);

        return view('pagamento.show', compact('pagamento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pagamento $pagamento)
    {
        // Verificar se o pagamento pode ser editado (apenas se estiver pendente)
        if ($pagamento->status !== 'pendente') {
            return redirect()->route('pagamento.show', $pagamento)
                ->with('error', 'Este pagamento não pode ser editado pois não está mais pendente. Status atual: ' . $pagamento->status_name);
        }

        $medicoes = Medicao::where('status', 'aprovado')
            ->naoPagas() // Filtra apenas medições que não foram pagas
            ->with(['catalogo', 'contrato', 'lotacao'])
            ->orderBy('data_medicao', 'desc')
            ->get();

        return view('pagamento.edit', compact('pagamento', 'medicoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pagamento $pagamento)
    {
        // Verificar se o pagamento pode ser editado (apenas se estiver pendente)
        if ($pagamento->status !== 'pendente') {
            return redirect()->route('pagamento.show', $pagamento)
                ->with('error', 'Este pagamento não pode ser editado pois não está mais pendente. Status atual: ' . $pagamento->status_name);
        }

        $request->validate([
            'medicao_id' => 'required|exists:medicoes,id',
            'numero_pagamento' => 'required|string|max:50',
            'data_pagamento' => 'required|date',
            'valor_pagamento' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
            'documento_redmine' => 'nullable|string|max:255',
            'status' => 'required|in:pendente,aprovado,rejeitado,pago',
        ], [
            'medicao_id.required' => 'A medição é obrigatória.',
            'medicao_id.exists' => 'A medição selecionada não existe.',
            'numero_pagamento.required' => 'O número do pagamento é obrigatório.',
            'data_pagamento.required' => 'A data do pagamento é obrigatória.',
            'data_pagamento.date' => 'A data deve ser válida.',
            'valor_pagamento.required' => 'O valor do pagamento é obrigatório.',
            'valor_pagamento.numeric' => 'O valor deve ser um número.',
            'valor_pagamento.min' => 'O valor deve ser maior que zero.',
            'status.required' => 'O status é obrigatório.',
        ]);

        // Verificar se a medição está aprovada
        $medicao = Medicao::findOrFail($request->medicao_id);
        if ($medicao->status !== 'aprovado') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['medicao_id' => 'Só é possível criar pagamentos para medições aprovadas.']);
        }

        // Verificar se a medição já foi paga (exceto se estiver editando o próprio pagamento)
        if ($medicao->foiPaga() && $request->medicao_id != $pagamento->medicao_id) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['medicao_id' => 'Esta medição já foi paga e não pode receber novos pagamentos.']);
        }

        $pagamento->update($request->all());

        return redirect()->route('pagamento.index')
            ->with('success', 'Pagamento atualizado com sucesso!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pagamento $pagamento)
    {
        $pagamento->delete(); // Soft delete

        return redirect()->route('pagamento.index')
            ->with('success', 'Pagamento excluído com sucesso!');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $pagamento = Pagamento::withTrashed()->findOrFail($id);
        $pagamento->restore();

        return redirect()->route('pagamento.index')
            ->with('success', 'Pagamento restaurado com sucesso!');
    }

    /**
     * Get medicao data for AJAX request
     */
    public function getMedicaoData(Request $request)
    {
        $medicao = Medicao::with(['catalogo', 'contrato', 'lotacao'])->find($request->medicao_id);

        if (!$medicao) {
            return response()->json(['error' => 'Medição não encontrada'], 404);
        }

        return response()->json([
            'valor_total' => $medicao->valor_total,
            'catalogo_nome' => $medicao->catalogo->nome,
            'contrato_numero' => $medicao->contrato->numero,
            'lotacao_nome' => $medicao->lotacao->nome,
        ]);
    }
}
