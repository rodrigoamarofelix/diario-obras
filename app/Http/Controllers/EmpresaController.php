<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $empresas = Empresa::withTrashed()
            ->with('criadoPor')
            ->orderBy('nome')
            ->paginate(15);

        return view('empresas.index', compact('empresas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Corrigir sequência do PostgreSQL
        try {
            $maxId = \DB::selectOne("SELECT MAX(id) as max_id FROM empresas");
            if ($maxId && $maxId->max_id) {
                \DB::select("SELECT setval('empresas_id_seq', {$maxId->max_id})");
            }
        } catch (\Exception $e) {
            // Ignorar erro de sequência se não existir
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:empresas,cnpj',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'cep' => 'required|string|max:10',
            'endereco' => 'required|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
            'pais' => 'nullable|string|max:50',
            'site' => 'nullable|url|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        // Validação customizada do CNPJ
        if ($request->cnpj && !Empresa::validarCnpj($request->cnpj)) {
            $validator->errors()->add('cnpj', 'CNPJ inválido.');
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $empresa = Empresa::create([
            'nome' => $request->nome,
            'razao_social' => $request->razao_social,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
            'email' => $request->email,
            'telefone' => preg_replace('/\D/', '', $request->telefone),
            'whatsapp' => preg_replace('/\D/', '', $request->whatsapp),
            'cep' => preg_replace('/\D/', '', $request->cep),
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'pais' => $request->pais ?? 'Brasil',
            'site' => $request->site,
            'observacoes' => $request->observacoes,
            'ativo' => $request->has('ativo'),
            'created_by' => Auth::id()
        ]);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        $empresa->load(['criadoPor', 'projetos']);
        return view('empresas.show', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:empresas,cnpj,' . $empresa->id,
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'cep' => 'required|string|max:10',
            'endereco' => 'required|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2',
            'pais' => 'nullable|string|max:50',
            'site' => 'nullable|url|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'boolean'
        ]);

        // Validação customizada do CNPJ
        if ($request->cnpj && !Empresa::validarCnpj($request->cnpj)) {
            $validator->errors()->add('cnpj', 'CNPJ inválido.');
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $empresa->update([
            'nome' => $request->nome,
            'razao_social' => $request->razao_social,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
            'email' => $request->email,
            'telefone' => preg_replace('/\D/', '', $request->telefone),
            'whatsapp' => preg_replace('/\D/', '', $request->whatsapp),
            'cep' => preg_replace('/\D/', '', $request->cep),
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'pais' => $request->pais ?? 'Brasil',
            'site' => $request->site,
            'observacoes' => $request->observacoes,
            'ativo' => $request->has('ativo')
        ]);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }

    /**
     * Buscar CEP via API
     */
    public function buscarCep(Request $request)
    {
        $cep = preg_replace('/\D/', '', $request->cep);

        if (strlen($cep) != 8) {
            return response()->json(['error' => 'CEP deve ter 8 dígitos'], 400);
        }

        try {
            // Usando ViaCEP (gratuito)
            $url = "https://viacep.com.br/ws/{$cep}/json/";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if (isset($data['erro'])) {
                return response()->json(['error' => 'CEP não encontrado'], 404);
            }

            return response()->json([
                'cep' => $data['cep'],
                'endereco' => $data['logradouro'],
                'bairro' => $data['bairro'],
                'cidade' => $data['localidade'],
                'estado' => $data['uf']
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar CEP'], 500);
        }
    }

    /**
     * Validar CNPJ
     */
    public function validarCnpj(Request $request)
    {
        $cnpj = preg_replace('/\D/', '', $request->cnpj);
        $valido = Empresa::validarCnpj($cnpj);

        return response()->json(['valido' => $valido]);
    }

    /**
     * Alternar status ativo/inativo
     */
    public function toggleStatus(Empresa $empresa)
    {
        $empresa->update(['ativo' => !$empresa->ativo]);

        $status = $empresa->ativo ? 'ativada' : 'inativada';

        return redirect()->back()
            ->with('success', "Empresa {$status} com sucesso!");
    }

    /**
     * Restaurar empresa excluída (soft delete)
     */
    public function restore($id)
    {
        $empresa = Empresa::withTrashed()->findOrFail($id);
        $empresa->restore();

        return redirect()->back()
            ->with('success', 'Empresa restaurada com sucesso!');
    }

    /**
     * Excluir permanentemente (force delete)
     */
    public function forceDelete($id)
    {
        $empresa = Empresa::withTrashed()->findOrFail($id);
        $empresa->forceDelete();

        return redirect()->back()
            ->with('success', 'Empresa excluída permanentemente!');
    }
}
