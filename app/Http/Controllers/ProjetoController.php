<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projetos = Projeto::with(['responsavel', 'atividades', 'fotos'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('diario-obras.projetos.index', compact('projetos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = User::where('approval_status', 'approved')->get();
        return view('diario-obras.projetos.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
            'cep' => 'nullable|string|max:10',
            'cliente' => 'required|string|max:255',
            'contrato' => 'nullable|string|max:255',
            'valor_total' => 'nullable|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after:data_inicio',
            'status' => 'required|in:planejamento,em_andamento,pausado,concluido,cancelado',
            'prioridade' => 'required|in:baixa,media,alta,urgente',
            'responsavel_id' => 'required|exists:users,id',
            'observacoes' => 'nullable|string'
        ]);

        $projeto = Projeto::create([
            ...$request->all(),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('projetos.show', $projeto)
            ->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Projeto $projeto)
    {
        $projeto->load([
            'responsavel',
            'criadoPor',
            'atividades' => function($query) {
                $query->orderBy('data_atividade', 'desc');
            },
            'equipe' => function($query) {
                $query->orderBy('data_trabalho', 'desc');
            },
            'materiais' => function($query) {
                $query->orderBy('data_movimento', 'desc');
            },
            'fotos' => function($query) {
                $query->orderBy('data_foto', 'desc');
            },
            'empresas'
        ]);

        return view('diario-obras.projetos.show', compact('projeto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Projeto $projeto)
    {
        $usuarios = User::where('approval_status', 'approved')->get();
        return view('diario-obras.projetos.edit', compact('projeto', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Projeto $projeto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
            'cep' => 'nullable|string|max:10',
            'cliente' => 'required|string|max:255',
            'contrato' => 'nullable|string|max:255',
            'valor_total' => 'nullable|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after:data_inicio',
            'data_fim_real' => 'nullable|date|after:data_inicio',
            'status' => 'required|in:planejamento,em_andamento,pausado,concluido,cancelado',
            'prioridade' => 'required|in:baixa,media,alta,urgente',
            'responsavel_id' => 'required|exists:users,id',
            'observacoes' => 'nullable|string'
        ]);

        $projeto->update($request->all());

        return redirect()->route('projetos.show', $projeto)
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Projeto $projeto)
    {
        $projeto->delete();

        return redirect()->route('projetos.index')
            ->with('success', 'Projeto excluído com sucesso!');
    }
}
