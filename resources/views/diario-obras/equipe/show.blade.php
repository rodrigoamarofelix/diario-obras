@extends('layouts.admin')

@section('title', 'Detalhes da Equipe - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-eye text-primary"></i>
                        Detalhes da Equipe
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.equipe.index') }}">Equipe</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users text-primary"></i>
                                {{ $equipe->pessoa->nome ?? ($equipe->funcionario->name ?? 'Funcionário') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('diario-obras.equipe.index') }}" class="btn btn-secondary btn-sm mr-2">
                                    <i class="fas fa-arrow-left"></i>
                                    Voltar
                                </a>
                                <a href="{{ route('diario-obras.equipe.edit', $equipe->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Funcionário:</strong></td>
                                            <td>{{ $equipe->pessoa->nome ?? ($equipe->funcionario->name ?? 'N/A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Projeto:</strong></td>
                                            <td>{{ $equipe->projeto->nome ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Data:</strong></td>
                                            <td>{{ $equipe->data_trabalho->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Função:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($equipe->funcao) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Presente:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $equipe->presente ? 'success' : 'danger' }}">
                                                    {{ $equipe->presente ? 'Sim' : 'Não' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr>
                                            <td><strong>Horário:</strong></td>
                                            <td>
                                                @if($equipe->hora_entrada && $equipe->hora_saida)
                                                    {{ $equipe->hora_entrada }} - {{ $equipe->hora_saida }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        @if($equipe->hora_saida_almoco || $equipe->hora_retorno_almoco)
                                        <tr>
                                            <td><strong>Almoço:</strong></td>
                                            <td>
                                                @if($equipe->hora_saida_almoco && $equipe->hora_retorno_almoco)
                                                    {{ $equipe->hora_saida_almoco }} - {{ $equipe->hora_retorno_almoco }}
                                                    <span class="badge badge-info ml-2">{{ ucfirst($equipe->tipo_almoco) }}</span>
                                                @elseif($equipe->hora_saida_almoco)
                                                    Saída: {{ $equipe->hora_saida_almoco }}
                                                @elseif($equipe->hora_retorno_almoco)
                                                    Retorno: {{ $equipe->hora_retorno_almoco }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Horas Trabalhadas:</strong></td>
                                            <td>
                                                @php
                                                    $horasManha = 0;
                                                    $horasTarde = 0;
                                                    $total = $equipe->horas_trabalhadas ?? 0;

                                                    if ($equipe->hora_entrada && $equipe->hora_saida) {
                                                        // Parse times
                                                        $entrada = is_string($equipe->hora_entrada) ? strtotime($equipe->hora_entrada) : strtotime($equipe->hora_entrada->format('H:i'));
                                                        $saida = is_string($equipe->hora_saida) ? strtotime($equipe->hora_saida) : strtotime($equipe->hora_saida->format('H:i'));
                                                        $entradaAlmoco = null;
                                                        $retornoAlmoco = null;
                                                        $meioDia = strtotime('12:00:00');

                                                        if ($equipe->hora_saida_almoco) {
                                                            $entradaAlmoco = is_string($equipe->hora_saida_almoco) ? strtotime($equipe->hora_saida_almoco) : strtotime($equipe->hora_saida_almoco->format('H:i'));
                                                        }
                                                        if ($equipe->hora_retorno_almoco) {
                                                            $retornoAlmoco = is_string($equipe->hora_retorno_almoco) ? strtotime($equipe->hora_retorno_almoco) : strtotime($equipe->hora_retorno_almoco->format('H:i'));
                                                        }

                                                        // Calcular horas da manhã
                                                        if ($entradaAlmoco) {
                                                            $horasManha = ($entradaAlmoco - $entrada) / 3600;
                                                        } else {
                                                            // Se não houver almoço, assumir meio-dia
                                                            $horasManha = ($meioDia - $entrada) / 3600;
                                                            if ($horasManha < 0) $horasManha = 0;
                                                        }

                                                        // Calcular horas da tarde
                                                        if ($retornoAlmoco) {
                                                            $horasTarde = ($saida - $retornoAlmoco) / 3600;
                                                        } else {
                                                            // Se não houver retorno almoço, usar entrada almoço ou meio-dia até saída
                                                            if ($entradaAlmoco) {
                                                                $horasTarde = ($saida - $entradaAlmoco) / 3600;
                                                            } else {
                                                                $horasTarde = ($saida - $meioDia) / 3600;
                                                                if ($horasTarde < 0) $horasTarde = 0;
                                                            }
                                                        }

                                                        // Se as horas calculadas não correspondem ao total, distribuir proporcionalmente
                                                        if ($equipe->horas_trabalhadas) {
                                                            $somaCalculada = $horasManha + $horasTarde;
                                                            if ($somaCalculada > 0) {
                                                                $fator = $total / $somaCalculada;
                                                                $horasManha = $horasManha * $fator;
                                                                $horasTarde = $horasTarde * $fator;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if($equipe->horas_trabalhadas)
                                                    <div style="line-height: 1.8;">
                                                        <div>
                                                            <i class="fas fa-sun text-warning"></i>
                                                            <strong>Manhã:</strong> {{ number_format($horasManha, 1) }}h
                                                        </div>
                                                        <div>
                                                            <i class="fas fa-moon text-info"></i>
                                                            <strong>Tarde:</strong> {{ number_format($horasTarde, 1) }}h
                                                        </div>
                                                        <div class="mt-2 pt-2" style="border-top: 2px solid #dee2e6;">
                                                            <i class="fas fa-clock text-primary"></i>
                                                            <strong>Total:</strong> {{ number_format($total, 1) }}h
                                                        </div>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Criado em:</strong></td>
                                            <td>{{ $equipe->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Atualizado em:</strong></td>
                                            <td>{{ $equipe->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($equipe->atividades_realizadas)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-tasks"></i> Atividades Realizadas</h5>
                                    <p class="text-muted">{{ $equipe->atividades_realizadas }}</p>
                                </div>
                            </div>
                            @endif

                            @if($equipe->observacoes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note"></i> Observações</h5>
                                    <p class="text-muted">{{ $equipe->observacoes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

