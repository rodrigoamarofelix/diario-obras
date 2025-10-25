<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\AtividadeObra;
use App\Models\EquipeObra;
use App\Models\MaterialObra;
use App\Models\FotoObra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use Barryvdh\DomPDF\Facade\Pdf;

class DiarioObrasController extends Controller
{
    /**
     * Dashboard principal com estatísticas avançadas
     */
    public function dashboard()
    {
        $stats = $this->getDashboardStats();
        $projetosRecentes = $this->getProjetosRecentes();
        $atividadesHoje = $this->getAtividadesHoje();
        $alertas = $this->getAlertas();
        $graficos = $this->getDadosGraficos();

        return view('diario-obras.dashboard', compact(
            'stats',
            'projetosRecentes',
            'atividadesHoje',
            'alertas',
            'graficos'
        ));
    }

    /**
     * Relatório completo de projeto
     */
    public function relatorioProjeto(Request $request, Projeto $projeto)
    {
        $dataInicio = $request->get('data_inicio', $projeto->data_inicio);
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));

        // Carregar todas as atividades do projeto
        $atividades = $projeto->atividades()
            ->whereBetween('data_atividade', [$dataInicio, $dataFim])
            ->with(['responsavel', 'fotos'])
            ->orderBy('data_atividade', 'desc')
            ->get();

        // Separar ocorrências (atividades com problemas)
        $ocorrencias = $atividades->filter(function($atividade) {
            return !empty($atividade->problemas_encontrados);
        });

        // Separar comentários (atividades com observações mas sem problemas)
        $comentarios = $atividades->filter(function($atividade) {
            return !empty($atividade->observacoes) && empty($atividade->problemas_encontrados);
        });

        // Carregar fotos do projeto
        $fotos = $projeto->fotos()
            ->whereBetween('data_foto', [$dataInicio, $dataFim])
            ->orderBy('data_foto', 'desc')
            ->get();

        $relatorio = [
            'projeto' => $projeto,
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ],
            'atividades' => $atividades,
            'ocorrencias' => $ocorrencias,
            'comentarios' => $comentarios,
            'fotos' => $fotos,
            'equipe' => $projeto->equipe()
                ->whereBetween('data_trabalho', [$dataInicio, $dataFim])
                ->with('funcionario')
                ->get(),
            'materiais' => $projeto->materiais()
                ->whereBetween('data_movimento', [$dataInicio, $dataFim])
                ->get(),
            'estatisticas' => $this->getEstatisticasProjeto($projeto, $dataInicio, $dataFim)
        ];

        if ($request->get('formato') === 'pdf') {
            return $this->gerarRelatorioPDF($relatorio);
        }

        return view('diario-obras.relatorios.detalhado', compact('relatorio', 'projeto', 'atividades', 'ocorrencias', 'comentarios', 'fotos'));
    }

    /**
     * Relatório de produtividade da equipe
     */
    public function relatorioProdutividade(Request $request)
    {
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        $projetoId = $request->get('projeto_id');

        $query = EquipeObra::whereBetween('data_trabalho', [$dataInicio, $dataFim])
            ->with(['funcionario', 'projeto']);

        if ($projetoId) {
            $query->where('projeto_id', $projetoId);
        }

        $produtividade = $query->get()
            ->groupBy('funcionario_id')
            ->map(function ($registros) {
                $funcionario = $registros->first()->funcionario;
                return [
                    'funcionario' => $funcionario,
                    'total_dias' => $registros->count(),
                    'total_horas' => $registros->sum('horas_trabalhadas'),
                    'funcoes' => $registros->pluck('funcao')->unique()->values(),
                    'projetos' => $registros->pluck('projeto.nome')->unique()->values(),
                    'ultima_atividade' => $registros->max('data_trabalho')
                ];
            });

        $projetos = Projeto::ativos()->get();

        return view('diario-obras.relatorios.produtividade', compact(
            'produtividade',
            'projetos',
            'dataInicio',
            'dataFim',
            'projetoId'
        ));
    }

    /**
     * Relatório de custos e materiais
     */
    public function relatorioCustos(Request $request)
    {
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        $projetoId = $request->get('projeto_id');

        $query = MaterialObra::whereBetween('data_movimento', [$dataInicio, $dataFim])
            ->with(['projeto', 'atividade']);

        if ($projetoId) {
            $query->where('projeto_id', $projetoId);
        }

        $materiais = $query->get();

        $custos = [
            'total_entrada' => $materiais->where('tipo_movimento', 'entrada')->sum('valor_total'),
            'total_saida' => $materiais->where('tipo_movimento', 'saida')->sum('valor_total'),
            'por_projeto' => $materiais->groupBy('projeto_id')->map(function ($materiaisProjeto) {
                return [
                    'projeto' => $materiaisProjeto->first()->projeto,
                    'total' => $materiaisProjeto->sum('valor_total'),
                    'entrada' => $materiaisProjeto->where('tipo_movimento', 'entrada')->sum('valor_total'),
                    'saida' => $materiaisProjeto->where('tipo_movimento', 'saida')->sum('valor_total')
                ];
            }),
            'por_fornecedor' => $materiais->groupBy('fornecedor')->map(function ($materiaisFornecedor) {
                return [
                    'fornecedor' => $materiaisFornecedor->first()->fornecedor,
                    'total' => $materiaisFornecedor->sum('valor_total'),
                    'quantidade_materiais' => $materiaisFornecedor->count()
                ];
            })
        ];

        $projetos = Projeto::ativos()->get();

        return view('diario-obras.relatorios.custos', compact(
            'custos',
            'projetos',
            'dataInicio',
            'dataFim',
            'projetoId'
        ));
    }

    /**
     * Exportar dados para Excel
     */
    public function exportarExcel(Request $request)
    {
        $tipo = $request->get('tipo', 'projetos');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));

        switch ($tipo) {
            case 'projetos':
                return $this->exportarProjetosExcel();
            case 'atividades':
                return $this->exportarAtividadesExcel($dataInicio, $dataFim);
            case 'equipe':
                return $this->exportarEquipeExcel($dataInicio, $dataFim);
            case 'materiais':
                return $this->exportarMateriaisExcel($dataInicio, $dataFim);
            default:
                return redirect()->back()->with('error', 'Tipo de exportação inválido');
        }
    }

    /**
     * Sistema de alertas e notificações
     */
    public function alertas()
    {
        $alertas = $this->getAlertas();
        return view('diario-obras.alertas.index', compact('alertas'));
    }

    /**
     * Configurações do sistema
     */
    public function configuracoes()
    {
        return view('diario-obras.configuracoes.index');
    }

    /**
     * Métodos privados para estatísticas
     */
    private function getDashboardStats()
    {
        try {
            return [
                // Estatísticas originais
                'projetos_ativos' => Projeto::count(),
                'projetos_concluidos' => Projeto::where('status', 'concluido')->count(),
                'atividades_hoje' => AtividadeObra::whereDate('data_atividade', today())->count(),
                'atividades_semana' => AtividadeObra::whereBetween('data_atividade', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'funcionarios_presentes' => EquipeObra::whereDate('data_trabalho', today())
                    ->where('presente', true)->count(),
                'materiais_recebidos_hoje' => MaterialObra::whereDate('data_movimento', today())
                    ->where('tipo_movimento', 'entrada')->count(),
                'valor_total_projetos' => Projeto::sum('valor_total') ?? 0,
                'valor_materiais_mes' => MaterialObra::whereMonth('data_movimento', now()->month)
                    ->whereYear('data_movimento', now()->year)
                    ->where('tipo_movimento', 'entrada')
                    ->sum('valor_total') ?? 0,

                // Novas estatísticas baseadas nas imagens
                'relatorios_total' => AtividadeObra::count(),
                'relatorios_mes' => AtividadeObra::whereMonth('data_atividade', now()->month)
                    ->whereYear('data_atividade', now()->year)
                    ->count(),
                'atividades_total' => AtividadeObra::count(),
                'ocorrencias_total' => AtividadeObra::whereNotNull('problemas_encontrados')->count(),
                'ocorrencias_pendentes' => AtividadeObra::whereNotNull('problemas_encontrados')
                    ->whereNull('solucoes_aplicadas')
                    ->count(),
                'comentarios_total' => AtividadeObra::whereNotNull('observacoes')->count(),
                'comentarios_semana' => AtividadeObra::whereNotNull('observacoes')
                    ->whereBetween('data_atividade', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'fotos_total' => FotoObra::count(),
                'fotos_hoje' => FotoObra::whereDate('data_foto', today())->count(),
                'videos_total' => FotoObra::where('tipo_arquivo', 'video')->count(),
                'videos_mes' => FotoObra::where('tipo_arquivo', 'video')
                    ->whereMonth('data_foto', now()->month)
                    ->whereYear('data_foto', now()->year)
                    ->count(),
            ];
        } catch (\Exception $e) {
            return [
                'projetos_ativos' => 0,
                'projetos_concluidos' => 0,
                'atividades_hoje' => 0,
                'atividades_semana' => 0,
                'funcionarios_presentes' => 0,
                'materiais_recebidos_hoje' => 0,
                'valor_total_projetos' => 0,
                'valor_materiais_mes' => 0,
                'relatorios_total' => 0,
                'relatorios_mes' => 0,
                'atividades_total' => 0,
                'ocorrencias_total' => 0,
                'ocorrencias_pendentes' => 0,
                'comentarios_total' => 0,
                'comentarios_semana' => 0,
                'fotos_total' => 0,
                'fotos_hoje' => 0,
                'videos_total' => 0,
                'videos_mes' => 0,
            ];
        }
    }

    private function getProjetosRecentes()
    {
        try {
            return Projeto::with(['responsavel'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getAtividadesHoje()
    {
        try {
            return AtividadeObra::with(['projeto', 'responsavel'])
                ->whereDate('data_atividade', today())
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getAlertas()
    {
        try {
            $alertas = [];

            // Projetos atrasados
            $projetosAtrasados = Projeto::where('data_fim_prevista', '<', now())
                ->whereIn('status', ['planejamento', 'em_andamento'])
                ->get();

            foreach ($projetosAtrasados as $projeto) {
                $alertas[] = [
                    'tipo' => 'warning',
                    'icone' => 'fas fa-exclamation-triangle',
                    'titulo' => 'Projeto Atrasado',
                    'mensagem' => "O projeto '{$projeto->nome}' está atrasado em " . now()->diffInDays($projeto->data_fim_prevista) . " dias",
                    'acao' => route('diario-obras.projetos.show', $projeto)
                ];
            }

            // Atividades sem responsável
            $atividadesSemResponsavel = AtividadeObra::whereNull('responsavel_id')
                ->where('status', '!=', 'concluido')
                ->count();

            if ($atividadesSemResponsavel > 0) {
                $alertas[] = [
                    'tipo' => 'info',
                    'icone' => 'fas fa-info-circle',
                    'titulo' => 'Atividades Sem Responsável',
                    'mensagem' => "Existem {$atividadesSemResponsavel} atividades sem responsável definido",
                    'acao' => route('diario-obras.atividades.index')
                ];
            }

            return $alertas;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getDadosGraficos()
    {
        try {
            return [
                'projetos_por_status' => Projeto::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->get(),
                'atividades_por_mes' => AtividadeObra::select(
                        DB::raw('MONTH(data_atividade) as mes'),
                        DB::raw('count(*) as total')
                    )
                    ->whereYear('data_atividade', now()->year)
                    ->groupBy('mes')
                    ->get(),
                'custos_por_mes' => MaterialObra::select(
                        DB::raw('MONTH(data_movimento) as mes'),
                        DB::raw('SUM(valor_total) as total')
                    )
                    ->whereYear('data_movimento', now()->year)
                    ->where('tipo_movimento', 'entrada')
                    ->groupBy('mes')
                    ->get()
            ];
        } catch (\Exception $e) {
            return [
                'projetos_por_status' => collect(),
                'atividades_por_mes' => collect(),
                'custos_por_mes' => collect()
            ];
        }
    }

    private function getEstatisticasProjeto(Projeto $projeto, $dataInicio, $dataFim)
    {
        $atividades = $projeto->atividades()
            ->whereBetween('data_atividade', [$dataInicio, $dataFim])
            ->get();

        $equipe = $projeto->equipe()
            ->whereBetween('data_trabalho', [$dataInicio, $dataFim])
            ->get();

        $materiais = $projeto->materiais()
            ->whereBetween('data_movimento', [$dataInicio, $dataFim])
            ->get();

        return [
            'total_atividades' => $atividades->count(),
            'atividades_concluidas' => $atividades->where('status', 'concluido')->count(),
            'total_horas_trabalhadas' => $equipe->sum('horas_trabalhadas'),
            'total_funcionarios' => $equipe->pluck('funcionario_id')->unique()->count(),
            'valor_materiais' => $materiais->sum('valor_total'),
            'total_fotos' => $projeto->fotos()
                ->whereBetween('data_foto', [$dataInicio, $dataFim])
                ->count()
        ];
    }

    private function gerarRelatorioPDF($relatorio)
    {
        // PDF temporariamente desabilitado - instalar barryvdh/laravel-dompdf
        return response()->json(['message' => 'Funcionalidade de PDF em desenvolvimento']);

        // $pdf = Pdf::loadView('diario-obras.relatorios.pdf.projeto', compact('relatorio'));
        // $pdf->setPaper('A4', 'portrait');
        // return $pdf->download('relatorio-projeto-' . $relatorio['projeto']->id . '.pdf');
    }

    private function exportarProjetosExcel()
    {
        // Implementar exportação para Excel
        return response()->json(['message' => 'Exportação de projetos em desenvolvimento']);
    }

    private function exportarAtividadesExcel($dataInicio, $dataFim)
    {
        // Implementar exportação para Excel
        return response()->json(['message' => 'Exportação de atividades em desenvolvimento']);
    }

    private function exportarEquipeExcel($dataInicio, $dataFim)
    {
        // Implementar exportação para Excel
        return response()->json(['message' => 'Exportação de equipe em desenvolvimento']);
    }

    private function exportarMateriaisExcel($dataInicio, $dataFim)
    {
        // Implementar exportação para Excel
        return response()->json(['message' => 'Exportação de materiais em desenvolvimento']);
    }
}