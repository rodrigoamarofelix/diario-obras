@extends('layouts.admin')

@section('title', 'Equipe - Diário de Obras')

@section('styles')
<style>
    .table-sm td { vertical-align: middle; }
    .btn-xs i { font-size: 14px; }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-users text-warning"></i>
                        Equipe de Obra
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Equipe</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Ações -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('diario-obras.equipe.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus"></i>
                        Registrar Equipe
                    </a>
                </div>
            </div>

            <!-- Lista de Equipe -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list text-primary"></i>
                                Registros de Equipe
                            </h3>
                        </div>
                        <div class="card-body">
                            @if(count($equipeAgrupada) > 0)
                                @foreach($equipeAgrupada as $grupo)
                                    @php
                                        $primeiroRegistro = $grupo->first();
                                    @endphp
                                    <div class="card card-primary collapsed-card mb-3">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas fa-users"></i>
                                                {{ $primeiroRegistro->projeto->nome ?? 'Projeto' }} -
                                                {{ $primeiroRegistro->data_trabalho->format('d/m/Y') }}
                                                <span class="badge badge-info ml-2">{{ $grupo->count() }} pessoa(s)</span>
                                            </h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body" style="display: none;">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Funcionário</th>
                                                            <th>Função</th>
                                                            <th>Horário</th>
                                                            <th>Horas</th>
                                                            <th>Presente</th>
                                                            <th>Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($grupo as $registro)
                                                            <tr class="{{ $registro->trashed() ? 'table-secondary' : '' }}">
                                                                <td>
                                                                    @if($registro->pessoa)
                                                                        {{ $registro->pessoa->nome }}
                                                                        @if($registro->pessoa->funcao)
                                                                            <br><small class="text-muted">{{ $registro->pessoa->funcao->nome }}</small>
                                                                        @endif
                                                                    @elseif($registro->funcionario)
                                                                        {{ $registro->funcionario->name }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                    @if($registro->trashed())
                                                                        <br><span class="badge badge-secondary">Na Lixeira</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-info">
                                                                        {{ ucfirst($registro->funcao) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($registro->hora_entrada && $registro->hora_saida)
                                                                        {{ $registro->hora_entrada }} - {{ $registro->hora_saida }}
                                                                        @if($registro->tipo_almoco)
                                                                            <br><small class="text-muted">{{ $registro->tipo_almoco == 'integral' ? 'Almoço Integral' : 'Almoço Reduzido' }}</small>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">N/A</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($registro->horas_trabalhadas)
                                                                        {{ $registro->horas_trabalhadas }}h
                                                                    @else
                                                                        <span class="text-muted">N/A</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-{{ $registro->presente ? 'success' : 'danger' }}">
                                                                        {{ $registro->presente ? 'Sim' : 'Não' }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <a href="{{ route('diario-obras.equipe.show', $registro->id) }}" class="btn btn-xs btn-primary" title="Ver detalhes">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        @if(!$registro->trashed())
                                                                            <a href="{{ route('diario-obras.equipe.edit', $registro->id) }}" class="btn btn-xs btn-warning" title="Editar">
                                                                                <i class="fas fa-edit"></i>
                                                                            </a>
                                                                            <form action="{{ route('diario-obras.equipe.destroy', $registro->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este registro?')">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-xs btn-danger" title="Excluir">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <form action="{{ route('diario-obras.equipe.restore', $registro->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja restaurar este registro?')">
                                                                                @csrf
                                                                                @method('PATCH')
                                                                                <button type="submit" class="btn btn-xs btn-success" title="Restaurar">
                                                                                    <i class="fas fa-undo"></i>
                                                                                </button>
                                                                            </form>
                                                                            @if(auth()->user()->can('manage-users'))
                                                                                <form action="{{ route('diario-obras.equipe.force-delete', $registro->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('ATENÇÃO: Esta ação é irreversível! Tem certeza que deseja excluir permanentemente este registro?')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-xs btn-danger" title="Excluir Permanentemente">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            @endif
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Nenhum registro de equipe</h4>
                                    <p class="text-muted">Comece registrando a presença da equipe.</p>
                                    <a href="{{ route('diario-obras.equipe.create') }}" class="btn btn-warning">
                                        <i class="fas fa-plus"></i>
                                        Registrar Primeira Equipe
                                    </a>
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

