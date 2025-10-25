<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    /**
     * Relatório do dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $periodo = $request->get('periodo', '30'); // últimos 30 dias por padrão
        $dataInicio = now()->subDays($periodo);

        $dados = [
            'resumo' => [
                'total_contratos' => Contrato::count(),
                'contratos_ativos' => Contrato::where('status', 'ativo')->count(),
                'total_medicoes' => Medicao::count(),
                'total_pagamentos' => Pagamento::count(),
                'valor_total_medicoes' => Medicao::sum('valor_total'),
                'valor_total_pagamentos' => Pagamento::sum('valor'),
            ],
            'contratos_por_status' => Contrato::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get(),
            'medicoes_por_mes' => Medicao::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, count(*) as total, sum(valor_total) as valor')
                ->where('created_at', '>=', $dataInicio)
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->get(),
            'pagamentos_por_mes' => Pagamento::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, count(*) as total, sum(valor) as valor')
                ->where('created_at', '>=', $dataInicio)
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->get(),
            'usuarios_por_mes' => User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, count(*) as total')
                ->where('created_at', '>=', $dataInicio)
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $dados,
            'periodo' => $periodo,
            'data_inicio' => $dataInicio->toISOString(),
            'data_fim' => now()->toISOString(),
        ]);
    }

    /**
     * Relatório financeiro
     */
    public function financeiro(Request $request): JsonResponse
    {
        $dataInicio = $request->get('data_inicio', now()->subMonths(6)->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));

        $dados = [
            'resumo_financeiro' => [
                'total_medicoes' => Medicao::whereBetween('created_at', [$dataInicio, $dataFim])->sum('valor_total'),
                'total_pagamentos' => Pagamento::whereBetween('created_at', [$dataInicio, $dataFim])->sum('valor'),
                'medicoes_pendentes' => Medicao::where('status', 'pendente')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->sum('valor_total'),
                'pagamentos_pendentes' => Pagamento::where('status', 'pendente')
                    ->whereBetween('created_at', [$dataInicio, $dataFim])
                    ->sum('valor'),
            ],
            'evolucao_mensal' => Medicao::selectRaw('
                    DATE_FORMAT(created_at, "%Y-%m") as mes,
                    count(*) as quantidade_medicoes,
                    sum(valor_total) as valor_medicoes
                ')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->groupBy('mes')
                ->orderBy('mes', 'asc')
                ->get(),
            'top_contratos' => Contrato::with(['gestor', 'fiscal'])
                ->withSum('medicoes', 'valor_total')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->orderBy('medicoes_sum_valor_total', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $dados,
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim,
            ]
        ]);
    }

    /**
     * Relatório de contratos
     */
    public function contratos(Request $request): JsonResponse
    {
        $filtros = $request->only(['status', 'data_inicio', 'data_fim', 'gestor_id', 'fiscal_id']);

        $query = Contrato::with(['gestor', 'fiscal', 'medicoes']);

        foreach ($filtros as $campo => $valor) {
            if ($valor) {
                if (in_array($campo, ['data_inicio', 'data_fim'])) {
                    if ($campo === 'data_inicio') {
                        $query->where('data_inicio', '>=', $valor);
                    } else {
                        $query->where('data_fim', '<=', $valor);
                    }
                } else {
                    $query->where($campo, $valor);
                }
            }
        }

        $contratos = $query->orderBy('created_at', 'desc')->get();

        $dados = [
            'contratos' => $contratos,
            'estatisticas' => [
                'total' => $contratos->count(),
                'por_status' => $contratos->groupBy('status')->map->count(),
                'vencendo_em_30_dias' => $contratos->where('data_fim', '<=', now()->addDays(30))
                    ->where('data_fim', '>=', now())
                    ->where('status', 'ativo')
                    ->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $dados,
            'filtros_aplicados' => $filtros,
        ]);
    }

    /**
     * Relatório de medições
     */
    public function medicoes(Request $request): JsonResponse
    {
        $filtros = $request->only(['status', 'data_inicio', 'data_fim', 'contrato_id', 'lotacao_id']);

        $query = Medicao::with(['contrato', 'catalogo', 'lotacao', 'user']);

        foreach ($filtros as $campo => $valor) {
            if ($valor) {
                if (in_array($campo, ['data_inicio', 'data_fim'])) {
                    if ($campo === 'data_inicio') {
                        $query->where('created_at', '>=', $valor);
                    } else {
                        $query->where('created_at', '<=', $valor);
                    }
                } else {
                    $query->where($campo, $valor);
                }
            }
        }

        $medicoes = $query->orderBy('created_at', 'desc')->get();

        $dados = [
            'medicoes' => $medicoes,
            'estatisticas' => [
                'total' => $medicoes->count(),
                'valor_total' => $medicoes->sum('valor_total'),
                'por_status' => $medicoes->groupBy('status')->map->count(),
                'por_contrato' => $medicoes->groupBy('contrato.numero')->map(function ($group) {
                    return [
                        'quantidade' => $group->count(),
                        'valor_total' => $group->sum('valor_total'),
                    ];
                }),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $dados,
            'filtros_aplicados' => $filtros,
        ]);
    }

    /**
     * Relatório de pagamentos
     */
    public function pagamentos(Request $request): JsonResponse
    {
        $filtros = $request->only(['status', 'data_inicio', 'data_fim', 'medicao_id']);

        $query = Pagamento::with(['medicao', 'medicao.contrato', 'user']);

        foreach ($filtros as $campo => $valor) {
            if ($valor) {
                if (in_array($campo, ['data_inicio', 'data_fim'])) {
                    if ($campo === 'data_inicio') {
                        $query->where('created_at', '>=', $valor);
                    } else {
                        $query->where('created_at', '<=', $valor);
                    }
                } else {
                    $query->where($campo, $valor);
                }
            }
        }

        $pagamentos = $query->orderBy('created_at', 'desc')->get();

        $dados = [
            'pagamentos' => $pagamentos,
            'estatisticas' => [
                'total' => $pagamentos->count(),
                'valor_total' => $pagamentos->sum('valor'),
                'por_status' => $pagamentos->groupBy('status')->map->count(),
                'por_mes' => $pagamentos->groupBy(function ($item) {
                    return $item->created_at->format('Y-m');
                })->map(function ($group) {
                    return [
                        'quantidade' => $group->count(),
                        'valor_total' => $group->sum('valor'),
                    ];
                }),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $dados,
            'filtros_aplicados' => $filtros,
        ]);
    }

    /**
     * Relatório de usuários
     */
    public function usuarios(Request $request): JsonResponse
    {
        $filtros = $request->only(['profile', 'approval_status']);

        $query = User::withCount(['medicoes', 'pagamentos']);

        foreach ($filtros as $campo => $valor) {
            if ($valor) {
                $query->where($campo, $valor);
            }
        }

        $usuarios = $query->orderBy('created_at', 'desc')->get();

        $dados = [
            'usuarios' => $usuarios,
            'estatisticas' => [
                'total' => $usuarios->count(),
                'por_perfil' => $usuarios->groupBy('profile')->map->count(),
                'por_status' => $usuarios->groupBy('approval_status')->map->count(),
                'usuarios_ativos' => $usuarios->where('approval_status', 'aprovado')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $dados,
            'filtros_aplicados' => $filtros,
        ]);
    }
}