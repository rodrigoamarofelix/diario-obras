<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use Illuminate\Http\Request;

class FuncaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $funcoes = Funcao::withTrashed()
            ->orderBy('nome')
            ->paginate(15);

        return view('funcoes.index', compact('funcoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('funcoes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:funcoes,nome',
            'descricao' => 'nullable|string',
            'categoria' => 'required|in:construcao,tecnica,supervisao,administrativa,outros',
            'ativo' => 'boolean',
        ]);

        // Corrigir sequência do PostgreSQL
        try {
            $maxId = \DB::selectOne("SELECT MAX(id) as max_id FROM funcoes");
            if ($maxId && $maxId->max_id) {
                \DB::select("SELECT setval('funcoes_id_seq', {$maxId->max_id})");
            }
        } catch (\Exception $e) {
            // Ignorar erro de sequência se não existir
        }

        Funcao::create($request->all());

        return redirect()->route('funcoes.index')
            ->with('success', 'Função cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $funcao = Funcao::withTrashed()->findOrFail($id);
        $funcao->load('pessoas');
        return view('funcoes.show', compact('funcao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $funcao = Funcao::findOrFail($id);
        return view('funcoes.edit', compact('funcao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $funcao = Funcao::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255|unique:funcoes,nome,' . $funcao->id,
            'descricao' => 'nullable|string',
            'categoria' => 'required|in:construcao,tecnica,supervisao,administrativa,outros',
            'ativo' => 'boolean',
        ]);

        $funcao->update($request->all());

        return redirect()->route('funcoes.index')
            ->with('success', 'Função atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $funcao = Funcao::findOrFail($id);
        $funcao->delete();

        return redirect()->back()
            ->with('success', 'Função excluída com sucesso!');
    }

    /**
     * Toggle status of the specified resource.
     */
    public function toggleStatus($id)
    {
        $funcao = Funcao::findOrFail($id);
        $funcao->update(['ativo' => !$funcao->ativo]);

        $status = $funcao->ativo ? 'ativada' : 'inativada';

        return redirect()->back()
            ->with('success', "Função {$status} com sucesso!");
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        $funcao = Funcao::withTrashed()->findOrFail($id);
        $funcao->restore();

        return redirect()->back()
            ->with('success', 'Função restaurada com sucesso!');
    }

    /**
     * Force delete the specified resource.
     */
    public function forceDelete($id)
    {
        $funcao = Funcao::withTrashed()->findOrFail($id);
        $funcao->forceDelete();

        return redirect()->back()
            ->with('success', 'Função excluída permanentemente!');
    }
}
