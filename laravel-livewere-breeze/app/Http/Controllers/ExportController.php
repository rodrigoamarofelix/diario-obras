<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\User;
use App\Models\Pessoa;
use App\Models\Lotacao;
use App\Models\Catalogo;
use App\Models\Auditoria;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Exibir página de exportação
     */
    public function index()
    {
        return view('exports.index');
    }

    /**
     * Exportar contratos para PDF
     */
    public function contratosPdf(Request $request)
    {
        $filtros = $this->getFiltros($request);
        $contratos = $this->getContratos($filtros);

        $pdf = Pdf::loadView('exports.pdf.contratos', [
            'contratos' => $contratos,
            'filtros' => $filtros,
            'dataExportacao' => now(),
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('SGC_Contratos_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar contratos para Excel
     */
    public function contratosExcel(Request $request)
    {
        $filtros = $this->getFiltros($request);
        $contratos = $this->getContratos($filtros);

        return Excel::download(new ContratosExport($contratos), 'SGC_Contratos_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Exportar medições para PDF
     */
    public function medicoesPdf(Request $request)
    {
        $filtros = $this->getFiltros($request);
        $medicoes = $this->getMedicoes($filtros);

        $pdf = Pdf::loadView('exports.pdf.medicoes', [
            'medicoes' => $medicoes,
            'filtros' => $filtros,
            'dataExportacao' => now(),
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('SGC_Medicoes_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar medições para Excel
     */
    public function medicoesExcel(Request $request)
    {
        $filtros = $this->getFiltros($request);
        $medicoes = $this->getMedicoes($filtros);

        return Excel::download(new MedicoesExport($medicoes), 'SGC_Medicoes_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Exportar pagamentos para PDF
     */
    public function pagamentosPdf(Request $request)
    {
        $filtros = $this->getFiltros($request);
        $pagamentos = $this->getPagamentos($filtros);

        $pdf = Pdf::loadView('exports.pdf.pagamentos', [
            'pagamentos' => $pagamentos,
            'filtros' => $filtros,
            'dataExportacao' => now(),
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('SGC_Pagamentos_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar pagamentos para Excel
     */
    public function pagamentosExcel(Request $request)
    {
        $filtros = $this->getFiltros($request);
        $pagamentos = $this->getPagamentos($filtros);

        return Excel::download(new PagamentosExport($pagamentos), 'SGC_Pagamentos_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Exportar relatório financeiro completo
     */
    public function relatorioFinanceiroPdf(Request $request)
    {
        $filtros = $this->getFiltros($request);

        $dados = [
            'contratos' => $this->getContratos($filtros),
            'medicoes' => $this->getMedicoes($filtros),
            'pagamentos' => $this->getPagamentos($filtros),
            'resumo' => $this->getResumoFinanceiro($filtros),
            'filtros' => $filtros,
            'dataExportacao' => now(),
        ];

        $pdf = Pdf::loadView('exports.pdf.relatorio-financeiro', $dados);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('SGC_Relatorio_Financeiro_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Obter filtros da requisição
     */
    private function getFiltros(Request $request)
    {
        return [
            'data_inicio' => $request->get('data_inicio'),
            'data_fim' => $request->get('data_fim'),
            'status' => $request->get('status'),
            'lotacao_id' => $request->get('lotacao_id'),
            'contrato_id' => $request->get('contrato_id'),
        ];
    }

    /**
     * Obter contratos com filtros
     */
    private function getContratos($filtros)
    {
        $query = Contrato::with(['gestor', 'fiscal']);

        if ($filtros['data_inicio']) {
            $query->where('data_inicio', '>=', $filtros['data_inicio']);
        }

        if ($filtros['data_fim']) {
            $query->where('data_fim', '<=', $filtros['data_fim']);
        }

        if ($filtros['status']) {
            $query->where('status', $filtros['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obter medições com filtros
     */
    private function getMedicoes($filtros)
    {
        $query = Medicao::with(['contrato', 'catalogo', 'lotacao', 'usuario']);

        if ($filtros['data_inicio']) {
            $query->where('data_medicao', '>=', $filtros['data_inicio']);
        }

        if ($filtros['data_fim']) {
            $query->where('data_medicao', '<=', $filtros['data_fim']);
        }

        if ($filtros['status']) {
            $query->where('status', $filtros['status']);
        }

        if ($filtros['contrato_id']) {
            $query->where('contrato_id', $filtros['contrato_id']);
        }

        if ($filtros['lotacao_id']) {
            $query->where('lotacao_id', $filtros['lotacao_id']);
        }

        return $query->orderBy('data_medicao', 'desc')->get();
    }

    /**
     * Obter pagamentos com filtros
     */
    private function getPagamentos($filtros)
    {
        $query = Pagamento::with(['medicao.contrato', 'medicao.catalogo', 'medicao.lotacao', 'usuario']);

        if ($filtros['data_inicio']) {
            $query->where('data_pagamento', '>=', $filtros['data_inicio']);
        }

        if ($filtros['data_fim']) {
            $query->where('data_pagamento', '<=', $filtros['data_fim']);
        }

        if ($filtros['status']) {
            $query->where('status', $filtros['status']);
        }

        if ($filtros['contrato_id']) {
            $query->whereHas('medicao', function($q) use ($filtros) {
                $q->where('contrato_id', $filtros['contrato_id']);
            });
        }

        if ($filtros['lotacao_id']) {
            $query->whereHas('medicao', function($q) use ($filtros) {
                $q->where('lotacao_id', $filtros['lotacao_id']);
            });
        }

        return $query->orderBy('data_pagamento', 'desc')->get();
    }

    /**
     * Obter resumo financeiro
     */
    private function getResumoFinanceiro($filtros)
    {
        $medicoesQuery = Medicao::query();
        $pagamentosQuery = Pagamento::query();

        if ($filtros['data_inicio']) {
            $medicoesQuery->where('data_medicao', '>=', $filtros['data_inicio']);
            $pagamentosQuery->where('data_pagamento', '>=', $filtros['data_inicio']);
        }

        if ($filtros['data_fim']) {
            $medicoesQuery->where('data_medicao', '<=', $filtros['data_fim']);
            $pagamentosQuery->where('data_pagamento', '<=', $filtros['data_fim']);
        }

        return [
            'total_medicoes' => $medicoesQuery->count(),
            'valor_total_medicoes' => $medicoesQuery->sum('valor_total'),
            'total_pagamentos' => $pagamentosQuery->count(),
            'valor_total_pagamentos' => $pagamentosQuery->sum('valor_pagamento'),
            'medicoes_por_status' => $medicoesQuery->selectRaw('status, count(*) as total, sum(valor_total) as valor')
                ->groupBy('status')
                ->get(),
            'pagamentos_por_status' => $pagamentosQuery->selectRaw('status, count(*) as total, sum(valor_pagamento) as valor')
                ->groupBy('status')
                ->get(),
        ];
    }
}

// Classes de Export para Excel

class ContratosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $contratos;

    public function __construct($contratos)
    {
        $this->contratos = $contratos;
    }

    public function collection()
    {
        return $this->contratos;
    }

    public function headings(): array
    {
        return [
            'Número',
            'Descrição',
            'Data Início',
            'Data Fim',
            'Status',
            'Gestor',
            'Fiscal',
            'Criado em',
        ];
    }

    public function map($contrato): array
    {
        return [
            $contrato->numero,
            $contrato->descricao,
            $contrato->data_inicio->format('d/m/Y'),
            $contrato->data_fim->format('d/m/Y'),
            ucfirst($contrato->status),
            $contrato->gestor->nome ?? 'N/A',
            $contrato->fiscal->nome ?? 'N/A',
            $contrato->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 12,
            'D' => 12,
            'E' => 10,
            'F' => 20,
            'G' => 20,
            'H' => 15,
        ];
    }
}

class MedicoesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $medicoes;

    public function __construct($medicoes)
    {
        $this->medicoes = $medicoes;
    }

    public function collection()
    {
        return $this->medicoes;
    }

    public function headings(): array
    {
        return [
            'Número',
            'Contrato',
            'Catálogo',
            'Lotação',
            'Data Medição',
            'Quantidade',
            'Valor Unitário',
            'Valor Total',
            'Status',
            'Usuário',
            'Criado em',
        ];
    }

    public function map($medicao): array
    {
        return [
            $medicao->numero_medicao,
            $medicao->contrato->numero ?? 'N/A',
            $medicao->catalogo->nome ?? 'N/A',
            $medicao->lotacao->nome ?? 'N/A',
            $medicao->data_medicao->format('d/m/Y'),
            $medicao->quantidade,
            'R$ ' . number_format($medicao->valor_unitario, 2, ',', '.'),
            'R$ ' . number_format($medicao->valor_total, 2, ',', '.'),
            ucfirst($medicao->status),
            $medicao->usuario->name ?? 'N/A',
            $medicao->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 12,
            'F' => 10,
            'G' => 12,
            'H' => 12,
            'I' => 10,
            'J' => 20,
            'K' => 15,
        ];
    }
}

class PagamentosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $pagamentos;

    public function __construct($pagamentos)
    {
        $this->pagamentos = $pagamentos;
    }

    public function collection()
    {
        return $this->pagamentos;
    }

    public function headings(): array
    {
        return [
            'Número',
            'Medição',
            'Contrato',
            'Data Pagamento',
            'Valor',
            'Status',
            'Documento Redmine',
            'Usuário',
            'Criado em',
        ];
    }

    public function map($pagamento): array
    {
        return [
            $pagamento->numero_pagamento,
            $pagamento->medicao->numero_medicao ?? 'N/A',
            $pagamento->medicao->contrato->numero ?? 'N/A',
            $pagamento->data_pagamento->format('d/m/Y'),
            'R$ ' . number_format($pagamento->valor_pagamento, 2, ',', '.'),
            ucfirst($pagamento->status),
            $pagamento->documento_redmine ?? 'N/A',
            $pagamento->usuario->name ?? 'N/A',
            $pagamento->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 15,
            'D' => 12,
            'E' => 12,
            'F' => 10,
            'G' => 15,
            'H' => 20,
            'I' => 15,
        ];
    }
}
