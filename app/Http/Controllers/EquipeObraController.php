<?php

namespace App\Http\Controllers;

use App\Models\EquipeObra;
use App\Models\Projeto;
use App\Models\AtividadeObra;
use App\Models\User;
use App\Models\Funcao;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class EquipeObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Agrupar por projeto e data, mostrando todas as pessoas em cada equipe
        $equipeAgrupada = EquipeObra::with(['projeto', 'atividade', 'funcionario', 'pessoa.funcao'])
            ->withTrashed()
            ->orderBy('data_trabalho', 'desc')
            ->orderBy('projeto_id')
            ->get()
            ->groupBy(function($item) {
                return $item->projeto_id . '_' . $item->data_trabalho;
            });

        return view('diario-obras.equipe.index', compact('equipeAgrupada'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projetos = Projeto::ativos()->get();
        $atividades = AtividadeObra::whereIn('status', ['planejado', 'em_andamento'])->orderBy('titulo')->get();
        $usuarios = User::where('profile', '!=', 'pending')->get();
        $funcoes = Funcao::ativas()->orderBy('nome')->get();
        $pessoas = Pessoa::ativas()->with('funcao')->orderBy('nome')->get();

        return view('diario-obras.equipe.create', compact('projetos', 'atividades', 'usuarios', 'funcoes', 'pessoas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'projeto_id' => 'required|exists:projetos,id',
                'atividade_id' => 'nullable|exists:atividade_obras,id',
                'pessoas' => 'required|array|min:1',
                'pessoas.*' => 'required|array',
                'pessoas.*.hora_entrada' => 'nullable|date_format:H:i',
                'pessoas.*.tipo_almoco' => 'required|in:integral,reduzido',
                'pessoas.*.hora_saida_almoco' => 'nullable|date_format:H:i',
                'pessoas.*.hora_retorno_almoco' => 'nullable|date_format:H:i',
                'pessoas.*.hora_saida' => 'nullable|date_format:H:i',
                'pessoas.*.horas_trabalhadas' => 'nullable',
                'pessoas.*.presente' => 'boolean',
                'data_trabalho' => 'required|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        $registrosCriados = 0;

        // Verificar se temos dados de pessoas
        if (!isset($request->pessoas) || empty($request->pessoas)) {
            return redirect()->back()
                ->with('error', 'Nenhuma pessoa foi adicionada à equipe. Por favor, adicione pelo menos uma pessoa.')
                ->withInput();
        }

        // Criar um registro para cada pessoa com seus dados individuais
        foreach ($request->pessoas as $pessoaId => $dadosPessoa) {
            $pessoa = Pessoa::with('funcao')->find($pessoaId);

            if ($pessoa) {
                EquipeObra::create([
                    'projeto_id' => $request->projeto_id,
                    'atividade_id' => $request->atividade_id,
                    'funcionario_id' => null,
                    'pessoa_id' => $pessoaId,
                    'data_trabalho' => $request->data_trabalho,
                    'hora_entrada' => $dadosPessoa['hora_entrada'] ?? null,
                    'tipo_almoco' => $dadosPessoa['tipo_almoco'] ?? 'integral',
                    'hora_saida_almoco' => $dadosPessoa['hora_saida_almoco'] ?? null,
                    'hora_retorno_almoco' => $dadosPessoa['hora_retorno_almoco'] ?? null,
                    'hora_saida' => $dadosPessoa['hora_saida'] ?? null,
                    'horas_trabalhadas' => $dadosPessoa['horas_trabalhadas'] ? (float)$dadosPessoa['horas_trabalhadas'] : null,
                    'funcao' => $pessoa->funcao ? strtolower($pessoa->funcao->nome) : 'outros',
                    'atividades_realizadas' => $dadosPessoa['atividades_realizadas'] ?? null,
                    'observacoes' => $dadosPessoa['observacoes'] ?? null,
                    'presente' => isset($dadosPessoa['presente']) ? (bool)$dadosPessoa['presente'] : true,
                    'created_by' => auth()->id(),
                ]);
                $registrosCriados++;
            }
        }

        if ($registrosCriados == 0) {
            return redirect()->back()
                ->with('error', 'Nenhum registro foi criado. Verifique os dados informados.')
                ->withInput();
        }

        $mensagem = $registrosCriados > 1
            ? "{$registrosCriados} registros de equipe criados com sucesso!"
            : "Registro de equipe criado com sucesso!";

        return redirect()->route('diario-obras.equipe.index')
            ->with('success', $mensagem);
    }

    /**
     * Display the specified resource.
     */
    public function show(EquipeObra $equipe)
    {
        $equipe->load(['projeto', 'atividade', 'funcionario', 'pessoa.funcao']);

        return view('diario-obras.equipe.show', compact('equipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EquipeObra $equipe)
    {
        $projetos = Projeto::ativos()->get();
        $atividades = AtividadeObra::whereIn('status', ['planejado', 'em_andamento'])->orderBy('titulo')->get();
        $usuarios = User::where('profile', '!=', 'pending')->get();
        $funcoes = Funcao::ativas()->orderBy('nome')->get();
        $pessoas = Pessoa::ativas()->with('funcao')->orderBy('nome')->get();

        return view('diario-obras.equipe.edit', compact('equipe', 'projetos', 'atividades', 'usuarios', 'funcoes', 'pessoas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EquipeObra $equipe)
    {
        $request->validate([
            'projeto_id' => 'required|exists:projetos,id',
            'atividade_id' => 'nullable|exists:atividade_obras,id',
            'pessoa_id' => 'required|exists:pessoas,id',
            'data_trabalho' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_saida_almoco' => 'nullable|date_format:H:i',
            'hora_retorno_almoco' => 'nullable|date_format:H:i',
            'hora_saida' => 'nullable|date_format:H:i',
            'tipo_almoco' => 'nullable|in:integral,reduzido',
            'horas_trabalhadas' => 'nullable|numeric',
            'presente' => 'boolean',
        ]);

        $pessoa = Pessoa::with('funcao')->find($request->pessoa_id);

        $equipe->update([
            'projeto_id' => $request->projeto_id,
            'atividade_id' => $request->atividade_id,
            'funcionario_id' => null,
            'pessoa_id' => $request->pessoa_id,
            'data_trabalho' => $request->data_trabalho,
            'hora_entrada' => $request->hora_entrada,
            'tipo_almoco' => $request->tipo_almoco ?? 'integral',
            'hora_saida_almoco' => $request->hora_saida_almoco,
            'hora_retorno_almoco' => $request->hora_retorno_almoco,
            'hora_saida' => $request->hora_saida,
            'horas_trabalhadas' => $request->horas_trabalhadas,
            'funcao' => $pessoa->funcao ? strtolower($pessoa->funcao->nome) : 'outros',
            'atividades_realizadas' => $request->atividades_realizadas,
            'observacoes' => $request->observacoes,
            'presente' => $request->presente ?? true,
        ]);

        return redirect()->route('diario-obras.equipe.index')
            ->with('success', 'Registro de equipe atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(EquipeObra $equipe)
    {
        $equipe->delete();

        return redirect()->route('diario-obras.equipe.index')
            ->with('success', 'Registro de equipe excluído com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore($id)
    {
        $equipe = EquipeObra::withTrashed()->findOrFail($id);
        $equipe->restore();

        return redirect()->route('diario-obras.equipe.index')
            ->with('success', 'Registro de equipe restaurado com sucesso!');
    }

    /**
     * Permanently delete a resource (only for administrators).
     */
    public function forceDelete($id)
    {
        // Apenas administradores podem fazer exclusão permanente
        if (!auth()->user()->can('manage-users')) {
            abort(403, 'Você não tem permissão para excluir permanentemente.');
        }

        $equipe = EquipeObra::withTrashed()->findOrFail($id);
        $equipe->forceDelete();

        return redirect()->route('diario-obras.equipe.index')
            ->with('success', 'Registro de equipe excluído permanentemente!');
    }

    /**
     * Equipe por projeto
     */
    public function porProjeto(Projeto $projeto)
    {
        $equipe = $projeto->equipe()
            ->with(['funcionario', 'atividade'])
            ->latest('data_trabalho')
            ->paginate(15);

        return view('diario-obras.equipe.por-projeto', compact('equipe', 'projeto'));
    }
}