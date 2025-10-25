<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use Illuminate\Http\Request;

class LotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lotacoes = Lotacao::withTrashed()->orderBy('nome')->get();
        return view('lotacao.index', compact('lotacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lotacao.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        Lotacao::create($request->all());

        return redirect()->route('lotacao.index')
            ->with('success', 'Lotação criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lotacao $lotacao)
    {
        return view('lotacao.show', compact('lotacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lotacao $lotacao)
    {
        return view('lotacao.edit', compact('lotacao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lotacao $lotacao)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:ativo,inativo',
        ]);

        $lotacao->update($request->all());

        return redirect()->route('lotacao.index')
            ->with('success', 'Lotação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lotacao $lotacao)
    {
        $lotacao->delete(); // Soft delete

        return redirect()->route('lotacao.index')
            ->with('success', 'Lotação excluída com sucesso!');
    }

    /**
     * Restore a soft deleted lotacao.
     */
    public function restore($id)
    {
        $lotacao = Lotacao::withTrashed()->findOrFail($id);
        $lotacao->restore();

        return redirect()->route('lotacao.index')
            ->with('success', 'Lotação restaurada com sucesso!');
    }

    /**
     * Permanently delete a lotacao.
     */
    public function forceDelete($id)
    {
        $lotacao = Lotacao::withTrashed()->findOrFail($id);
        $lotacao->forceDelete();

        return redirect()->route('lotacao.index')
            ->with('success', 'Lotação excluída permanentemente!');
    }
}
