<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Pessoa;
use App\Models\ContratoResponsavel;
use App\Models\ContratoAnexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ContratoController extends Controller
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

        $query = Contrato::with(['gestor', 'fiscal', 'responsaveis' => function($q) {
            $q->ativo()->with(['gestor', 'fiscal']);
        }]);

        // Se não estiver mostrando excluídos, filtrar apenas ativos
        if (!$showDeleted) {
            $query = $query->whereNull('deleted_at');
        } else {
            $query = $query->withTrashed();
        }

        if (!empty($keyword)) {
            $query = $query->where('numero', 'LIKE', "%$keyword%")
                ->orWhere('descricao', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhereHas('gestor', function($q) use ($keyword) {
                    $q->where('nome', 'LIKE', "%$keyword%");
                })
                ->orWhereHas('fiscal', function($q) use ($keyword) {
                    $q->where('nome', 'LIKE', "%$keyword%");
                });
        }

        $contratos = $query->latest()->paginate($perPage);

        // Contar excluídos para mostrar no cabeçalho
        $deletedCount = Contrato::onlyTrashed()->count();

        return view('contrato.index', compact('contratos', 'showDeleted', 'deletedCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $pessoas = Pessoa::ativo()->orderBy('nome')->get();
        return view('contrato.create', compact('pessoas'));
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
        // Validação básica primeiro
        $basicValidation = $request->validate([
            'numero' => 'required|string|max:255|unique:contratos,numero',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'gestor_id' => 'required|exists:pessoas,id',
            'fiscal_id' => 'required|exists:pessoas,id',
            'status' => 'required|in:ativo,inativo,vencido,suspenso',
            'anexar_arquivos' => 'nullable|boolean',
            'anexos' => 'nullable|array',
            'anexos.*' => 'file|max:10240',
        ], [
            'data_fim.after' => 'A data de fim deve ser posterior à data de início.',
            'gestor_id.exists' => 'O gestor selecionado não existe.',
            'fiscal_id.exists' => 'O fiscal selecionado não existe.',
            'anexos.*.max' => 'Cada arquivo deve ter no máximo 10MB.',
        ]);

        $requestData = $request->except(['anexar_arquivos', 'anexos']);

        // Corrigir sequência do PostgreSQL antes de criar
        try {
            $maxId = \DB::selectOne("SELECT MAX(id) as max_id FROM contratos");
            if ($maxId && $maxId->max_id) {
                \DB::select("SELECT setval('contratos_id_seq', {$maxId->max_id})");
            }
        } catch (\Exception $e) {
            // Ignorar erro de sequência se não existir
        }

        $contrato = Contrato::create($requestData);

        // Criar registro inicial de responsáveis
        ContratoResponsavel::create([
            'contrato_id' => $contrato->id,
            'gestor_id' => $contrato->gestor_id,
            'fiscal_id' => $contrato->fiscal_id,
            'data_inicio' => $contrato->data_inicio,
            'data_fim' => null, // Ativo
        ]);

        // Processar anexos se solicitado
        if ($request->hasFile('anexos')) {
            $this->processarAnexos($contrato, $request->file('anexos'), $request->descricao ?? null);
        }

        return redirect()->route('contrato.index')->with('success', 'Contrato cadastrado com sucesso!');
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
        $contrato = Contrato::withTrashed()->with(['gestor', 'fiscal', 'responsaveis.gestor', 'responsaveis.fiscal', 'anexos.usuario'])->findOrFail($id);
        $historicoResponsaveis = $contrato->responsaveis()->with(['gestor', 'fiscal'])->orderBy('data_inicio', 'desc')->get();
        return view('contrato.show', compact('contrato', 'historicoResponsaveis'));
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
        $contrato = Contrato::with(['gestor', 'fiscal', 'responsaveis.gestor', 'responsaveis.fiscal'])->findOrFail($id);
        $pessoas = Pessoa::ativo()->orderBy('nome')->get();

        // Determinar o gestor_id e fiscal_id corretos (podem vir de responsáveis ativos)
        $responsavelAtual = $contrato->responsaveis()->ativo()->first();

        if ($responsavelAtual) {
            $contrato->gestor_id = $responsavelAtual->gestor_id;
            $contrato->fiscal_id = $responsavelAtual->fiscal_id;
        }

        return view('contrato.edit', compact('contrato', 'pessoas'));
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
            'numero' => 'required|string|max:255|unique:contratos,numero,' . $id,
            'descricao' => 'required|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
            'gestor_id' => 'required|exists:pessoas,id',
            'fiscal_id' => 'required|exists:pessoas,id',
            'status' => 'required|in:ativo,inativo,vencido,suspenso',
        ], [
            'data_fim.after' => 'A data de fim deve ser posterior à data de início.',
            'gestor_id.exists' => 'O gestor selecionado não existe.',
            'fiscal_id.exists' => 'O fiscal selecionado não existe.',
        ]);

        $requestData = $request->except(['anexar_arquivos', 'anexos']);

        $contrato = Contrato::with(['gestor', 'fiscal'])->findOrFail($id);

        // Verificar se gestor ou fiscal mudaram
        $gestorMudou = $contrato->gestor_id != $request->gestor_id;
        $fiscalMudou = $contrato->fiscal_id != $request->fiscal_id;

        if ($gestorMudou || $fiscalMudou) {
            // Finalizar período atual de responsáveis
            $responsavelAtual = ContratoResponsavel::where('contrato_id', $contrato->id)
                ->whereNull('data_fim')
                ->first();

            if ($responsavelAtual) {
                $responsavelAtual->update([
                    'data_fim' => now()->toDateString()
                ]);
            }

            // Criar novo período de responsáveis
            ContratoResponsavel::create([
                'contrato_id' => $contrato->id,
                'gestor_id' => $request->gestor_id,
                'fiscal_id' => $request->fiscal_id,
                'data_inicio' => now()->toDateString(),
                'data_fim' => null, // Ativo
            ]);

            // Auditar mudança de responsáveis
            $observacoes = [];
            if ($gestorMudou) {
                $gestorAnterior = $contrato->gestor ? $contrato->gestor->nome : 'N/A';
                $gestorNovo = Pessoa::find($request->gestor_id);
                $observacoes[] = "Gestor alterado de {$gestorAnterior} para " . ($gestorNovo ? $gestorNovo->nome : 'N/A');
            }
            if ($fiscalMudou) {
                $fiscalAnterior = $contrato->fiscal ? $contrato->fiscal->nome : 'N/A';
                $fiscalNovo = Pessoa::find($request->fiscal_id);
                $observacoes[] = "Fiscal alterado de {$fiscalAnterior} para " . ($fiscalNovo ? $fiscalNovo->nome : 'N/A');
            }

            // Desabilitar auditoria automática temporariamente
            Contrato::disableAuditing();

            $contrato->update($requestData);

            // Registrar auditoria específica após a atualização
            $contrato->auditar('manager_changed', [
                'gestor_id' => $contrato->gestor_id,
                'fiscal_id' => $contrato->fiscal_id,
            ], [
                'gestor_id' => $request->gestor_id,
                'fiscal_id' => $request->fiscal_id,
            ], implode('; ', $observacoes));

            // Reabilitar auditoria automática
            Contrato::enableAuditing();
        } else {
            // Se não há mudança de responsáveis, atualizar normalmente
            $contrato->update($requestData);
        }

        // Processar anexos se solicitado
        if ($request->hasFile('anexos')) {
            $this->processarAnexos($contrato, $request->file('anexos'), $request->descricao ?? null);
        }

        return redirect()->route('contrato.index')->with('success', 'Contrato atualizado com sucesso!');
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
        $contrato = Contrato::findOrFail($id);
        $contrato->delete(); // Soft delete

        return redirect()->route('contrato.index')->with('success', 'Contrato excluído com sucesso!');
    }

    /**
     * Restore a soft deleted contrato.
     */
    public function restore($id)
    {
        $contrato = Contrato::withTrashed()->findOrFail($id);
        $contrato->restore();

        return redirect()->route('contrato.index')
            ->with('success', 'Contrato restaurado com sucesso!');
    }

    /**
     * Processar anexos do contrato
     */
    private function processarAnexos(Contrato $contrato, array $arquivos, string $descricao = null)
    {
        $dataHora = now()->format('Y-m-d_H-i-s');
        $pastaContrato = "contratos/{$contrato->numero}_{$dataHora}";

        foreach ($arquivos as $arquivo) {
            if ($arquivo->isValid()) {
                // Gerar nome único para o arquivo
                $nomeOriginal = $arquivo->getClientOriginalName();
                $extensao = $arquivo->getClientOriginalExtension();
                $nomeArquivo = time() . '_' . uniqid() . '.' . $extensao;

                // Salvar arquivo
                $caminho = $arquivo->storeAs($pastaContrato, $nomeArquivo);

                // Criar registro no banco
                ContratoAnexo::create([
                    'contrato_id' => $contrato->id,
                    'nome_original' => $nomeOriginal,
                    'nome_arquivo' => $nomeArquivo,
                    'caminho' => $caminho,
                    'tipo_mime' => $arquivo->getMimeType(),
                    'tamanho' => $arquivo->getSize(),
                    'descricao' => $descricao,
                    'usuario_id' => Auth::id(),
                ]);
            }
        }
    }

    /**
     * Upload de anexos para contrato existente
     */
    public function uploadAnexos(Request $request, $id)
    {
        $contrato = Contrato::findOrFail($id);

        $request->validate([
            'anexos' => 'required|array',
            'anexos.*' => 'file|max:10240',
            'descricao' => 'nullable|string|max:255',
        ], [
            'anexos.*.max' => 'Cada arquivo deve ter no máximo 10MB.',
        ]);

        $this->processarAnexos($contrato, $request->file('anexos'), $request->descricao);

        return redirect()->route('contrato.show', $contrato->id)
            ->with('success', 'Anexos enviados com sucesso!');
    }

    /**
     * Download de anexo
     */
    public function downloadAnexo($id)
    {
        $anexo = ContratoAnexo::findOrFail($id);

        if (!$anexo->arquivoExiste()) {
            return redirect()->back()->with('error', 'Arquivo não encontrado.');
        }

        return Storage::download($anexo->caminho, $anexo->nome_original);
    }

    /**
     * Excluir anexo
     */
    public function excluirAnexo($id)
    {
        $anexo = ContratoAnexo::findOrFail($id);
        $contratoId = $anexo->contrato_id;

        // Excluir arquivo físico
        if ($anexo->arquivoExiste()) {
            Storage::delete($anexo->caminho);
        }

        // Excluir registro do banco
        $anexo->delete();

        return redirect()->route('contrato.show', $contratoId)
            ->with('success', 'Anexo excluído com sucesso!');
    }
}
