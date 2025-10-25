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
                                {{ $equipe->funcionario->name ?? 'Funcionário' }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('diario-obras.equipe.edit', $equipe) }}" class="btn btn-warning btn-sm">
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
                                            <td>{{ $equipe->funcionario->name ?? 'N/A' }}</td>
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
                                        <tr>
                                            <td><strong>Horas Trabalhadas:</strong></td>
                                            <td>
                                                @if($equipe->horas_trabalhadas)
                                                    {{ $equipe->horas_trabalhadas }} horas
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

