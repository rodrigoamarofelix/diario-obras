<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Relatório' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }

        .header h2 {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            color: #6c757d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-ativo {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-aprovado {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-rejeitado {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-pago {
            background-color: #d4edda;
            color: #155724;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 10px 20px;
            font-size: 10px;
            color: #6c757d;
        }

        .page-break {
            page-break-before: always;
        }

        .summary-box {
            background-color: #e7f3ff;
            border: 1px solid #007bff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-box h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 16px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .summary-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo ?? 'Relatório' }}</h1>
        <h2>{{ config("app.name") }}</h2>
    </div>

    @if(isset($filtros) && !empty(array_filter($filtros)))
    <div class="info-box">
        <h3><i class="fas fa-filter"></i> Filtros Aplicados</h3>
        @if($filtros['data_inicio'])
            <div class="info-row">
                <span class="info-label">Data Início:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($filtros['data_inicio'])->format('d/m/Y') }}</span>
            </div>
        @endif
        @if($filtros['data_fim'])
            <div class="info-row">
                <span class="info-label">Data Fim:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($filtros['data_fim'])->format('d/m/Y') }}</span>
            </div>
        @endif
        @if($filtros['status'])
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($filtros['status']) }}</span>
            </div>
        @endif
        @if($filtros['lotacao_id'])
            <div class="info-row">
                <span class="info-label">Lotação:</span>
                <span class="info-value">{{ \App\Models\Lotacao::find($filtros['lotacao_id'])->nome ?? 'N/A' }}</span>
            </div>
        @endif
    </div>
    @endif

    @yield('content')

    <div class="footer">
        <div style="float: left;">
            Gerado em: {{ $dataExportacao->format('d/m/Y H:i:s') }}
        </div>
        <div style="float: right;">
            Página <span class="page-number"></span>
        </div>
    </div>
</body>
</html>
