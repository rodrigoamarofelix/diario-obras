<?php

namespace App\Http\Controllers;

use App\Models\MaterialObra;
use App\Models\Projeto;
use App\Models\AtividadeObra;
use App\Models\User;
use Illuminate\Http\Request;

class MaterialObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materiais = MaterialObra::with(['projeto', 'atividade', 'responsavel'])
            ->latest('data_movimento')
            ->paginate(15);

        return view('diario-obras.materiais.index', compact('materiais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projetos = Projeto::ativos()->get();
        $atividades = AtividadeObra::where('status', '!=', 'concluido')->get();
        $usuarios = User::where('profile', '!=', 'pending')->get();

        return view('diario-obras.materiais.create', compact('projetos', 'atividades', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'atividade_id' => 'nullable|exists:atividade_obras,id',
            'nome_material' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade_medida' => 'required|string|max:50',
            'quantidade' => 'required|numeric|min:0',
            'valor_unitario' => 'nullable|numeric|min:0',
            'tipo_movimento' => 'required|in:entrada,saida,transferencia',
            'data_movimento' => 'required|date',
            'fornecedor' => 'nullable|string|max:255',
            'nota_fiscal' => 'nullable|string|max:255',
            'responsavel_id' => 'required|exists:users,id',
        ]);

        $valorTotal = $request->quantidade * ($request->valor_unitario ?? 0);

        $material = MaterialObra::create([
            'projeto_id' => $request->projeto_id,
            'atividade_id' => $request->atividade_id,
            'nome_material' => $request->nome_material,
            'descricao' => $request->descricao,
            'unidade_medida' => $request->unidade_medida,
            'quantidade' => $request->quantidade,
            'valor_unitario' => $request->valor_unitario,
            'valor_total' => $valorTotal,
            'tipo_movimento' => $request->tipo_movimento,
            'data_movimento' => $request->data_movimento,
            'fornecedor' => $request->fornecedor,
            'nota_fiscal' => $request->nota_fiscal,
            'observacoes' => $request->observacoes,
            'responsavel_id' => $request->responsavel_id,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('diario-obras.materiais.index')
            ->with('success', 'Material registrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialObra $material)
    {
        $material->load(['projeto', 'atividade', 'responsavel']);

        return view('diario-obras.materiais.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialObra $material)
    {
        $projetos = Projeto::ativos()->get();
        $atividades = AtividadeObra::where('status', '!=', 'concluido')->get();
        $usuarios = User::where('profile', '!=', 'pending')->get();

        return view('diario-obras.materiais.edit', compact('material', 'projetos', 'atividades', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialObra $material)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'atividade_id' => 'nullable|exists:atividade_obras,id',
            'nome_material' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade_medida' => 'required|string|max:50',
            'quantidade' => 'required|numeric|min:0',
            'valor_unitario' => 'nullable|numeric|min:0',
            'tipo_movimento' => 'required|in:entrada,saida,transferencia',
            'data_movimento' => 'required|date',
            'fornecedor' => 'nullable|string|max:255',
            'nota_fiscal' => 'nullable|string|max:255',
            'responsavel_id' => 'required|exists:users,id',
        ]);

        $valorTotal = $request->quantidade * ($request->valor_unitario ?? 0);

        $material->update([
            'projeto_id' => $request->projeto_id,
            'atividade_id' => $request->atividade_id,
            'nome_material' => $request->nome_material,
            'descricao' => $request->descricao,
            'unidade_medida' => $request->unidade_medida,
            'quantidade' => $request->quantidade,
            'valor_unitario' => $request->valor_unitario,
            'valor_total' => $valorTotal,
            'tipo_movimento' => $request->tipo_movimento,
            'data_movimento' => $request->data_movimento,
            'fornecedor' => $request->fornecedor,
            'nota_fiscal' => $request->nota_fiscal,
            'observacoes' => $request->observacoes,
            'responsavel_id' => $request->responsavel_id,
        ]);

        return redirect()->route('diario-obras.materiais.index')
            ->with('success', 'Material atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialObra $material)
    {
        $material->delete();

        return redirect()->route('diario-obras.materiais.index')
            ->with('success', 'Material excluído com sucesso!');
    }

    /**
     * Materiais por projeto
     */
    public function porProjeto(Projeto $projeto)
    {
        $materiais = $projeto->materiais()
            ->with(['atividade', 'responsavel'])
            ->latest('data_movimento')
            ->paginate(15);

        return view('diario-obras.materiais.por-projeto', compact('materiais', 'projeto'));
    }
}