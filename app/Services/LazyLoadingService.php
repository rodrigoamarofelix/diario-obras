<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LazyLoadingService
{
    /**
     * Carregamento preguiçoso de contratos com paginação
     */
    public static function getContratosLazy($page = 1, $perPage = 20, $filters = [])
    {
        $cacheKey = 'contratos_lazy_' . md5(serialize($filters)) . '_page_' . $page;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($page, $perPage, $filters) { // 5 minutos
            $query = \App\Models\Contrato::query();

            // Aplicar filtros
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['search']) && $filters['search']) {
                $query->where(function($q) use ($filters) {
                    $q->where('numero_contrato', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('nome_contratado', 'like', '%' . $filters['search'] . '%');
                });
            }

            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->orderBy('created_at', 'desc')
                          ->get();

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        });
    }

    /**
     * Carregamento preguiçoso de medições
     */
    public static function getMedicoesLazy($page = 1, $perPage = 20, $filters = [])
    {
        $cacheKey = 'medicoes_lazy_' . md5(serialize($filters)) . '_page_' . $page;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($page, $perPage, $filters) {
            $query = \App\Models\Medicao::with('contrato');

            if (isset($filters['contrato_id']) && $filters['contrato_id']) {
                $query->where('contrato_id', $filters['contrato_id']);
            }

            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->orderBy('created_at', 'desc')
                          ->get();

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        });
    }

    /**
     * Carregamento preguiçoso de pagamentos
     */
    public static function getPagamentosLazy($page = 1, $perPage = 20, $filters = [])
    {
        $cacheKey = 'pagamentos_lazy_' . md5(serialize($filters)) . '_page_' . $page;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($page, $perPage, $filters) {
            $query = \App\Models\Pagamento::with('contrato');

            if (isset($filters['contrato_id']) && $filters['contrato_id']) {
                $query->where('contrato_id', $filters['contrato_id']);
            }

            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->orderBy('created_at', 'desc')
                          ->get();

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        });
    }

    /**
     * Carregamento preguiçoso de workflow
     */
    public static function getWorkflowLazy($page = 1, $perPage = 20, $filters = [])
    {
        $cacheKey = 'workflow_lazy_' . md5(serialize($filters)) . '_page_' . $page;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 180, function() use ($page, $perPage, $filters) { // 3 minutos
            $query = \App\Models\WorkflowAprovacao::with(['model', 'solicitante', 'aprovador']);

            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['tipo']) && $filters['tipo']) {
                $query->where('tipo', $filters['tipo']);
            }

            if (isset($filters['urgente']) && $filters['urgente'] !== '') {
                $query->where('urgente', $filters['urgente'] === 'sim');
            }

            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->orderBy('urgente', 'desc')
                          ->orderBy('prazo_aprovacao', 'asc')
                          ->get();

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        });
    }

    /**
     * Carregamento preguiçoso de notificações
     */
    public static function getNotificationsLazy($userId, $page = 1, $perPage = 10)
    {
        $cacheKey = "notifications_lazy_user_{$userId}_page_{$page}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function() use ($userId, $page, $perPage) { // 1 minuto
            $query = \App\Models\Notification::where('user_id', $userId)
                ->where('read_at', null);

            $total = $query->count();
            $items = $query->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->orderBy('created_at', 'desc')
                          ->get();

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        });
    }

    /**
     * Carregamento preguiçoso de busca avançada
     */
    public static function getAdvancedSearchLazy($searchData, $page = 1, $perPage = 20)
    {
        $cacheKey = 'advanced_search_' . md5(serialize($searchData)) . '_page_' . $page;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($searchData, $page, $perPage) {
            $results = [];

            // Buscar contratos
            if (isset($searchData['tipo']) && in_array('contrato', $searchData['tipo'])) {
                $contratosQuery = \App\Models\Contrato::query();

                if (isset($searchData['numero_contrato']) && $searchData['numero_contrato']) {
                    $contratosQuery->where('numero_contrato', 'like', '%' . $searchData['numero_contrato'] . '%');
                }

                if (isset($searchData['nome_contratado']) && $searchData['nome_contratado']) {
                    $contratosQuery->where('nome_contratado', 'like', '%' . $searchData['nome_contratado'] . '%');
                }

                $contratos = $contratosQuery->take($perPage)->get();
                foreach ($contratos as $contrato) {
                    $results[] = [
                        'type' => 'contrato',
                        'id' => $contrato->id,
                        'title' => $contrato->numero_contrato,
                        'subtitle' => $contrato->nome_contratado,
                        'created_at' => $contrato->created_at,
                        'url' => route('contrato.show', $contrato->id)
                    ];
                }
            }

            // Buscar medições
            if (isset($searchData['tipo']) && in_array('medicao', $searchData['tipo'])) {
                $medicoesQuery = \App\Models\Medicao::with('contrato');

                if (isset($searchData['numero_medicao']) && $searchData['numero_medicao']) {
                    $medicoesQuery->where('numero_medicao', 'like', '%' . $searchData['numero_medicao'] . '%');
                }

                $medicoes = $medicoesQuery->take($perPage)->get();
                foreach ($medicoes as $medicao) {
                    $results[] = [
                        'type' => 'medicao',
                        'id' => $medicao->id,
                        'title' => $medicao->numero_medicao,
                        'subtitle' => $medicao->contrato->nome_contratado ?? 'Sem contrato',
                        'created_at' => $medicao->created_at,
                        'url' => route('medicao.show', $medicao->id)
                    ];
                }
            }

            // Buscar pagamentos
            if (isset($searchData['tipo']) && in_array('pagamento', $searchData['tipo'])) {
                $pagamentosQuery = \App\Models\Pagamento::with('contrato');

                if (isset($searchData['numero_pagamento']) && $searchData['numero_pagamento']) {
                    $pagamentosQuery->where('numero_pagamento', 'like', '%' . $searchData['numero_pagamento'] . '%');
                }

                $pagamentos = $pagamentosQuery->take($perPage)->get();
                foreach ($pagamentos as $pagamento) {
                    $results[] = [
                        'type' => 'pagamento',
                        'id' => $pagamento->id,
                        'title' => $pagamento->numero_pagamento,
                        'subtitle' => $pagamento->contrato->nome_contratado ?? 'Sem contrato',
                        'created_at' => $pagamento->created_at,
                        'url' => route('pagamento.show', $pagamento->id)
                    ];
                }
            }

            // Ordenar por data de criação
            usort($results, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return array_slice($results, ($page - 1) * $perPage, $perPage);
        });
    }

    /**
     * Limpar cache de lazy loading
     */
    public static function clearLazyCache($type = null)
    {
        $patterns = [
            'contratos_lazy_',
            'medicoes_lazy_',
            'pagamentos_lazy_',
            'workflow_lazy_',
            'notifications_lazy_',
            'advanced_search_'
        ];

        if ($type) {
            $patterns = [$type . '_lazy_'];
        }

        foreach ($patterns as $pattern) {
            \App\Services\CacheService::clearCache($pattern);
        }

        return true;
    }
}



