<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdvancedSearchComponent extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $searchType = 'all'; // all, contratos, medicoes, pagamentos, pessoas, usuarios
    public $filters = [
        'status' => '',
        'date_from' => '',
        'date_to' => '',
        'valor_min' => '',
        'valor_max' => '',
        'user_id' => '',
        'lotacao_id' => '',
    ];

    public $results = [];
    public $totalResults = 0;
    public $searchHistory = [];
    public $savedFilters = [];
    public $showFilters = false;
    public $perPage = 10;

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'searchType' => ['except' => 'all'],
        'filters' => ['except' => []],
    ];

    public function mount()
    {
        $this->loadSearchHistory();
        $this->loadSavedFilters();

        // Carregar parâmetros da URL
        if (request()->has('q')) {
            $this->searchTerm = request('q');
            $this->performSearch();
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSearchType()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedFilters()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function performSearch()
    {
        if (empty($this->searchTerm) && empty(array_filter($this->filters))) {
            $this->results = [];
            $this->totalResults = 0;
            return;
        }

        $this->addToHistory();

        switch ($this->searchType) {
            case 'contratos':
                $this->searchContratos();
                break;
            case 'medicoes':
                $this->searchMedicoes();
                break;
            case 'pagamentos':
                $this->searchPagamentos();
                break;
            case 'pessoas':
                $this->searchPessoas();
                break;
            case 'usuarios':
                $this->searchUsuarios();
                break;
            default:
                $this->searchAll();
                break;
        }
    }

    private function searchAll()
    {
        $allResults = collect();

        // Buscar contratos
        $contratos = $this->searchContratosQuery()->get();
        foreach ($contratos as $contrato) {
            $allResults->push([
                'type' => 'contrato',
                'title' => $contrato->numero,
                'description' => 'Contrato - ' . $contrato->descricao,
                'status' => $contrato->status,
                'date' => $contrato->data_inicio,
                'url' => route('contrato.show', $contrato->id),
                'data' => $contrato
            ]);
        }

        // Buscar medições
        $medicoes = $this->searchMedicoesQuery()->get();
        foreach ($medicoes as $medicao) {
            $allResults->push([
                'type' => 'medicao',
                'title' => $medicao->numero_medicao,
                'description' => 'Medição - ' . $medicao->contrato->numero,
                'status' => $medicao->status,
                'date' => $medicao->data_medicao,
                'url' => route('medicao.show', $medicao->id),
                'data' => $medicao
            ]);
        }

        // Buscar pagamentos
        $pagamentos = $this->searchPagamentosQuery()->get();
        foreach ($pagamentos as $pagamento) {
            $allResults->push([
                'type' => 'pagamento',
                'title' => $pagamento->numero_pagamento,
                'description' => 'Pagamento - ' . $pagamento->medicao->numero_medicao,
                'status' => $pagamento->status,
                'date' => $pagamento->data_pagamento,
                'url' => route('pagamento.show', $pagamento->id),
                'data' => $pagamento
            ]);
        }

        // Buscar pessoas
        $pessoas = $this->searchPessoasQuery()->get();
        foreach ($pessoas as $pessoa) {
            $allResults->push([
                'type' => 'pessoa',
                'title' => $pessoa->nome,
                'description' => 'Pessoa - CPF: ' . $pessoa->cpf,
                'status' => $pessoa->status,
                'date' => $pessoa->created_at,
                'url' => route('pessoa.show', $pessoa->id),
                'data' => $pessoa
            ]);
        }

        // Buscar usuários
        $usuarios = $this->searchUsuariosQuery()->get();
        foreach ($usuarios as $usuario) {
            $allResults->push([
                'type' => 'usuario',
                'title' => $usuario->name,
                'description' => 'Usuário - ' . $usuario->email,
                'status' => $usuario->approval_status,
                'date' => $usuario->created_at,
                'url' => route('users.show', $usuario->id),
                'data' => $usuario
            ]);
        }

        $this->totalResults = $allResults->count();
        $this->results = $allResults->sortByDesc('date')->take($this->perPage)->values()->toArray();
    }

    private function searchContratos()
    {
        $query = $this->searchContratosQuery();
        $this->totalResults = $query->count();
        $this->results = $query->paginate($this->perPage);
    }

    private function searchMedicoes()
    {
        $query = $this->searchMedicoesQuery();
        $this->totalResults = $query->count();
        $this->results = $query->paginate($this->perPage);
    }

    private function searchPagamentos()
    {
        $query = $this->searchPagamentosQuery();
        $this->totalResults = $query->count();
        $this->results = $query->paginate($this->perPage);
    }

    private function searchPessoas()
    {
        $query = $this->searchPessoasQuery();
        $this->totalResults = $query->count();
        $this->results = $query->paginate($this->perPage);
    }

    private function searchUsuarios()
    {
        $query = $this->searchUsuariosQuery();
        $this->totalResults = $query->count();
        $this->results = $query->paginate($this->perPage);
    }

    private function searchContratosQuery()
    {
        $query = Contrato::with(['gestor', 'fiscal']);

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('numero', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('descricao', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('data_inicio', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('data_inicio', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    private function searchMedicoesQuery()
    {
        $query = Medicao::with(['contrato', 'usuario']);

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('numero_medicao', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('observacoes', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('contrato', function($subQ) {
                      $subQ->where('numero', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('data_medicao', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('data_medicao', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['valor_min'])) {
            $query->where('valor_total', '>=', $this->filters['valor_min']);
        }

        if (!empty($this->filters['valor_max'])) {
            $query->where('valor_total', '<=', $this->filters['valor_max']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('usuario_id', $this->filters['user_id']);
        }

        return $query;
    }

    private function searchPagamentosQuery()
    {
        $query = Pagamento::with(['medicao.contrato', 'usuario']);

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('numero_pagamento', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('observacoes', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('medicao', function($subQ) {
                      $subQ->where('numero_medicao', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->where('data_pagamento', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->where('data_pagamento', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['valor_min'])) {
            $query->where('valor_pagamento', '>=', $this->filters['valor_min']);
        }

        if (!empty($this->filters['valor_max'])) {
            $query->where('valor_pagamento', '<=', $this->filters['valor_max']);
        }

        if (!empty($this->filters['user_id'])) {
            $query->where('usuario_id', $this->filters['user_id']);
        }

        return $query;
    }

    private function searchPessoasQuery()
    {
        $query = Pessoa::with('lotacao');

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('nome', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('cpf', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['lotacao_id'])) {
            $query->where('lotacao_id', $this->filters['lotacao_id']);
        }

        return $query;
    }

    private function searchUsuariosQuery()
    {
        $query = User::query();

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('approval_status', $this->filters['status']);
        }

        return $query;
    }

    public function clearFilters()
    {
        $this->filters = [
            'status' => '',
            'date_from' => '',
            'date_to' => '',
            'valor_min' => '',
            'valor_max' => '',
            'user_id' => '',
            'lotacao_id' => '',
        ];
        $this->performSearch();
    }

    public function saveFilter($name)
    {
        $this->savedFilters[$name] = $this->filters;
        session(['saved_filters' => $this->savedFilters]);
        session()->flash('success', 'Filtro salvo com sucesso!');
    }

    public function loadFilter($name)
    {
        if (isset($this->savedFilters[$name])) {
            $this->filters = $this->savedFilters[$name];
            $this->performSearch();
        }
    }

    public function deleteFilter($name)
    {
        unset($this->savedFilters[$name]);
        session(['saved_filters' => $this->savedFilters]);
        session()->flash('success', 'Filtro removido com sucesso!');
    }

    private function addToHistory()
    {
        $search = [
            'term' => $this->searchTerm,
            'type' => $this->searchType,
            'filters' => $this->filters,
            'timestamp' => now()
        ];

        $this->searchHistory = collect($this->searchHistory)
            ->prepend($search)
            ->take(10)
            ->toArray();

        session(['search_history' => $this->searchHistory]);
    }

    private function loadSearchHistory()
    {
        $this->searchHistory = session('search_history', []);
    }

    private function loadSavedFilters()
    {
        $this->savedFilters = session('saved_filters', []);
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function render()
    {
        return view('livewire.advanced-search-component');
    }
}
