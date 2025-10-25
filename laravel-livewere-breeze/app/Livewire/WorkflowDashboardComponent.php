<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkflowAprovacao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WorkflowDashboardComponent extends Component
{
    use WithPagination;

    public $stats = [];
    public $itensUrgentes = [];
    public $itensVencidos = [];
    public $filtroStatus = '';
    public $filtroTipo = '';
    public $filtroUrgente = '';

    public function mount()
    {
        $this->carregarDados();

        // Debug: Mostrar usuário logado
        $user = Auth::user();
        session()->flash('info', 'Usuário logado: ' . $user->name . ' (ID: ' . $user->id . ', Profile: ' . $user->profile . ')');
    }

    public function carregarDados()
    {
        $user = Auth::user();

        // Estatísticas
        $this->stats = [
            'pendentes' => WorkflowAprovacao::pendentes()->count(),
            'em_analise' => WorkflowAprovacao::emAnalise()->count(),
            'aprovados_hoje' => WorkflowAprovacao::aprovados()
                ->whereDate('aprovado_em', today())->count(),
            'urgentes' => WorkflowAprovacao::urgentes()
                ->whereIn('status', ['pendente', 'em_analise'])->count(),
            'vencidos' => WorkflowAprovacao::vencidos()->count(),
        ];

        // Itens urgentes
        $this->itensUrgentes = WorkflowAprovacao::urgentes()
            ->whereIn('status', ['pendente', 'em_analise'])
            ->with(['model', 'solicitante', 'aprovador'])
            ->orderBy('prazo_aprovacao', 'asc')
            ->limit(5)
            ->get();

        // Itens vencidos
        $this->itensVencidos = WorkflowAprovacao::vencidos()
            ->with(['model', 'solicitante', 'aprovador'])
            ->orderBy('prazo_aprovacao', 'asc')
            ->limit(5)
            ->get();
    }

    public function updatedFiltroStatus()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo()
    {
        $this->resetPage();
    }

    public function updatedFiltroUrgente()
    {
        $this->resetPage();
    }

    public function aprovar($id)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        if (!$item->podeSerAprovadoPor(Auth::id())) {
            session()->flash('error', 'Você não tem permissão para aprovar este item.');
            return;
        }

        $item->aprovar(Auth::id());
        $this->carregarDados();
        session()->flash('success', 'Item aprovado com sucesso!');
    }

    public function rejeitar($id, $justificativa)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        if (!$item->podeSerRejeitadoPor(Auth::id())) {
            session()->flash('error', 'Você não tem permissão para rejeitar este item.');
            return;
        }

        if (empty($justificativa)) {
            session()->flash('error', 'Justificativa é obrigatória para rejeição.');
            return;
        }

        $item->rejeitar(Auth::id(), $justificativa);
        $this->carregarDados();
        session()->flash('success', 'Item rejeitado com sucesso!');
    }

    public function marcarEmAnalise($id)
    {
        $item = WorkflowAprovacao::findOrFail($id);

        if (!$item->podeSerAprovadoPor(Auth::id())) {
            session()->flash('error', 'Você não tem permissão para marcar este item como em análise.');
            return;
        }

        $item->marcarComoEmAnalise();
        $this->carregarDados();
        session()->flash('success', 'Item marcado como em análise!');
    }

    public function render()
    {
        $user = Auth::user();

        // Query para itens para aprovar (usando paginação do Livewire)
        $query = WorkflowAprovacao::where('aprovador_id', $user->id)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->with(['model', 'solicitante']);

        if ($this->filtroStatus) {
            $query->where('status', $this->filtroStatus);
        }

        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->filtroUrgente) {
            $query->where('urgente', $this->filtroUrgente === 'sim');
        }

        $itensParaAprovar = $query->orderBy('urgente', 'desc')
            ->orderBy('prazo_aprovacao', 'asc')
            ->paginate(10);

        return view('livewire.workflow-dashboard-component', [
            'itensParaAprovar' => $itensParaAprovar
        ]);
    }
}