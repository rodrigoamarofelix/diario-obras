<?php

namespace App\Http\Controllers;

use App\Models\AtividadeObra;
use App\Models\Projeto;
use App\Models\User;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class AtividadeObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $atividades = AtividadeObra::with(['projeto', 'responsavel.funcao'])
            ->latest('data_atividade')
            ->paginate(15);

        return view('diario-obras.atividades.index', compact('atividades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projetos = Projeto::ativos()->get();
        $pessoas = Pessoa::ativo()->orderBy('nome')->get();

        return view('diario-obras.atividades.create', compact('projetos', 'pessoas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'data_atividade' => 'required|date',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|in:construcao,demolicao,reforma,manutencao,limpeza,outros',
            'status' => 'required|in:planejado,em_andamento,concluido,cancelado',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
            'responsavel_id' => 'required|exists:pessoas,id',
        ]);

        $atividade = AtividadeObra::create([
            'projeto_id' => $request->projeto_id,
            'data_atividade' => $request->data_atividade,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
            'status' => $request->status,
            'hora_inicio' => $request->hora_inicio,
            'hora_fim' => $request->hora_fim,
            'tempo_gasto_minutos' => $request->tempo_gasto_minutos,
            'observacoes' => $request->observacoes,
            'problemas_encontrados' => $request->problemas_encontrados,
            'solucoes_aplicadas' => $request->solucoes_aplicadas,
            'responsavel_id' => $request->responsavel_id,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('diario-obras.atividades.index')
            ->with('success', 'Atividade criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AtividadeObra $atividade)
    {
        $atividade->load(['projeto', 'responsavel.funcao', 'equipe.funcionario', 'materiais', 'fotos']);

        return view('diario-obras.atividades.show', compact('atividade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AtividadeObra $atividade)
    {
        $projetos = Projeto::ativos()->get();
        $pessoas = Pessoa::ativo()->orderBy('nome')->get();

        return view('diario-obras.atividades.edit', compact('atividade', 'projetos', 'pessoas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AtividadeObra $atividade)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'data_atividade' => 'required|date',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo' => 'required|in:construcao,demolicao,reforma,manutencao,limpeza,outros',
            'status' => 'required|in:planejado,em_andamento,concluido,cancelado',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
            'responsavel_id' => 'required|exists:pessoas,id',
        ]);

        $atividade->update([
            'projeto_id' => $request->projeto_id,
            'data_atividade' => $request->data_atividade,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'tipo' => $request->tipo,
            'status' => $request->status,
            'hora_inicio' => $request->hora_inicio,
            'hora_fim' => $request->hora_fim,
            'tempo_gasto_minutos' => $request->tempo_gasto_minutos,
            'observacoes' => $request->observacoes,
            'problemas_encontrados' => $request->problemas_encontrados,
            'solucoes_aplicadas' => $request->solucoes_aplicadas,
            'responsavel_id' => $request->responsavel_id,
        ]);

        return redirect()->route('diario-obras.atividades.index')
            ->with('success', 'Atividade atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AtividadeObra $atividade)
    {
        $atividade->delete();

        return redirect()->route('diario-obras.atividades.index')
            ->with('success', 'Atividade excluída com sucesso!');
    }

    /**
     * Atividades por projeto
     */
    public function porProjeto(Projeto $projeto)
    {
        $atividades = $projeto->atividades()
            ->with(['responsavel'])
            ->latest('data_atividade')
            ->paginate(15);

        return view('diario-obras.atividades.por-projeto', compact('atividades', 'projeto'));
    }

    public function getByProjeto(Projeto $projeto)
    {
        $atividades = $projeto->atividades()
            ->whereIn('status', ['planejado', 'em_andamento'])
            ->orderBy('titulo')
            ->get(['id', 'titulo']);

        return response()->json($atividades);
    }
}