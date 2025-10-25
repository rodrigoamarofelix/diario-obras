<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\User;
use App\Models\Lotacao;
use App\Models\Auditoria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsComponent extends Component
{
    public $reportType = 'contratos';
    public $dateFrom;
    public $dateTo;
    public $status = '';
    public $userId = '';
    public $contractId = '';
    public $results = [];
    public $loading = false;
    public $showResults = false;

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function testClick()
    {
        // Teste super simples
        \Log::info('testClick executado!');
        $this->loading = true;
        $this->showResults = true;
        $this->results = ['test' => 'Funcionou!'];
        $this->loading = false;
    }

    public function generateReport()
    {
        $this->loading = true;
        $this->showResults = true;

        // Teste simples para verificar se o Livewire está funcionando
        $this->results = [
            'test' => 'Livewire funcionando!',
            'reportType' => $this->reportType,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'timestamp' => now()->format('d/m/Y H:i:s')
        ];

        session()->flash('success', 'Relatório de teste gerado com sucesso!');
        $this->loading = false;
    }

    private function generateContratosReport()
    {
        $query = Contrato::query();

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $this->results = $query->with(['gestor', 'fiscal'])->get();
    }

    private function generateMedicoesReport()
    {
        $query = Medicao::query();

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->contractId) {
            $query->where('contrato_id', $this->contractId);
        }

        $this->results = $query->with(['contrato', 'catalogo'])->get();
    }

    private function generatePagamentosReport()
    {
        $query = Pagamento::query();

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $this->results = $query->with(['medicao.contrato'])->get();
    }

    private function generateUsuariosReport()
    {
        $query = User::query();

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->status) {
            $query->where('approval_status', $this->status);
        }

        $this->results = $query->get();
    }

    private function generateAuditoriaReport()
    {
        $query = Auditoria::query();

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->userId) {
            $query->where('usuario_id', $this->userId);
        }

        $this->results = $query->with('usuario')->get();
    }

    private function generateFinanceiroReport()
    {
        // Relatório financeiro consolidado
        $this->results = [
            'total_medicoes' => Medicao::whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->sum('valor_total'),
            'total_pagamentos' => Pagamento::whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->sum('valor'),
            'pagamentos_pendentes' => Pagamento::where('status', 'pendente')->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->sum('valor'),
            'pagamentos_pagos' => Pagamento::where('status', 'pago')->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->sum('valor'),
            'contratos_ativos' => Contrato::where('status', 'ativo')->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])->count(),
        ];
    }

    public function exportToPDF()
    {
        // Implementar exportação para PDF
        session()->flash('success', 'Exportação para PDF será implementada em breve!');
    }

    public function exportToExcel()
    {
        // Implementar exportação para Excel
        session()->flash('success', 'Exportação para Excel será implementada em breve!');
    }

    public function getReportTitle()
    {
        $titles = [
            'contratos' => 'Relatório de Contratos',
            'medicoes' => 'Relatório de Medições',
            'pagamentos' => 'Relatório de Pagamentos',
            'usuarios' => 'Relatório de Usuários',
            'auditoria' => 'Relatório de Auditoria',
            'financeiro' => 'Relatório Financeiro'
        ];

        return $titles[$this->reportType] ?? 'Relatório';
    }

    public function getContractsProperty()
    {
        return Contrato::orderBy('numero')->get();
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.reports-component')
            ->layout('layouts.admin');
    }
}