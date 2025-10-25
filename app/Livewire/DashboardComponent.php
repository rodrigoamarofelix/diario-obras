<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\User;
use App\Models\Pessoa;
use App\Models\Lotacao;
use App\Services\CacheService;
use App\Services\LazyLoadingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    public $loading = true;
    public $periodo = '30'; // dias
    public $refreshInterval = 30; // segundos

    // Métricas principais
    public $totalContratos = 0;
    public $contratosAtivos = 0;
    public $contratosVencidos = 0;
    public $totalMedicoes = 0;
    public $medicoesPendentes = 0;
    public $totalPagamentos = 0;
    public $pagamentosPendentes = 0;
    public $valorTotalMedicoes = 0;
    public $valorTotalPagamentos = 0;
    public $totalUsuarios = 0;
    public $usuariosPendentes = 0;

    // Dados para gráficos
    public $contratosPorStatus = [];
    public $medicoesPorMes = [];
    public $pagamentosPorMes = [];
    public $usuariosPorMes = [];
    public $topContratos = [];
    public $atividadesRecentes = [];

    // Cache stats
    public $cacheStats = [];
    public $cacheWorking = false;

    public function mount()
    {
        $this->carregarDados();
    }

    public function carregarDados()
    {
        $this->loading = true;

        try {
            // Verificar se o cache está funcionando
            $this->cacheWorking = CacheService::isCacheWorking();

            if ($this->cacheWorking) {
                $this->carregarDadosComCache();
            } else {
                $this->carregarDadosDoBanco();
            }

            // Carregar estatísticas do cache
            $this->cacheStats = CacheService::getCacheStats();

            $this->loading = false;
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar dados do dashboard: ' . $e->getMessage());
            $this->carregarDadosDoBanco();
            $this->loading = false;
        }
    }

    private function carregarDadosComCache()
    {
        // Carregar estatísticas do cache
        $stats = CacheService::getDashboardStats($this->periodo);
        $this->totalContratos = $stats['contratos_total'];
        $this->contratosAtivos = $stats['contratos_ativos'];
        $this->totalMedicoes = $stats['medicoes_total'];
        $this->totalPagamentos = $stats['pagamentos_total'];
        $this->totalUsuarios = $stats['usuarios_total'];
        $this->valorTotalMedicoes = $stats['valor_total_medicoes'];
        $this->valorTotalPagamentos = $stats['valor_total_pagamentos'];

        // Carregar dados dos gráficos do cache
        $chartData = CacheService::getChartData($this->periodo);
        $this->contratosPorStatus = $chartData['contratos_por_status'];
        $this->medicoesPorMes = $chartData['medicoes_por_mes'];
        $this->pagamentosPorMes = $chartData['pagamentos_por_mes'];
        $this->usuariosPorMes = $chartData['usuarios_por_mes'];

        // Carregar dados que não estão em cache
        $this->calcularMetricasAdicionais();
        $this->carregarAtividadesRecentes();
        $this->carregarTopContratos();
    }

    private function carregarDadosDoBanco()
    {
        $this->calcularMetricasPrincipais();
        $this->calcularDadosGraficos();
        $this->carregarAtividadesRecentes();
        $this->carregarTopContratos();
    }

    private function calcularMetricasPrincipais()
    {
        // Contratos
        $this->totalContratos = Contrato::count();
        $this->contratosAtivos = Contrato::where('status', 'ativo')->count();
        $this->contratosVencidos = Contrato::where('status', 'vencido')->count();

        // Medições
        $this->totalMedicoes = Medicao::count();
        $this->medicoesPendentes = Medicao::where('status', 'pendente')->count();
        $this->valorTotalMedicoes = Medicao::sum('valor_total');

        // Pagamentos
        $this->totalPagamentos = Pagamento::count();
        $this->pagamentosPendentes = Pagamento::where('status', 'pendente')->count();
        $this->valorTotalPagamentos = Pagamento::sum('valor_pagamento');

        // Usuários
        $this->totalUsuarios = User::count();
        $this->usuariosPendentes = User::where('approval_status', 'pending')->count();
    }

    private function calcularDadosGraficos()
    {
        $dias = (int) $this->periodo;
        $dataInicio = Carbon::now()->subDays($dias);

        // Contratos por Status
        $this->contratosPorStatus = Contrato::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Medições por Mês (últimos 6 meses)
        $this->medicoesPorMes = Medicao::select(
                DB::raw('DatabaseHelper::formatDateForMonthGrouping()'),
                DB::raw('count(*) as total'),
                DB::raw('sum(valor_total) as valor')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->toArray();

        // Pagamentos por Mês
        $this->pagamentosPorMes = Pagamento::select(
                DB::raw('DatabaseHelper::formatDateForMonthGrouping()'),
                DB::raw('count(*) as total'),
                DB::raw('sum(valor_pagamento) as valor')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->toArray();

        // Usuários por Mês
        $this->usuariosPorMes = User::select(
                DB::raw('DatabaseHelper::formatDateForMonthGrouping()'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->toArray();
    }

    private function carregarAtividadesRecentes()
    {
        $this->atividadesRecentes = collect()
            ->merge(
                Contrato::with('gestor', 'fiscal')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($contrato) {
                        return [
                            'tipo' => 'contrato',
                            'titulo' => 'Novo contrato: ' . $contrato->numero,
                            'descricao' => $contrato->descricao,
                            'data' => $contrato->created_at,
                            'usuario' => $contrato->gestor->nome ?? 'N/A',
                            'icone' => 'file-contract',
                            'cor' => 'primary'
                        ];
                    })
            )
            ->merge(
                Medicao::with('contrato', 'usuario')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($medicao) {
                        return [
                            'tipo' => 'medicao',
                            'titulo' => 'Nova medição: ' . $medicao->numero_medicao,
                            'descricao' => 'Contrato: ' . ($medicao->contrato->numero ?? 'N/A'),
                            'data' => $medicao->created_at,
                            'usuario' => $medicao->usuario->name ?? 'N/A',
                            'icone' => 'ruler',
                            'cor' => 'success'
                        ];
                    })
            )
            ->merge(
                Pagamento::with('medicao.contrato', 'usuario')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($pagamento) {
                        return [
                            'tipo' => 'pagamento',
                            'titulo' => 'Novo pagamento: ' . $pagamento->numero_pagamento,
                            'descricao' => 'Valor: ' . $pagamento->valor_pagamento_formatado,
                            'data' => $pagamento->created_at,
                            'usuario' => $pagamento->usuario->name ?? 'N/A',
                            'icone' => 'money-bill-wave',
                            'cor' => 'warning'
                        ];
                    })
            )
            ->sortByDesc('data')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function carregarTopContratos()
    {
        $this->topContratos = Contrato::with(['gestor', 'fiscal'])
            ->withCount('medicoes')
            ->withSum('medicoes', 'valor_total')
            ->orderBy('medicoes_sum_valor_total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($contrato) {
                return [
                    'numero' => $contrato->numero,
                    'descricao' => $contrato->descricao,
                    'status' => $contrato->status,
                    'gestor' => $contrato->gestor->nome ?? 'N/A',
                    'total_medicoes' => $contrato->medicoes_count,
                    'valor_total' => $contrato->medicoes_sum_valor_total ?? 0,
                    'data_inicio' => $contrato->data_inicio,
                    'data_fim' => $contrato->data_fim
                ];
            })
            ->toArray();
    }

    public function atualizarPeriodo($periodo)
    {
        $this->periodo = $periodo;
        $this->carregarDados();
    }

    private function calcularMetricasAdicionais()
    {
        // Métricas que não estão em cache
        $this->contratosVencidos = Contrato::where('status', 'vencido')->count();
        $this->medicoesPendentes = Medicao::where('status', 'pendente')->count();
        $this->pagamentosPendentes = Pagamento::where('status', 'pendente')->count();
        $this->usuariosPendentes = User::where('approval_status', 'pending')->count();
    }

    public function refresh()
    {
        // Limpar cache do dashboard
        if ($this->cacheWorking) {
            CacheService::clearDashboardCache();
        }

        $this->carregarDados();
        session()->flash('success', 'Dashboard atualizado com sucesso!');
    }

    public function clearCache()
    {
        CacheService::clearCache();
        $this->carregarDados();
        session()->flash('success', 'Cache limpo com sucesso!');
    }

    public function getStatusColor($status)
    {
        return match($status) {
            'ativo' => 'success',
            'inativo' => 'secondary',
            'vencido' => 'danger',
            'suspenso' => 'warning',
            default => 'primary'
        };
    }

    public function getStatusName($status)
    {
        return match($status) {
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'vencido' => 'Vencido',
            'suspenso' => 'Suspenso',
            default => ucfirst($status)
        };
    }

    public function render()
    {
        return view('livewire.dashboard-component');
    }
}

