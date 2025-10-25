<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contrato;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\User;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $reportType = $request->input('reportType', 'contratos');
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        $status = $request->input('status');

        $results = [];

        switch ($reportType) {
            case 'contratos':
                $query = Contrato::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                if ($status) $query->where('status', $status);
                $results = $query->with(['gestor', 'fiscal'])->get();
                break;

            case 'medicoes':
                $query = Medicao::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                $results = $query->with(['contrato', 'catalogo'])->get();
                break;

            case 'pagamentos':
                $query = Pagamento::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                if ($status) $query->where('status', $status);
                $results = $query->with(['medicao.contrato'])->get();
                break;

            case 'usuarios':
                $query = User::query();
                if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                if ($dateTo) $query->where('created_at', '<=', $dateTo);
                if ($status) $query->where('approval_status', $status);
                $results = $query->get();
                break;
        }

        return view('reports.index', compact('results', 'reportType', 'dateFrom', 'dateTo', 'status'));
    }
}