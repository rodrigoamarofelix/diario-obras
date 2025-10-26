<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catalogos = Catalogo::withTrashed()->orderBy('nome')->get();
        return view('catalogo.index', compact('catalogos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('catalogo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Corrigir sequência do PostgreSQL
        try {
            $maxId = \DB::selectOne("SELECT MAX(id) as max_id FROM catalogos");
            if ($maxId && $maxId->max_id) {
                \DB::select("SELECT setval('catalogos_id_seq', {$maxId->max_id})");
            }
        } catch (\Exception $e) {
            // Ignorar erro de sequência se não existir
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'codigo' => 'required|string|max:50|unique:catalogos,codigo',
            'valor_unitario' => 'required|numeric|min:0',
            'unidade_medida' => 'required|string|max:20',
            'status' => 'required|in:ativo,inativo',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'codigo.required' => 'O código é obrigatório.',
            'codigo.unique' => 'Este código já existe.',
            'valor_unitario.required' => 'O valor unitário é obrigatório.',
            'valor_unitario.numeric' => 'O valor unitário deve ser um número.',
            'valor_unitario.min' => 'O valor unitário deve ser maior que zero.',
            'unidade_medida.required' => 'A unidade de medida é obrigatória.',
            'status.required' => 'O status é obrigatório.',
        ]);

        Catalogo::create($request->all());

        return redirect()->route('catalogo.index')
            ->with('success', 'Catálogo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Catalogo $catalogo)
    {
        $catalogo->load(['medicoes' => function($query) {
            $query->orderBy('data_medicao', 'desc');
        }]);

        return view('catalogo.show', compact('catalogo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Catalogo $catalogo)
    {
        return view('catalogo.edit', compact('catalogo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Catalogo $catalogo)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'codigo' => 'required|string|max:50|unique:catalogos,codigo,' . $catalogo->id,
            'valor_unitario' => 'required|numeric|min:0',
            'unidade_medida' => 'required|string|max:20',
            'status' => 'required|in:ativo,inativo',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'codigo.required' => 'O código é obrigatório.',
            'codigo.unique' => 'Este código já existe.',
            'valor_unitario.required' => 'O valor unitário é obrigatório.',
            'valor_unitario.numeric' => 'O valor unitário deve ser um número.',
            'valor_unitario.min' => 'O valor unitário deve ser maior que zero.',
            'unidade_medida.required' => 'A unidade de medida é obrigatória.',
            'status.required' => 'O status é obrigatório.',
        ]);

        $catalogo->update($request->all());

        return redirect()->route('catalogo.index')
            ->with('success', 'Catálogo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Catalogo $catalogo)
    {
        $catalogo->delete(); // Soft delete

        return redirect()->route('catalogo.index')
            ->with('success', 'Catálogo excluído com sucesso!');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $catalogo = Catalogo::withTrashed()->findOrFail($id);
        $catalogo->restore();

        return redirect()->route('catalogo.index')
            ->with('success', 'Catálogo restaurado com sucesso!');
    }
}


