<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\Lotacao;
use App\Rules\CpfValido;
use App\Services\ReceitaFederalService;
use Illuminate\Http\Request;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $showDeleted = $request->get('show_deleted', false);
        $perPage = 25;

        $query = Pessoa::with('lotacao');

        // Se não estiver mostrando excluídos, filtrar apenas ativos
        if (!$showDeleted) {
            $query = $query->whereNull('deleted_at');
        } else {
            $query = $query->withTrashed();
        }

        if (!empty($keyword)) {
            $query = $query->where('nome', 'LIKE', "%$keyword%")
                ->orWhere('cpf', 'LIKE', "%$keyword%")
                ->orWhereHas('lotacao', function($q) use ($keyword) {
                    $q->where('nome', 'LIKE', "%$keyword%");
                })
                ->orWhere('status', 'LIKE', "%$keyword%");
        }

        $pessoas = $query->latest()->paginate($perPage);

        // Contar excluídos para mostrar no cabeçalho
        $deletedCount = Pessoa::onlyTrashed()->count();

        return view('pessoa.index', compact('pessoas', 'showDeleted', 'deletedCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $lotacoes = Lotacao::ativo()->orderBy('nome')->get();
        return view('pessoa.create', compact('lotacoes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => ['required', 'string', new CpfValido],
            'lotacao_id' => 'required|exists:lotacoes,id',
            'status' => 'required|in:ativo,inativo,pendente',
        ]);

        // Validação customizada para CPF único apenas entre pessoas ativas
        $cpfExists = Pessoa::where('cpf', $request->cpf)
            ->whereNull('deleted_at')
            ->exists();

        if ($cpfExists) {
            return redirect()->back()
                ->withErrors(['cpf' => 'Este CPF já está cadastrado para uma pessoa ativa.'])
                ->withInput();
        }

        // Tentar validar CPF na Receita Federal
        $receitaService = new ReceitaFederalService();
        $resultadoReceita = $receitaService->consultarCpf($request->cpf);

        $requestData = $request->all();

        if ($resultadoReceita['success']) {
            // CPF validado com sucesso
            $requestData['status_validacao'] = 'validado';
            $requestData['data_validacao'] = now();
            $requestData['observacoes_validacao'] = 'CPF validado com sucesso na Receita Federal';

            // Verificar se o nome informado corresponde ao da Receita Federal
            if (strtoupper(trim($request->nome)) !== strtoupper(trim($resultadoReceita['nome']))) {
                return redirect()->back()
                    ->withErrors(['nome' => 'O nome informado não corresponde ao nome registrado na Receita Federal.'])
                    ->withInput();
            }
        } else {
            // API fora ou CPF irregular - cadastro pendente
            $requestData['status_validacao'] = 'pendente';
            $requestData['status'] = 'pendente'; // Status da pessoa também fica pendente
            $requestData['observacoes_validacao'] = 'Cadastro pendente - CPF será validado posteriormente';
        }

        Pessoa::create($requestData);

        $mensagem = $resultadoReceita['success']
            ? 'Pessoa cadastrada com sucesso!'
            : 'Pessoa cadastrada com status pendente. A validação será realizada posteriormente.';

        return redirect('pessoa')->with('success', $mensagem);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $pessoa = Pessoa::with('lotacao')->findOrFail($id);

        return view('pessoa.show', compact('pessoa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $pessoa = Pessoa::findOrFail($id);
        $lotacoes = Lotacao::ativo()->orderBy('nome')->get();

        return view('pessoa.edit', compact('pessoa', 'lotacoes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => ['required', 'string', new CpfValido],
            'lotacao_id' => 'required|exists:lotacoes,id',
            'status' => 'required|in:ativo,inativo,pendente',
        ]);

        // Validação customizada para CPF único apenas entre pessoas ativas (exceto a atual)
        $cpfExists = Pessoa::where('cpf', $request->cpf)
            ->whereNull('deleted_at')
            ->where('id', '!=', $id)
            ->exists();

        if ($cpfExists) {
            return redirect()->back()
                ->withErrors(['cpf' => 'Este CPF já está cadastrado para uma pessoa ativa.'])
                ->withInput();
        }

        $requestData = $request->all();

        $pessoa = Pessoa::findOrFail($id);
        $pessoa->update($requestData);

        return redirect('pessoa')->with('success', 'Pessoa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $pessoa = Pessoa::findOrFail($id);
        $pessoa->delete(); // Soft delete

        return redirect()->route('pessoa.index')->with('success', 'Pessoa excluída com sucesso!');
    }

    /**
     * Restore a soft deleted pessoa.
     */
    public function restore($id)
    {
        $pessoa = Pessoa::withTrashed()->findOrFail($id);

        // Verificar se já existe uma pessoa ativa com o mesmo CPF
        $cpfExists = Pessoa::where('cpf', $pessoa->cpf)
            ->whereNull('deleted_at')
            ->exists();

        if ($cpfExists) {
            return redirect()->route('pessoa.index')
                ->with('error', 'Não é possível reativar esta pessoa. Já existe uma pessoa ativa com o mesmo CPF.');
        }

        // Restaurar com nova data de criação para controlar histórico
        $pessoa->restore();
        $pessoa->update([
            'created_at' => now(), // Nova data de criação
            'updated_at' => now(),
        ]);

        return redirect()->route('pessoa.index')
            ->with('success', 'Pessoa reativada com sucesso! Nova data de criação registrada.');
    }

    /**
     * Consulta CPF na Receita Federal via AJAX
     */
    public function consultarCpf(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'string', new CpfValido]
        ]);

        $cpf = $request->cpf;
        $receitaService = new ReceitaFederalService();

        // Verifica se o CPF já existe no banco
        $cpfExists = Pessoa::where('cpf', $cpf)
            ->whereNull('deleted_at')
            ->exists();

        if ($cpfExists) {
            return response()->json([
                'success' => false,
                'message' => 'Este CPF já está cadastrado no sistema.',
                'tipo' => 'cpf_existente'
            ]);
        }

        // Consulta na Receita Federal
        $resultado = $receitaService->consultarCpf($cpf);

        if (!$resultado['success']) {
            // Se a API estiver fora, permite cadastro pendente
            return response()->json([
                'success' => false,
                'message' => $resultado['message'],
                'tipo' => 'api_fora',
                'permite_cadastro_pendente' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'nome' => $resultado['nome'],
            'situacao' => $resultado['situacao'],
            'message' => 'CPF válido e regular na Receita Federal'
        ]);
    }

    /**
     * Revalidar CPF de uma pessoa pendente
     */
    public function revalidar($id)
    {
        $pessoa = Pessoa::findOrFail($id);

        if ($pessoa->status_validacao !== 'pendente') {
            return redirect()->back()->with('error', 'Apenas pessoas com status pendente podem ser revalidadas.');
        }

        try {
            // Incrementar tentativas
            $pessoa->incrementarTentativas();

            // Consultar CPF na Receita Federal
            $receitaService = new ReceitaFederalService();
            $resultado = $receitaService->consultarCpf($pessoa->cpf);

            if ($resultado['success']) {
                // CPF válido e regular
                $pessoa->marcarComoValidada();
                $mensagem = "CPF validado com sucesso! Status alterado para 'ativo'.";
            } else {
                // CPF irregular ou erro na consulta
                $motivo = $resultado['message'];
                $pessoa->marcarComoRejeitada($motivo);
                $mensagem = "CPF rejeitado: {$motivo}. Status alterado para 'inativo'.";
            }

            return redirect()->back()->with('success', $mensagem);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao revalidar CPF: ' . $e->getMessage());
        }
    }
}
