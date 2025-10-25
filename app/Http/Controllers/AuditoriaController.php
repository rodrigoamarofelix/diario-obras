<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Pessoa;
use App\Models\Contrato;
use App\Models\ContratoResponsavel;
use App\Models\Lotacao;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auditoria::with('usuario');

        // Filtros
        if ($request->filled('modelo')) {
            $query->where('modelo', $request->modelo);
        }

        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        // Dados para filtros
        $modelos = Auditoria::distinct()->pluck('modelo')->sort();
        $acoes = Auditoria::distinct()->pluck('acao')->sort();
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.index', compact('auditorias', 'modelos', 'acoes', 'usuarios'));
    }

    /**
     * Mostra auditorias de pessoas
     */
    public function pessoas(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->where('modelo', 'Pessoa');

        // Filtros específicos para pessoas
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        $acoes = Auditoria::where('modelo', 'Pessoa')->distinct()->pluck('acao')->sort();
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.pessoas', compact('auditorias', 'acoes', 'usuarios'));
    }

    /**
     * Mostra auditorias de responsáveis de contratos
     */
    public function responsaveis(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->whereIn('modelo', ['Contrato', 'ContratoResponsavel'])
            ->whereIn('acao', ['manager_changed', 'created', 'updated', 'deleted']);

        // Filtros específicos para responsáveis
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        $acoes = Auditoria::whereIn('modelo', ['Contrato', 'ContratoResponsavel'])
            ->whereIn('acao', ['manager_changed', 'created', 'updated', 'deleted'])
            ->distinct()->pluck('acao')->sort();
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.responsaveis', compact('auditorias', 'acoes', 'usuarios'));
    }

    /**
     * Mostra auditorias de contratos
     */
    public function contratos(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->where('modelo', 'Contrato');

        // Filtros específicos para contratos
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        $acoes = Auditoria::where('modelo', 'Contrato')->distinct()->pluck('acao')->sort();
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.contratos', compact('auditorias', 'acoes', 'usuarios'));
    }

    /**
     * Mostra auditorias de lotações
     */
    public function lotacoes(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->where('modelo', 'Lotacao');

        // Filtros específicos para lotações
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        $acoes = Auditoria::where('modelo', 'Lotacao')->distinct()->pluck('acao')->sort();
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.lotacoes', compact('auditorias', 'acoes', 'usuarios'));
    }

    /**
     * Mostra auditorias de usuários
     */
    public function usuarios(Request $request)
    {
        $query = Auditoria::with('usuario')
            ->where('modelo', 'User');

        // Filtros específicos para usuários
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $auditorias = $query->orderBy('created_at', 'desc')->paginate(20);

        $acoes = Auditoria::where('modelo', 'User')->distinct()->pluck('acao')->sort();
        $usuarios = User::orderBy('name')->get();

        return view('auditoria.usuarios', compact('auditorias', 'acoes', 'usuarios'));
    }

    /**
     * Mostra detalhes de uma auditoria específica
     */
    public function show($id)
    {
        $auditoria = Auditoria::with('usuario')->findOrFail($id);

        return view('auditoria.show', compact('auditoria'));
    }

    /**
     * Método de teste para verificar se as observações estão sendo salvas
     */
    public function testarObservacoes()
    {
        // Criar uma auditoria de teste
        $auditoria = Auditoria::create([
            'modelo' => 'Teste',
            'modelo_id' => 999,
            'acao' => 'updated',
            'dados_anteriores' => ['status' => 'pendente'],
            'dados_novos' => ['status' => 'aprovado'],
            'usuario_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'observacoes' => 'Status alterado de Pendente para Aprovado',
        ]);

        return response()->json([
            'success' => true,
            'auditoria_id' => $auditoria->id,
            'observacoes' => $auditoria->observacoes
        ]);
    }
}