<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CacheService
{
    /**
     * Cache de estatísticas do dashboard
     */
    public static function getDashboardStats($periodo = 30)
    {
        $cacheKey = "dashboard_stats_{$periodo}";

        return Cache::remember($cacheKey, 1800, function() use ($periodo) { // 30 minutos
            return [
                'contratos_total' => \App\Models\Contrato::count(),
                'contratos_ativos' => \App\Models\Contrato::where('status', 'ativo')->count(),
                'medicoes_total' => \App\Models\Medicao::count(),
                'pagamentos_total' => \App\Models\Pagamento::count(),
                'usuarios_total' => \App\Models\User::count(),
                'valor_total_contratos' => \App\Models\Contrato::sum('valor_total'),
                'valor_total_medicoes' => \App\Models\Medicao::sum('valor_total'),
                'valor_total_pagamentos' => \App\Models\Pagamento::sum('valor_pagamento'),
                'periodo' => $periodo,
                'cached_at' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Cache de dados dos gráficos
     */
    public static function getChartData($periodo = 30)
    {
        $cacheKey = "chart_data_{$periodo}";

        return Cache::remember($cacheKey, 1800, function() use ($periodo) { // 30 minutos
            $dataInicio = Carbon::now()->subDays($periodo);

            return [
                'contratos_por_status' => \App\Models\Contrato::selectRaw('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray(),

                'medicoes_por_mes' => \App\Models\Medicao::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as mes, COUNT(*) as total')
                    ->where('created_at', '>=', $dataInicio)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get()
                    ->toArray(),

                'pagamentos_por_mes' => \App\Models\Pagamento::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as mes, COUNT(*) as total')
                    ->where('created_at', '>=', $dataInicio)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get()
                    ->toArray(),

                'usuarios_por_mes' => \App\Models\User::selectRaw('TO_CHAR(created_at, \'YYYY-MM\') as mes, COUNT(*) as total')
                    ->where('created_at', '>=', $dataInicio)
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get()
                    ->toArray(),

                'periodo' => $periodo,
                'cached_at' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Cache de lista de usuários
     */
    public static function getUsersList()
    {
        return Cache::remember('users_list', 3600, function() { // 1 hora
            return \App\Models\User::select('id', 'name', 'email', 'profile')
                ->orderBy('name')
                ->get()
                ->toArray();
        });
    }

    /**
     * Cache de configurações do sistema
     */
    public static function getSystemConfig()
    {
        return Cache::remember('system_config', 7200, function() { // 2 horas
            return [
                'app_name' => config('app.name'),
                'app_version' => '1.0.0',
                'maintenance_mode' => false,
                'max_file_size' => '10MB',
                'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                'cached_at' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Cache de workflow stats
     */
    public static function getWorkflowStats()
    {
        return Cache::remember('workflow_stats', 900, function() { // 15 minutos
            return [
                'pendentes' => \App\Models\WorkflowAprovacao::where('status', 'pendente')->count(),
                'em_analise' => \App\Models\WorkflowAprovacao::where('status', 'em_analise')->count(),
                'aprovados_hoje' => \App\Models\WorkflowAprovacao::where('status', 'aprovado')
                    ->whereDate('aprovado_em', today())->count(),
                'urgentes' => \App\Models\WorkflowAprovacao::where('urgente', true)
                    ->whereIn('status', ['pendente', 'em_analise'])->count(),
                'vencidos' => \App\Models\WorkflowAprovacao::where('prazo_aprovacao', '<', now())
                    ->whereIn('status', ['pendente', 'em_analise'])->count(),
                'cached_at' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Limpar cache específico
     */
    public static function clearCache($pattern = null)
    {
        if ($pattern) {
            $keys = Redis::keys("*{$pattern}*");
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            Cache::flush();
        }

        return true;
    }

    /**
     * Limpar cache do dashboard
     */
    public static function clearDashboardCache()
    {
        self::clearCache('dashboard_stats');
        self::clearCache('chart_data');
        return true;
    }

    /**
     * Limpar cache do workflow
     */
    public static function clearWorkflowCache()
    {
        self::clearCache('workflow_stats');
        return true;
    }

    /**
     * Obter estatísticas do cache
     */
    public static function getCacheStats()
    {
        try {
            $info = Redis::info();
            return [
                'redis_version' => $info['redis_version'] ?? 'N/A',
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'total_commands_processed' => $info['total_commands_processed'] ?? 0,
                'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $info['keyspace_hits'] > 0 ?
                    round(($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses'])) * 100, 2) : 0,
                'cached_at' => now()->toDateTimeString()
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Redis não disponível',
                'message' => $e->getMessage(),
                'cached_at' => now()->toDateTimeString()
            ];
        }
    }

    /**
     * Verificar se o cache está funcionando
     */
    public static function isCacheWorking()
    {
        try {
            $testKey = 'cache_test_' . time();
            Cache::put($testKey, 'test_value', 60);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            return $value === 'test_value';
        } catch (\Exception $e) {
            return false;
        }
    }
}
